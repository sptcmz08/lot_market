<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryPhoto;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SplitDeliveryTaskAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_tent_counter_and_other_equipment_to_different_staff(): void
    {
        $admin = $this->createUser('admin-split', 'admin', 'ผู้ดูแล');
        $tentStaff = $this->createUser('tent-staff', 'staff', 'คนส่งเต็นท์');
        $counterStaff = $this->createUser('counter-staff', 'staff', 'คนส่งเคาน์เตอร์');
        $otherStaff = $this->createUser('other-staff', 'staff', 'คนส่งอุปกรณ์');
        $booking = Booking::create([
            'booking_code' => 'BKSPLIT001',
            'use_date' => now()->toDateString(),
            'shop_name' => 'ร้านแบ่งงาน',
            'customer_phone' => '0811111111',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'counter_size' => '2 ล็อค 140x75 cm.',
            'collect_front_store' => true,
            'status' => 'pending_admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.confirm', $booking))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('delivery_tasks', ['booking_id' => $booking->id, 'task_type' => 'tent']);
        $this->assertDatabaseHas('delivery_tasks', ['booking_id' => $booking->id, 'task_type' => 'counter']);

        $this->actingAs($admin)
            ->post(route('admin.bookings.assign', $booking), [
                'tent_staff_id' => $tentStaff->id,
                'counter_staff_id' => $counterStaff->id,
                'other_staff_id' => $otherStaff->id,
                'other_equipment_note' => 'ถุงทรายและเชือก',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('delivery_tasks', [
            'booking_id' => $booking->id,
            'task_type' => 'tent',
            'staff_id' => $tentStaff->id,
        ]);
        $this->assertDatabaseHas('delivery_tasks', [
            'booking_id' => $booking->id,
            'task_type' => 'counter',
            'staff_id' => $counterStaff->id,
        ]);
        $this->assertDatabaseHas('delivery_tasks', [
            'booking_id' => $booking->id,
            'task_type' => 'other',
            'staff_id' => $otherStaff->id,
            'equipment_note' => 'ถุงทรายและเชือก',
        ]);
        $this->assertSame('assigned', $booking->fresh()->status);

        $details = $this->actingAs($admin)->get(route('admin.bookings.show', $booking));
        $details->assertOk()
            ->assertSee('งานเต็นท์: 2x2 สีขาว')
            ->assertSee('งานเคาน์เตอร์: 2 ล็อค 140x75 cm.')
            ->assertSee('ถุงทรายและเชือก')
            ->assertSee('คนส่งเต็นท์')
            ->assertSee('คนส่งเคาน์เตอร์')
            ->assertSee('คนส่งอุปกรณ์');

        $tentTasks = $this->actingAs($tentStaff)->get(route('staff.tasks.index'));
        $tentTasks->assertOk()
            ->assertSee('งานเต็นท์')
            ->assertSee('เต็นท์ 2x2 สีขาว')
            ->assertDontSee('งานเคาน์เตอร์');

        $tentTask = $booking->deliveryTasks()->where('task_type', 'tent')->firstOrFail();
        $lotPhoto = DeliveryPhoto::create([
            'delivery_task_id' => $tentTask->id,
            'photo_type' => 'lot_number',
            'image_path' => 'delivery-photos/test-lot.jpg',
            'ocr_status' => 'pending_review',
            'uploaded_by' => $tentStaff->id,
        ]);

        $dashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboard->assertOk()
            ->assertSee('คนส่งเต็นท์')
            ->assertSee('คนส่งเคาน์เตอร์')
            ->assertSee('คนส่งอุปกรณ์')
            ->assertSee('ถุงทรายและเชือก')
            ->assertSee('ส่งแล้ว / รอตรวจ 1 รูป')
            ->assertSee('Approve งานเต็นท์')
            ->assertSee(route('admin.lot_photo_reviews.approve', $lotPhoto), false);

        $this->actingAs($admin)
            ->post(route('admin.lot_photo_reviews.approve', $lotPhoto))
            ->assertSessionHas('success');
        $this->assertSame('approved', $lotPhoto->fresh()->ocr_status);
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
            'status' => 'assigned',
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
