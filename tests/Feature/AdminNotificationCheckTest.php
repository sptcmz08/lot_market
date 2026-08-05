<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_check_notifications_endpoint(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $booking = Booking::create([
            'booking_code' => 'BK-20260805-0999',
            'shop_name' => 'ร้านทดสอบจองใหม่',
            'customer_name' => 'ลูกค้าทดสอบ',
            'customer_phone' => '0812345678',
            'use_date' => now()->format('Y-m-d'),
            'status' => 'pending_admin',
            'total_price' => 100.00,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('notifications.check'));

        $response->assertStatus(200)
            ->assertJson([
                'pending_bookings_count' => 1,
                'photo_review_count' => 0,
            ])
            ->assertJsonPath('latest_pending_booking.code', $booking->booking_code);
    }

    public function test_staff_can_check_notifications_endpoint(): void
    {
        $staff = User::create([
            'name' => 'Staff Test',
            'username' => 'stafftest',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $booking = Booking::create([
            'booking_code' => 'BK-20260805-0888',
            'shop_name' => 'ร้านทดสอบสตาฟ',
            'customer_name' => 'ลูกค้าสตาฟ',
            'customer_phone' => '0823456789',
            'use_date' => now()->format('Y-m-d'),
            'status' => 'confirmed',
            'total_price' => 150.00,
        ]);

        $response = $this->actingAs($staff)
            ->getJson(route('notifications.check'));

        $response->assertStatus(200)
            ->assertJson([
                'confirmed_bookings_count' => 1,
            ])
            ->assertJsonPath('latest_confirmed_booking.code', $booking->booking_code);
    }
}
