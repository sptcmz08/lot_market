<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StaffBookingCurrentDateTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_staff_booking_list_defaults_to_the_current_use_date(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 22, 0, 30, 0, 'Asia/Bangkok'));

        $staff = $this->staff('current-date-staff');

        $this->booking('BKTODAY001', '2026-07-22', 'ร้านของวันนี้');
        $this->booking('BKOLD001', '2026-07-21', 'ร้านของเมื่อวาน');

        $response = $this->actingAs($staff)->get(route('staff.bookings.index'));

        $response->assertOk()
            ->assertSee('ร้านของวันนี้')
            ->assertDontSee('ร้านของเมื่อวาน')
            ->assertSee('value="2026-07-22"', false)
            ->assertSee('(วันปัจจุบัน)');
    }

    public function test_staff_can_explicitly_filter_another_use_date(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 22, 0, 30, 0, 'Asia/Bangkok'));

        $staff = $this->staff('other-date-staff');

        $this->booking('BKTODAY002', '2026-07-22', 'ร้านวันนี้อีกแห่ง');
        $this->booking('BKOLD002', '2026-07-21', 'ร้านย้อนหลัง');

        $response = $this->actingAs($staff)->get(route('staff.bookings.index', [
            'date' => '2026-07-21',
        ]));

        $response->assertOk()
            ->assertSee('ร้านย้อนหลัง')
            ->assertDontSee('ร้านวันนี้อีกแห่ง')
            ->assertSee('value="2026-07-21"', false)
            ->assertSee('กลับมาวันปัจจุบัน');
    }

    public function test_staff_can_filter_bookings_by_equipment_task_type(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 22, 12, 0, 0, 'Asia/Bangkok'));
        $staff = $this->staff('equipment-filter-staff');
        $tentBooking = $this->booking('BKTENT001', '2026-07-22', 'ร้านงานเต็นท์');
        $counterBooking = Booking::create([
            'booking_code' => 'BKCOUNTER001',
            'use_date' => '2026-07-22',
            'shop_name' => 'ร้านงานเคาน์เตอร์',
            'customer_phone' => '0823456789',
            'counter_size' => '1 ล็อค 70x75 cm. มีหลังคา',
            'status' => 'confirmed',
        ]);
        DeliveryTask::create([
            'booking_id' => $tentBooking->id,
            'task_type' => DeliveryTask::TYPE_TENT,
            'task_date' => '2026-07-22',
            'status' => 'waiting',
        ]);
        DeliveryTask::create([
            'booking_id' => $counterBooking->id,
            'task_type' => DeliveryTask::TYPE_COUNTER,
            'task_date' => '2026-07-22',
            'status' => 'waiting',
        ]);

        $this->actingAs($staff)->get(route('staff.bookings.index', ['equipment_type' => 'tent']))
            ->assertOk()
            ->assertSee('ร้านงานเต็นท์')
            ->assertDontSee('ร้านงานเคาน์เตอร์')
            ->assertSee('<option value="tent" selected>งานเต็นท์</option>', false);

        $this->actingAs($staff)->get(route('staff.bookings.index', ['equipment_type' => 'counter']))
            ->assertOk()
            ->assertSee('ร้านงานเคาน์เตอร์')
            ->assertDontSee('ร้านงานเต็นท์')
            ->assertSee('<option value="counter" selected>งานเคาน์เตอร์</option>', false);
    }

    private function booking(string $code, string $useDate, string $shopName): Booking
    {
        return Booking::create([
            'booking_code' => $code,
            'use_date' => $useDate,
            'shop_name' => $shopName,
            'customer_phone' => '0812345678',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'status' => 'confirmed',
        ]);
    }

    private function staff(string $username): User
    {
        return User::create([
            'name' => 'พนักงานทดสอบ',
            'username' => $username,
            'email' => $username.'@example.com',
            'phone' => '0899999999',
            'password' => 'password',
            'role' => 'staff',
            'is_active' => true,
        ]);
    }
}
