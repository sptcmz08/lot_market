<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryPhoto;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffOnSitePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_record_cash_payment_during_work_photo_upload(): void
    {
        Storage::fake('public');

        $staff = User::create([
            'name' => 'Staff',
            'username' => 'staff1',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $booking = Booking::create([
            'booking_code' => 'BKONSITE001',
            'use_date' => now()->format('Y-m-d'),
            'shop_name' => 'ร้านเงินสดหน้าร้าน',
            'customer_phone' => '0812345678',
            'total_price' => 300.00,
            'status' => 'confirmed',
        ]);

        $task = DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_type' => 'tent',
            'status' => 'started',
            'task_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($staff)->post(
            route('staff.bookings.photos', [$booking, $task]),
            [
                'photo_type' => 'after',
                'photos' => [UploadedFile::fake()->create('after.jpg', 100, 'image/jpeg')],
                'submit_after_upload' => '1',
                'on_site_payment_method' => 'cash',
            ]
        );

        $response->assertRedirect(route('staff.bookings.index'));

        $booking->refresh();
        $this->assertTrue((bool)$booking->collect_front_store);
        $this->assertNotNull($booking->front_store_collected_at);
    }

    public function test_staff_can_upload_payment_slip_during_work_photo_upload(): void
    {
        Storage::fake('public');

        $staff = User::create([
            'name' => 'Staff',
            'username' => 'staff2',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $booking = Booking::create([
            'booking_code' => 'BKONSITE002',
            'use_date' => now()->format('Y-m-d'),
            'shop_name' => 'ร้านสแกนจ่ายหน้าร้าน',
            'customer_phone' => '0898765432',
            'total_price' => 450.00,
            'status' => 'confirmed',
        ]);

        $task = DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_type' => 'tent',
            'status' => 'started',
            'task_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($staff)->post(
            route('staff.bookings.photos', [$booking, $task]),
            [
                'photo_type' => 'after',
                'photos' => [UploadedFile::fake()->create('after.jpg', 100, 'image/jpeg')],
                'submit_after_upload' => '1',
                'on_site_payment_method' => 'transfer',
                'on_site_payment_slip' => UploadedFile::fake()->create('slip.jpg', 100, 'image/jpeg'),
            ]
        );

        $response->assertRedirect(route('staff.bookings.index'));

        $booking->refresh();
        $this->assertNotNull($booking->payment_slip_path);
    }
}
