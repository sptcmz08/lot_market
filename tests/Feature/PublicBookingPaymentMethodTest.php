<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicBookingPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_form_has_separate_tent_and_counter_quantity_fields(): void
    {
        $this->get(route('public.booking.create'))
            ->assertOk()
            ->assertSee('name="tent_quantity"', false)
            ->assertSee('name="counter_quantity"', false)
            ->assertSee('data-equipment-row', false);
    }

    public function test_slip_payment_requires_and_stores_a_slip_without_front_store_collection(): void
    {
        Storage::fake('public');
        $this->createLot();

        $response = $this->post(route('public.booking.store'), $this->bookingData([
            'payment_method' => 'slip',
            'payment_slip' => $this->fakePaymentSlip(),
        ]));

        $response->assertRedirect(route('public.booking.check'));

        $booking = Booking::firstOrFail();
        $this->assertFalse($booking->collect_front_store);
        $this->assertNotNull($booking->payment_slip_path);
        $this->assertSame(3, $booking->tent_quantity);
        Storage::disk('public')->assertExists($booking->payment_slip_path);
    }

    public function test_front_store_payment_creates_collection_item_without_a_slip(): void
    {
        Storage::fake('public');
        $this->createLot();

        $response = $this->post(route('public.booking.store'), $this->bookingData([
            'payment_method' => 'front_store',
            'wants_counter' => 1,
            'counter_size' => '2 ล็อค 140x75 cm.',
            'counter_quantity' => 4,
        ]));

        $response->assertRedirect(route('public.booking.check'));

        $booking = Booking::firstOrFail();
        $this->assertTrue($booking->collect_front_store);
        $this->assertNull($booking->payment_slip_path);
        $this->assertSame(4, $booking->counter_quantity);
        $this->assertStringContainsString('จำนวน 4 ชุด', $booking->equipmentSummary());
    }

    public function test_slip_payment_cannot_be_submitted_without_a_slip(): void
    {
        $this->createLot();

        $response = $this->from(route('public.booking.create'))->post(
            route('public.booking.store'),
            $this->bookingData(['payment_method' => 'slip'])
        );

        $response->assertRedirect(route('public.booking.create'))
            ->assertSessionHasErrors('payment_slip');
        $this->assertSame(0, Booking::count());
    }

    public function test_booking_range_resolves_zero_padded_lot_codes_from_the_market_layout(): void
    {
        Lot::whereIn('lot_code', ['GJ08', 'GJ09'])->update(['is_active' => true]);

        $response = $this->post(route('public.booking.store'), $this->bookingData([
            'payment_method' => 'front_store',
            'lot_prefix' => 'GJ',
            'lot_number_from' => 8,
            'lot_number_to' => 9,
        ]));

        $response->assertRedirect(route('public.booking.check'));

        $this->assertSame(
            ['GJ08', 'GJ09'],
            Booking::firstOrFail()->lots()->orderBy('lot_code')->pluck('lot_code')->all()
        );
    }

    private function bookingData(array $overrides = []): array
    {
        return array_merge([
            'use_date' => now()->addDay()->toDateString(),
            'shop_name' => 'ร้านทดสอบวิธีชำระเงิน',
            'customer_phone' => '0812345678',
            'lot_mode' => 'single',
            'lot_prefix' => 'GA',
            'lot_number_from' => 1,
            'wants_tent' => 1,
            'tent_size' => '2x2',
            'tent_color' => 'ขาว',
            'tent_quantity' => 3,
        ], $overrides);
    }

    private function createLot(): void
    {
        Lot::create([
            'lot_code' => 'GA1',
            'display_name' => 'GA1',
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
