<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPaymentSlipUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_attach_a_payment_slip_before_confirming_booking(): void
    {
        Storage::fake('public');

        $admin = $this->createUser('admin', 'admin-slip-test');
        $booking = Booking::create([
            'booking_code' => 'BKADMINSLIP001',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบแนบสลิป',
            'customer_phone' => '0812345678',
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'status' => 'pending_admin',
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.bookings.payment_slip', $booking),
            ['payment_slip' => $this->fakePaymentSlip()->size(20 * 1024)]
        );

        $response->assertRedirect()
            ->assertSessionHas('success');

        $booking->refresh();

        $this->assertSame('pending_admin', $booking->status);
        $this->assertNotNull($booking->payment_slip_path);
        Storage::disk('public')->assertExists($booking->payment_slip_path);
        $this->assertDatabaseHas('status_logs', [
            'loggable_type' => Booking::class,
            'loggable_id' => $booking->id,
            'changed_by' => $admin->id,
            'note' => 'แอดมินแนบรูปสลิปการชำระเงิน',
        ]);
    }

    public function test_non_admin_cannot_attach_a_payment_slip(): void
    {
        Storage::fake('public');

        $staff = $this->createUser('staff', 'staff-slip-test');
        $booking = Booking::create([
            'booking_code' => 'BKADMINSLIP002',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบสิทธิ์',
            'customer_phone' => '0898765432',
            'tent_size' => '2x2',
            'tent_color' => 'แดง',
            'status' => 'pending_admin',
        ]);

        $response = $this->actingAs($staff)->post(
            route('admin.bookings.payment_slip', $booking),
            ['payment_slip' => $this->fakePaymentSlip()]
        );

        $response->assertForbidden();
        $this->assertNull($booking->fresh()->payment_slip_path);
        $this->assertSame(0, StatusLog::count());
    }

    public function test_admin_must_attach_a_slip_before_confirming_when_not_collecting_at_store(): void
    {
        $admin = $this->createUser('admin', 'admin-confirm-test');
        $booking = Booking::create([
            'booking_code' => 'BKADMINSLIP003',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบลำดับยืนยัน',
            'customer_phone' => '0823456789',
            'tent_size' => '2x2',
            'tent_color' => 'น้ำเงิน',
            'collect_front_store' => false,
            'status' => 'pending_admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking));

        $response->assertRedirect()
            ->assertSessionHas('error', 'กรุณาแนบรูปสลิปการชำระเงินก่อนยืนยันการจอง');
        $this->assertSame('pending_admin', $booking->fresh()->status);
        $this->assertNull($booking->fresh()->confirmed_at);
    }

    public function test_front_store_booking_does_not_allow_payment_slip_upload(): void
    {
        Storage::fake('public');

        $admin = $this->createUser('admin', 'admin-front-store-slip-test');
        $booking = Booking::create([
            'booking_code' => 'BKFRONTSTORE001',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านเก็บหน้าร้าน',
            'customer_phone' => '0834567890',
            'tent_size' => '2x2',
            'collect_front_store' => true,
            'status' => 'pending_admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.payment_slip', $booking), [
                'payment_slip' => $this->fakePaymentSlip(),
            ])
            ->assertSessionHas('error', 'รายการนี้เลือกเก็บเงินหน้าร้าน จึงไม่ต้องแนบสลิป');

        $this->assertNull($booking->fresh()->payment_slip_path);
        $this->assertSame([], Storage::disk('public')->allFiles());

        $this->actingAs($admin)
            ->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertDontSee(route('admin.bookings.payment_slip', $booking), false);
    }

    public function test_booking_list_separates_front_store_and_slip_payments(): void
    {
        $admin = $this->createUser('admin', 'admin-payment-list-test');
        $frontStoreBooking = Booking::create([
            'booking_code' => 'BKPAYMENTFRONT',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านจ่ายหน้าร้าน',
            'customer_phone' => '0845678901',
            'tent_size' => '2x2',
            'collect_front_store' => true,
            'status' => 'pending_admin',
        ]);
        $slipBooking = Booking::create([
            'booking_code' => 'BKPAYMENTSLIP',
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านแนบสลิป',
            'customer_phone' => '0856789012',
            'tent_size' => '2x2',
            'payment_slip_path' => 'payment-slips/example.jpg',
            'collect_front_store' => false,
            'status' => 'pending_admin',
        ]);

        $index = $this->actingAs($admin)->get(route('admin.bookings.index'));
        $index->assertOk()
            ->assertSee('เก็บเงินหน้าร้าน')
            ->assertSee('แนบสลิปแล้ว')
            ->assertSee('BKPAYMENTFRONT')
            ->assertSee('BKPAYMENTSLIP')
            ->assertDontSee(route('admin.bookings.payment_slip', $frontStoreBooking), false);

        $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['payment_method' => 'front_store']))
            ->assertOk()
            ->assertSee('BKPAYMENTFRONT')
            ->assertDontSee('BKPAYMENTSLIP');

        $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['payment_method' => 'slip_attached']))
            ->assertOk()
            ->assertSee('BKPAYMENTSLIP')
            ->assertDontSee('BKPAYMENTFRONT');
    }

    private function createUser(string $role, string $username): User
    {
        return User::create([
            'name' => 'ผู้ใช้ทดสอบ',
            'username' => $username,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function fakePaymentSlip(): UploadedFile
    {
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z1AAAAABJRU5ErkJggg=='
        );

        return UploadedFile::fake()->createWithContent('payment.png', $png);
    }
}
