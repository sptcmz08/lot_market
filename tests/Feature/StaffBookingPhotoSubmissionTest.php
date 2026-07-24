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

    public function test_lot_must_be_approved_before_staff_can_upload_and_submit_work_photos(): void
    {
        Storage::fake('public');
        $staff = $this->user('staff-photo-flow', 'staff');
        $admin = $this->user('admin-photo-flow', 'admin');
        $booking = Booking::create([
            'booking_code' => 'BKSTAFFPHOTO001',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบส่งรูป',
            'customer_phone' => '0812345678',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'status' => 'confirmed',
        ]);
        $task = DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_date' => $booking->use_date,
            'status' => 'waiting',
        ]);

        $this->actingAs($staff)->get(route('staff.bookings.index', ['date' => $booking->use_date->format('Y-m-d')]))
            ->assertOk()
            ->assertSee('ร้านทดสอบส่งรูป')
            ->assertSee('กล้อง')
            ->assertSee('ส่ง')
            ->assertDontSee(route('admin.bookings.destroy', $booking), false);

        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))
            ->assertOk()
            ->assertSee('data-camera-trigger', false)
            ->assertSee('data-gallery-trigger', false)
            ->assertSee('navigator.mediaDevices.getUserMedia', false)
            ->assertSee('for="camera_lot_number"', false)
            ->assertSee('accept="image/*;capture=camera" capture="environment"', false)
            ->assertSee("form.requestSubmit()", false)
            ->assertSee('data-native-camera-trigger', false)
            ->assertSee('package=com.android.chrome', false)
            ->assertSee('direct_camera', false)
            ->assertDontSee('เปิดด้วย Chrome เพื่อถ่ายรูป')
            ->assertSee('ถ่ายรูปด้วยกล้องมือถือ');

        $this->actingAs($staff)->post(route('staff.bookings.photos', [$booking, $task]), [
            'photo_type' => 'after',
            'photos' => [$this->photo('one.png'), $this->photo('two.png')],
        ])->assertRedirect(route('staff.bookings.camera', $booking))->assertSessionHas('success');

        $this->actingAs($staff)->post(route('staff.bookings.photos', $booking), [
            'photo_type' => 'lot_number',
            'camera_photo' => $this->photo('lot.png'),
        ])->assertRedirect(route('staff.bookings.camera', $booking))->assertSessionHas('success');

        $this->assertCount(2, $task->photos()->where('photo_type', 'after')->get());
        $this->assertCount(1, $task->photos()->where('photo_type', 'lot_number')->get());
        $lotPhotoPath = $task->photos()->where('photo_type', 'lot_number')->value('image_path');
        $this->actingAs($staff)->get(route('staff.bookings.index', ['date' => $booking->use_date->format('Y-m-d')]))
            ->assertOk()
            ->assertSee(route('media.show', ['path' => $lotPhotoPath]), false)
            ->assertSee('รูปเลข LOT');

        $this->actingAs($staff)->post(route('staff.bookings.submit_lot', $booking))
            ->assertRedirect(route('staff.bookings.index'))
            ->assertSessionHas('success');
        $this->assertSame('waiting', $task->fresh()->status);
        $this->assertSame('submitted', $task->photos()->where('photo_type', 'lot_number')->value('ocr_status'));

        $this->actingAs($admin)->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('รูป LOT รอตรวจ')
            ->assertSee('ตรวจรูป LOT')
            ->assertSee(route('admin.bookings.show', $booking).'#installation-review', false);
        $this->actingAs($admin)->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSee('ภาพถ่ายและอนุมัติงานติดตั้ง')
            ->assertSee('อนุมัติรูป LOT')
            ->assertSee('ต้องอนุมัติรูป LOT ก่อนจึงจะตรวจสอบงานติดตั้งได้')
            ->assertSee(route('admin.bookings.lot_review.approve', $booking), false);
        $this->actingAs($admin)->post(route('admin.tasks.work_review.approve', $task))
            ->assertForbidden();

        $this->actingAs($admin)->post(route('admin.bookings.lot_review.approve', $booking))
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('waiting', $task->fresh()->status);
        $this->assertSame('approved', $task->photos()->where('photo_type', 'lot_number')->value('ocr_status'));

        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))
            ->assertOk()
            ->assertSee('รูปเลข LOT อนุมัติแล้ว');

        $this->actingAs($staff)->post(route('staff.bookings.submit_work', [$booking, $task]))
            ->assertRedirect(route('staff.bookings.index'))
            ->assertSessionHas('success');
        $this->assertSame('photo_uploaded', $task->fresh()->status);

        $this->actingAs($admin)->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('รูปงานรอตรวจ')
            ->assertSee('ตรวจรูปงาน');
        $this->actingAs($admin)->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSee('อนุมัติรูปงาน')
            ->assertSee(route('admin.tasks.work_review.approve', $task), false);

        $this->actingAs($admin)->post(route('admin.tasks.work_review.approve', $task))
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('completed', $task->fresh()->status);
        $this->assertSame('completed', $booking->fresh()->status);

        $paths = $task->photos()->pluck('image_path');
        $publicPage = $this->post(route('public.booking.check.submit'), ['search_query' => 'BKSTAFFPHOTO001']);
        $publicPage->assertOk();
        $paths->each(fn (string $path) => $publicPage->assertSee($path));
    }

    public function test_camera_and_booking_list_separate_tent_and_counter_photos(): void
    {
        $staff = $this->user('staff-split-photo', 'staff');
        $booking = Booking::create([
            'booking_code' => 'BKSTAFFPHOTO002',
            'use_date' => now()->toDateString(),
            'shop_name' => 'ร้านเต็นท์และเคาน์เตอร์',
            'customer_phone' => '0899999999',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'counter_size' => '1 ล็อค 70x75 cm.',
            'status' => 'confirmed',
        ]);
        $tentTask = DeliveryTask::create([
            'booking_id' => $booking->id,
            'staff_id' => $staff->id,
            'task_type' => DeliveryTask::TYPE_TENT,
            'task_date' => $booking->use_date,
            'status' => 'waiting',
        ]);
        $counterTask = DeliveryTask::create([
            'booking_id' => $booking->id,
            'staff_id' => $staff->id,
            'task_type' => DeliveryTask::TYPE_COUNTER,
            'task_date' => $booking->use_date,
            'status' => 'waiting',
        ]);

        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))
            ->assertOk()
            ->assertSee('Tent (เต็นท์)')
            ->assertSee('Counter (เคาน์เตอร์)')
            ->assertDontSee('ต้องให้ Admin อนุมัติรูปเลข LOT ก่อน');

        DeliveryPhoto::create([
            'delivery_task_id' => $tentTask->id,
            'photo_type' => 'lot_number',
            'image_path' => 'delivery-photos/lot.jpg',
            'ocr_status' => 'approved',
            'uploaded_by' => $staff->id,
        ]);
        DeliveryPhoto::create([
            'delivery_task_id' => $tentTask->id,
            'photo_type' => 'after',
            'image_path' => 'delivery-photos/tent.jpg',
            'uploaded_by' => $staff->id,
        ]);
        DeliveryPhoto::create([
            'delivery_task_id' => $counterTask->id,
            'photo_type' => 'after',
            'image_path' => 'delivery-photos/counter.jpg',
            'uploaded_by' => $staff->id,
        ]);

        $this->actingAs($staff)->get(route('staff.bookings.index', ['date' => $booking->use_date->format('Y-m-d')]))
            ->assertOk()
            ->assertSee('data-lightbox-alt="รูปเลข LOT"', false)
            ->assertSee('data-lightbox-alt="รูปงานเต็นท์"', false)
            ->assertSee('data-lightbox-alt="รูปงานเคาน์เตอร์"', false);
    }

    public function test_staff_can_delete_draft_photos_but_not_photos_already_sent_for_review(): void
    {
        Storage::fake('public');
        $staff = $this->user('staff-delete-photo', 'staff');
        $booking = Booking::create([
            'booking_code' => 'BKSTAFFPHOTO003',
            'use_date' => now()->toDateString(),
            'shop_name' => 'ร้านทดสอบลบรูป',
            'customer_phone' => '0888888888',
            'tent_size' => '2x2',
            'tent_color' => 'แดง',
            'status' => 'confirmed',
        ]);
        $task = DeliveryTask::create([
            'booking_id' => $booking->id,
            'staff_id' => $staff->id,
            'task_type' => DeliveryTask::TYPE_TENT,
            'task_date' => $booking->use_date,
            'status' => 'waiting',
        ]);

        $this->actingAs($staff)->post(route('staff.bookings.photos', $booking), [
            'photo_type' => 'lot_number',
            'camera_photo' => $this->photo('draft-lot.png'),
        ])->assertRedirect(route('staff.bookings.camera', $booking));

        $draftPhoto = $task->photos()->where('photo_type', 'lot_number')->firstOrFail();
        Storage::disk('public')->assertExists($draftPhoto->image_path);
        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))
            ->assertOk()
            ->assertSee(route('staff.bookings.photos.destroy', [$booking, $draftPhoto]), false)
            ->assertSee('aria-label="ลบรูปเลข LOT"', false);

        $this->actingAs($staff)->delete(route('staff.bookings.photos.destroy', [$booking, $draftPhoto]))
            ->assertRedirect(route('staff.bookings.camera', $booking))
            ->assertSessionHas('success', 'ลบรูปเรียบร้อยแล้ว');
        $this->assertDatabaseMissing('delivery_photos', ['id' => $draftPhoto->id]);
        Storage::disk('public')->assertMissing($draftPhoto->image_path);

        $this->actingAs($staff)->post(route('staff.bookings.photos', $booking), [
            'photo_type' => 'lot_number',
            'camera_photo' => $this->photo('submitted-lot.png'),
        ])->assertRedirect(route('staff.bookings.camera', $booking));
        $submittedPhoto = $task->photos()->where('photo_type', 'lot_number')->firstOrFail();

        $this->actingAs($staff)->post(route('staff.bookings.submit_lot', $booking))->assertRedirect();
        $this->actingAs($staff)->delete(route('staff.bookings.photos.destroy', [$booking, $submittedPhoto]))
            ->assertForbidden();
        $this->assertDatabaseHas('delivery_photos', ['id' => $submittedPhoto->id]);
        Storage::disk('public')->assertExists($submittedPhoto->image_path);

        $submittedPhoto->update(['ocr_status' => 'approved']);
        $this->actingAs($staff)->post(route('staff.bookings.photos', [$booking, $task]), [
            'photo_type' => 'after',
            'camera_photo' => $this->photo('draft-work.png'),
        ])->assertRedirect(route('staff.bookings.camera', $booking));
        $draftWorkPhoto = $task->photos()->where('photo_type', 'after')->firstOrFail();

        $this->actingAs($staff)->delete(route('staff.bookings.photos.destroy', [$booking, $draftWorkPhoto]))
            ->assertRedirect(route('staff.bookings.camera', $booking));
        $this->assertDatabaseMissing('delivery_photos', ['id' => $draftWorkPhoto->id]);
        Storage::disk('public')->assertMissing($draftWorkPhoto->image_path);
    }

    private function user(string $username, string $role): User
    {
        return User::create([
            'name' => $username,
            'username' => $username,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function photo(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z1AAAAABJRU5ErkJggg=='
        ));
    }
}
