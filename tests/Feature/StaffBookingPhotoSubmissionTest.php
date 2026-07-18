<?php

namespace Tests\Feature;

use App\Models\Booking;
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

        $this->actingAs($staff)->get(route('staff.bookings.index'))
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
            ->assertSee('capture="environment"', false);

        $this->actingAs($staff)->post(route('staff.bookings.photos', $booking), [
            'photo_type' => 'after',
            'photos' => [$this->photo('one.png'), $this->photo('two.png')],
        ])->assertForbidden();

        $this->actingAs($staff)->post(route('staff.bookings.photos', $booking), [
            'photo_type' => 'lot_number',
            'camera_photo' => $this->photo('lot.png'),
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertCount(0, $task->photos()->where('photo_type', 'after')->get());
        $this->assertCount(1, $task->photos()->where('photo_type', 'lot_number')->get());

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
            ->assertSee('ต้องอนุมัติรูป LOT ก่อนจึงจะส่งรูปงานติดตั้งได้')
            ->assertSee(route('admin.bookings.lot_review.approve', $booking), false);
        $this->actingAs($admin)->post(route('admin.bookings.work_review.approve', $booking))
            ->assertForbidden();

        $this->actingAs($admin)->post(route('admin.bookings.lot_review.approve', $booking))
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('waiting', $task->fresh()->status);
        $this->assertSame('approved', $task->photos()->where('photo_type', 'lot_number')->value('ocr_status'));

        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))
            ->assertOk()
            ->assertSee('รูปเลข LOT อนุมัติแล้ว')
            ->assertSee('รูปงานติดตั้ง');

        $this->actingAs($staff)->post(route('staff.bookings.photos', $booking), [
            'photo_type' => 'after',
            'photos' => [$this->photo('one.png'), $this->photo('two.png')],
        ])->assertRedirect()->assertSessionHas('success');
        $this->assertCount(2, $task->photos()->where('photo_type', 'after')->get());

        $this->actingAs($staff)->post(route('staff.bookings.submit_work', $booking))
            ->assertRedirect(route('staff.bookings.index'))
            ->assertSessionHas('success');
        $this->assertSame('photo_uploaded', $task->fresh()->status);
        $this->actingAs($staff)->get(route('staff.bookings.camera', $booking))->assertForbidden();

        $this->actingAs($admin)->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('รูปงานรอตรวจ')
            ->assertSee('ตรวจรูปงาน');
        $this->actingAs($admin)->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSee('อนุมัติรูปงานติดตั้ง')
            ->assertSee(route('admin.bookings.work_review.approve', $booking), false);

        $this->actingAs($admin)->post(route('admin.bookings.work_review.approve', $booking))
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('completed', $task->fresh()->status);
        $this->assertSame('completed', $booking->fresh()->status);

        $paths = $task->photos()->pluck('image_path');
        $publicPage = $this->post(route('public.booking.check.submit'), ['search_query' => 'BKSTAFFPHOTO001']);
        $publicPage->assertOk();
        $paths->each(fn (string $path) => $publicPage->assertSee($path));
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
