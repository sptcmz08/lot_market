<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\DeliveryPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_status_becomes_completed_and_shows_completed_badge_when_all_tasks_approved(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $booking = Booking::create([
            'booking_code' => 'BK20260803083855312',
            'shop_name' => 'ร้านดีดี',
            'customer_name' => 'ลูกค้า',
            'customer_phone' => '0936544294',
            'use_date' => now()->format('Y-m-d'),
            'status' => 'confirmed',
            'total_price' => 200.00,
        ]);

        $taskTent = DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_type' => 'tent',
            'status' => 'photo_uploaded',
            'task_date' => now()->format('Y-m-d'),
        ]);

        DeliveryPhoto::create([
            'delivery_task_id' => $taskTent->id,
            'photo_type' => 'after',
            'image_path' => 'photos/tent_after.jpg',
        ]);

        $taskCounter = DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_type' => 'counter',
            'status' => 'photo_uploaded',
            'task_date' => now()->format('Y-m-d'),
        ]);

        DeliveryPhoto::create([
            'delivery_task_id' => $taskCounter->id,
            'photo_type' => 'after',
            'image_path' => 'photos/counter_after.jpg',
        ]);

        // Approve task 1
        $this->actingAs($admin)
            ->post(route('admin.tasks.work_review.approve', $taskTent))
            ->assertSessionHasNoErrors();

        // Approve task 2
        $this->actingAs($admin)
            ->post(route('admin.tasks.work_review.approve', $taskCounter))
            ->assertSessionHasNoErrors();

        $booking->refresh();

        // Verify booking status is now 'completed'
        $this->assertEquals('completed', $booking->status);

        // Verify admin bookings index table shows 'ติดตั้งสำเร็จ' badge instead of 'มีรูปงานรอส่งตรวจ'
        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));
        $response->assertSee('ติดตั้งสำเร็จ');
        $response->assertDontSee('มีรูปงานรอส่งตรวจ');
    }
}
