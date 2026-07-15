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

class StaffMultipleAfterPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_upload_multiple_after_photos_and_keep_adding_more(): void
    {
        Storage::fake('public');

        $staff = User::create([
            'name' => 'พนักงานทดสอบหลายรูป',
            'username' => 'staff-multiple-photos',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);
        $booking = Booking::create([
            'booking_code' => 'BKMULTIPHOTO001',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบหลายรูป',
            'customer_phone' => '0812345678',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'status' => 'installing',
        ]);
        $task = DeliveryTask::create([
            'booking_id' => $booking->id,
            'staff_id' => $staff->id,
            'task_date' => $booking->use_date,
            'status' => 'started',
        ]);
        DeliveryPhoto::create([
            'delivery_task_id' => $task->id,
            'photo_type' => 'lot_number',
            'image_path' => 'delivery-photos/approved-lot.png',
            'uploaded_by' => $staff->id,
            'ocr_status' => 'approved',
        ]);

        $response = $this->actingAs($staff)->post(route('staff.tasks.upload_photo', $task), [
            'photo_type' => 'after',
            'photos' => [
                $this->fakePhoto('after-1.png')->size(25 * 1024),
                $this->fakePhoto('after-2.png'),
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'อัปโหลดรูปหลังติดตั้งสำเร็จ 2 รูป สามารถเพิ่มรูปหรือกดส่งงานได้');

        $afterPhotos = $task->photos()->where('photo_type', 'after')->get();
        $this->assertCount(2, $afterPhotos);
        $afterPhotos->each(fn (DeliveryPhoto $photo) => Storage::disk('public')->assertExists($photo->image_path));

        $page = $this->actingAs($staff)->get(route('staff.tasks.show', $task));

        $page->assertOk()
            ->assertSee('มีรูปหลังติดตั้งแล้ว 2 รูป')
            ->assertSee('เพิ่มรูปหลังติดตั้ง')
            ->assertSee('name="photos[]"', false)
            ->assertSee('multiple', false);
    }

    private function fakePhoto(string $name): UploadedFile
    {
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z1AAAAABJRU5ErkJggg=='
        );

        return UploadedFile::fake()->createWithContent($name, $png);
    }
}
