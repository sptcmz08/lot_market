<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryPhoto;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffBookingPhotoSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_upload_and_submit_work_photos_without_lot_review(): void
    {
        Storage::fake('public');
        $staff = $this->user('staff-photo-flow', 'staff');
        $admin = $this->user('admin-photo-flow', 'admin');
        $booking = Booking::create([
            'booking_code' => 'BKSTAFFPHOTO001', 'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบส่งรูป', 'customer_phone' => '0812345678',
            'tent_size' => '2x2', 'tent_color' => 'ขาว', 'status' => 'confirmed',
        ]);
        $task = DeliveryTask::create([
            'booking_id' => $booking->id, 'task_date' => $booking->use_date, 'status' => 'waiting',
        ]);

        $this->actingAs($staff)->get(route('staff.bookings.index', ['date' => $booking->use_date->format('Y-m-d')]))
            ->assertOk()->assertSee('ร้านทดสอบส่งรูป')->assertSee('photo-open-action', false)
            ->assertDontSee('ส่ง LOT')->assertDontSee('icon-action', false);

        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))
            ->assertOk()->assertSee('data-gallery-trigger', false)->assertSee('for="camera_after_'.$task->id.'"', false)
            ->assertSee('accept="image/*" capture="environment"', false)->assertDontSee('camera_lot_number');

        $this->actingAs($staff)->post(route('staff.bookings.photos', [$booking, $task]), [
            'photo_type' => 'after', 'photos' => [$this->photo('one.png'), $this->photo('two.png')],
        ])->assertRedirect(route('staff.bookings.camera', $booking))->assertSessionHas('success');
        $this->assertCount(2, $task->photos()->where('photo_type', 'after')->get());

        $this->actingAs($staff)->post(route('staff.bookings.submit_work', [$booking, $task]))
            ->assertRedirect(route('staff.bookings.index'))->assertSessionHas('success');
        $this->assertSame('photo_uploaded', $task->fresh()->status);

        $this->actingAs($admin)->get(route('admin.bookings.index'))->assertOk()
            ->assertSee('รูปงานรอตรวจ')->assertSee('ตรวจรูปงาน');
        $this->actingAs($admin)->get(route('admin.bookings.show', $booking))->assertOk()
            ->assertSee('ตรวจและอนุมัติรูปงาน')->assertDontSee('อนุมัติรูป LOT')
            ->assertDontSee('ต้องอนุมัติรูป LOT ก่อน');
        $this->actingAs($admin)->post(route('admin.tasks.work_review.approve', $task))
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('completed', $task->fresh()->status);

        $paths = $task->photos()->pluck('image_path');
        $publicPage = $this->post(route('public.booking.check.submit'), ['search_query' => 'BKSTAFFPHOTO001']);
        $publicPage->assertOk();
        $paths->each(fn (string $path) => $publicPage->assertSee($path));
    }

    public function test_camera_and_booking_list_separate_tent_and_counter_photos(): void
    {
        $staff = $this->user('staff-split-photo', 'staff');
        $booking = Booking::create([
            'booking_code' => 'BKSTAFFPHOTO002', 'use_date' => now()->toDateString(),
            'shop_name' => 'ร้านเต็นท์และเคาน์เตอร์', 'customer_phone' => '0899999999',
            'tent_size' => '2x2', 'tent_color' => 'ขาว', 'counter_size' => '1 ล็อค 70x75 cm.', 'status' => 'confirmed',
        ]);
        $tentTask = DeliveryTask::create(['booking_id' => $booking->id, 'staff_id' => $staff->id, 'task_type' => DeliveryTask::TYPE_TENT, 'task_date' => $booking->use_date, 'status' => 'waiting']);
        $counterTask = DeliveryTask::create(['booking_id' => $booking->id, 'staff_id' => $staff->id, 'task_type' => DeliveryTask::TYPE_COUNTER, 'task_date' => $booking->use_date, 'status' => 'waiting']);

        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))->assertOk()
            ->assertSee('Tent (เต็นท์)')->assertSee('Counter (เคาน์เตอร์)')->assertDontSee('รูปเลข LOT');
        DeliveryPhoto::create(['delivery_task_id' => $tentTask->id, 'photo_type' => 'after', 'image_path' => 'delivery-photos/tent.jpg', 'uploaded_by' => $staff->id]);
        DeliveryPhoto::create(['delivery_task_id' => $counterTask->id, 'photo_type' => 'after', 'image_path' => 'delivery-photos/counter.jpg', 'uploaded_by' => $staff->id]);

        $this->actingAs($staff)->get(route('staff.bookings.index', ['date' => $booking->use_date->format('Y-m-d')]))
            ->assertOk()->assertSee('data-lightbox-alt="รูปงานเต็นท์"', false)
            ->assertSee('data-lightbox-alt="รูปงานเคาน์เตอร์"', false)->assertDontSee('รูปเลข LOT');
        $this->actingAs($staff)->get(route('staff.bookings.index', ['date' => $booking->use_date->format('Y-m-d'), 'equipment_type' => 'counter']))
            ->assertOk()->assertSee('เคาน์เตอร์')->assertDontSee('เต็นท์ (ขนาด)')->assertDontSee('<th>สี</th>', false);
    }

    public function test_staff_can_delete_draft_after_photos_but_not_photos_already_sent_for_review(): void
    {
        Storage::fake('public');
        $staff = $this->user('staff-delete-photo', 'staff');
        $booking = Booking::create(['booking_code' => 'BKSTAFFPHOTO003', 'use_date' => now()->toDateString(), 'shop_name' => 'ร้านทดสอบลบรูป', 'customer_phone' => '0888888888', 'tent_size' => '2x2', 'tent_color' => 'แดง', 'status' => 'confirmed']);
        $task = DeliveryTask::create(['booking_id' => $booking->id, 'staff_id' => $staff->id, 'task_type' => DeliveryTask::TYPE_TENT, 'task_date' => $booking->use_date, 'status' => 'waiting']);

        $this->actingAs($staff)->post(route('staff.bookings.photos', [$booking, $task]), ['photo_type' => 'after', 'camera_photo' => $this->photo('draft-work.png')])->assertRedirect();
        $draftPhoto = $task->photos()->where('photo_type', 'after')->firstOrFail();
        $this->actingAs($staff)->delete(route('staff.bookings.photos.destroy', [$booking, $draftPhoto]))->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseMissing('delivery_photos', ['id' => $draftPhoto->id]);

        $this->actingAs($staff)->post(route('staff.bookings.photos', [$booking, $task]), ['photo_type' => 'after', 'camera_photo' => $this->photo('sent-work.png')]);
        $sentPhoto = $task->photos()->where('photo_type', 'after')->firstOrFail();
        $this->actingAs($staff)->post(route('staff.bookings.submit_work', [$booking, $task]))->assertRedirect();
        $this->actingAs($staff)->delete(route('staff.bookings.photos.destroy', [$booking, $sentPhoto]))->assertForbidden();
        $this->assertDatabaseHas('delivery_photos', ['id' => $sentPhoto->id]);
    }

    private function user(string $username, string $role): User
    {
        return User::create(['name' => $username, 'username' => $username, 'password' => Hash::make('password'), 'role' => $role, 'is_active' => true]);
    }

    private function photo(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z1AAAAABJRU5ErkJggg=='));
    }
}
