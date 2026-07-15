<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_front_store_collection_and_see_it_in_daily_summary(): void
    {
        $admin = $this->createAdmin();
        $useDate = now()->addDay()->toDateString();
        $booking = $this->createBooking($useDate, true);
        $lot = Lot::create([
            'lot_code' => 'GA12',
            'display_name' => 'GA12',
            'is_active' => true,
        ]);
        $booking->lots()->attach($lot);

        $response = $this->actingAs($admin)->post(
            route('admin.dashboard.front_store_collection', $booking),
            ['front_store_collected_amount' => '500.00']
        );

        $response->assertRedirect()
            ->assertSessionHas('success');

        $booking->refresh();
        $this->assertSame('500.00', $booking->front_store_collected_amount);
        $this->assertNotNull($booking->front_store_collected_at);
        $this->assertSame($admin->id, $booking->front_store_collected_by);

        $dashboard = $this->actingAs($admin)->get(route('admin.dashboard', ['date' => $useDate]));

        $dashboard->assertOk()
            ->assertSee('GA12')
            ->assertSee('1 LOT')
            ->assertSee('500.00 บาท')
            ->assertSee('เก็บแล้ว');
    }

    public function test_admin_cannot_record_collection_for_booking_without_front_store_option(): void
    {
        $admin = $this->createAdmin('admin-collection-blocked');
        $booking = $this->createBooking(now()->addDay()->toDateString(), false, 'BKCOLLECT002');

        $response = $this->actingAs($admin)->post(
            route('admin.dashboard.front_store_collection', $booking),
            ['front_store_collected_amount' => '250.00']
        );

        $response->assertRedirect()
            ->assertSessionHas('error', 'รายการนี้ไม่ได้เลือกเก็บเงินหน้าร้าน');
        $this->assertNull($booking->fresh()->front_store_collected_at);
    }

    private function createAdmin(string $username = 'admin-collection-test'): User
    {
        return User::create([
            'name' => 'แอดมินทดสอบยอด',
            'username' => $username,
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    private function createBooking(string $useDate, bool $collectFrontStore, string $code = 'BKCOLLECT001'): Booking
    {
        return Booking::create([
            'booking_code' => $code,
            'use_date' => $useDate,
            'shop_name' => 'ร้านเก็บเงินหน้าร้าน',
            'customer_phone' => '0812345678',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'collect_front_store' => $collectFrontStore,
            'status' => 'confirmed',
        ]);
    }
}
