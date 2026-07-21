<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SplitDeliveryTaskAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_staff_see_confirmed_bookings_without_assignment(): void
    {
        $admin = $this->createUser('admin-split', 'admin', 'ผู้ดูแล');
        $tentStaff = $this->createUser('tent-staff', 'staff', 'คนส่งเต็นท์');
        $counterStaff = $this->createUser('counter-staff', 'staff', 'คนส่งเคาน์เตอร์');
        $booking = Booking::create([
            'booking_code' => 'BKSPLIT001',
            'use_date' => now()->toDateString(),
            'shop_name' => 'ร้านแบ่งงาน',
            'customer_phone' => '0811111111',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'tent_quantity' => 3,
            'tent_items' => [
                ['size' => '2x2', 'color' => 'ขาว', 'quantity' => 3],
                ['size' => '3x4.5', 'color' => 'แดง', 'quantity' => 1],
            ],
            'counter_size' => '2 ล็อค 140x75 cm.',
            'counter_quantity' => 2,
            'collect_front_store' => true,
            'status' => 'pending_admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.confirm', $booking))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('delivery_tasks', ['booking_id' => $booking->id, 'task_type' => 'tent']);
        $this->assertDatabaseHas('delivery_tasks', ['booking_id' => $booking->id, 'task_type' => 'counter']);

        $this->assertDatabaseHas('delivery_tasks', [
            'booking_id' => $booking->id,
            'task_type' => 'tent',
            'staff_id' => null,
        ]);
        $this->assertDatabaseHas('delivery_tasks', [
            'booking_id' => $booking->id,
            'task_type' => 'counter',
            'staff_id' => null,
        ]);
        $this->assertSame('confirmed', $booking->fresh()->status);
        $this->assertFalse(Route::has('admin.bookings.assign'));
        $this->assertFalse(Route::has('admin.tasks.index'));
        $this->assertFalse(Route::has('admin.lot_photo_reviews.index'));

        $details = $this->actingAs($admin)->get(route('admin.bookings.show', $booking));
        $details->assertOk()
            ->assertSee('เต็นท์ 2x2 สีขาว จำนวน 3 หลัง')
            ->assertSee('เต็นท์ 3x4.5 สีแดง จำนวน 1 หลัง')
            ->assertSee('เคาน์เตอร์ 2 ล็อค 140x75 cm.')
            ->assertDontSee('มอบหมายพนักงาน')
            ->assertDontSee('บันทึกการแบ่งงาน');

        $tentTasks = $this->actingAs($tentStaff)->get(route('staff.tasks.index'));
        $tentTasks->assertRedirect(route('staff.bookings.index'));

        $bookingList = $this->actingAs($tentStaff)->get(route('staff.bookings.index'));
        $bookingList->assertOk()
            ->assertSee('ร้านแบ่งงาน')
            ->assertSee('เต็นท์')
            ->assertSee('เคาน์เตอร์')
            ->assertSee('อื่น ๆ')
            ->assertSee('2x2')
            ->assertSee('ขาว')
            ->assertSee('x3', false)
            ->assertSee('3x4.5')
            ->assertSee('แดง')
            ->assertSee('x1', false)
            ->assertSee('ยอดจองเต็นท์รวม')
            ->assertSee('4 หลัง')
            ->assertSee('2 ล็อค 140x75 cm.')
            ->assertSee('x2', false);

        $counterList = $this->actingAs($counterStaff)->get(route('staff.bookings.index'));
        $counterList->assertOk()->assertSee('ร้านแบ่งงาน');

        $dashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboard->assertOk()
            ->assertDontSee('งานจัดส่งพนักงาน')
            ->assertDontSee('ตรวจรูปเลขล็อต')
            ->assertDontSee('อนุมัติรูปส่งงาน');
    }

    public function test_booking_completes_only_after_every_split_task_is_completed(): void
    {
        $booking = Booking::create([
            'booking_code' => 'BKSPLIT002',
            'use_date' => now()->toDateString(),
            'shop_name' => 'ร้านรอครบทุกงาน',
            'customer_phone' => '0822222222',
            'tent_size' => '2x2',
            'counter_size' => '1 ล็อค 70x75 cm. มีหลังคา',
            'status' => 'confirmed',
        ]);
        $tentTask = DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_type' => 'tent',
            'task_date' => now()->toDateString(),
            'status' => 'completed',
        ]);
        $counterTask = DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_type' => 'counter',
            'task_date' => now()->toDateString(),
            'status' => 'waiting',
        ]);

        $this->assertSame('installing', $booking->refreshDeliveryStatus());
        $counterTask->update(['status' => 'completed']);
        $this->assertSame('completed', $booking->fresh()->refreshDeliveryStatus());

        $this->assertSame('completed', $tentTask->fresh()->status);
        $this->assertSame('completed', $booking->fresh()->status);
    }

    private function createUser(string $username, string $role, string $name): User
    {
        return User::create([
            'name' => $name,
            'username' => $username,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
