<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DeliveryPhoto;
use App\Models\DeliveryTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBookingPhotoEvidenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_sees_approved_lot_photos_and_all_completion_photos(): void
    {
        $booking = $this->createBooking('completed');
        $task = $this->createTask($booking, 'completed');

        $this->createPhoto($task, 'lot_number', 'delivery-photos/approved-lot.jpg', 'approved');
        $this->createPhoto($task, 'lot_number', 'delivery-photos/pending-lot.jpg', 'pending_review');
        $this->createPhoto($task, 'after', 'delivery-photos/after-one.jpg');
        $this->createPhoto($task, 'after', 'delivery-photos/after-two.jpg');

        $response = $this->post(route('public.booking.check.submit'), [
            'search_query' => $booking->booking_code,
        ]);

        $response->assertOk()
            ->assertSee('รูปยืนยันเลข LOT (1 รูป)')
            ->assertSee('รูปส่งงานหลังติดตั้งทั้งหมด (2 รูป)')
            ->assertSee('approved-lot.jpg')
            ->assertDontSee('pending-lot.jpg')
            ->assertSee('after-one.jpg')
            ->assertSee('after-two.jpg');
    }

    public function test_customer_does_not_see_completion_photos_before_task_is_completed(): void
    {
        $booking = $this->createBooking('installing');
        $task = $this->createTask($booking, 'started');

        $this->createPhoto($task, 'after', 'delivery-photos/not-completed.jpg');

        $response = $this->post(route('public.booking.check.submit'), [
            'search_query' => $booking->booking_code,
        ]);

        $response->assertOk()
            ->assertSee('รูปส่งงานจะแสดงเมื่อติดตั้งเสร็จแล้ว')
            ->assertDontSee('not-completed.jpg');
    }

    private function createBooking(string $status): Booking
    {
        return Booking::create([
            'booking_code' => 'BKPHOTO' . strtoupper($status),
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบรูปภาพ',
            'customer_phone' => '0812345678',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'status' => $status,
        ]);
    }

    private function createTask(Booking $booking, string $status): DeliveryTask
    {
        return DeliveryTask::create([
            'booking_id' => $booking->id,
            'task_date' => $booking->use_date,
            'status' => $status,
        ]);
    }

    private function createPhoto(
        DeliveryTask $task,
        string $type,
        string $path,
        ?string $reviewStatus = null
    ): DeliveryPhoto {
        return DeliveryPhoto::create([
            'delivery_task_id' => $task->id,
            'photo_type' => $type,
            'image_path' => $path,
            'taken_at' => now(),
            'ocr_status' => $reviewStatus,
        ]);
    }
}
