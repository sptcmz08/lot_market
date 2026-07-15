<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use ZipArchive;

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
            ->assertSee('เก็บแล้ว')
            ->assertSee('ส่งออก Excel')
            ->assertSee('Home')
            ->assertSee(route('public.booking.create'), false);
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

    public function test_admin_can_export_front_store_collection_as_excel(): void
    {
        $admin = $this->createAdmin('admin-export-test');
        $useDate = now()->addDays(2)->toDateString();
        $booking = $this->createBooking($useDate, true, 'BKEXPORT001');
        $booking->update([
            'front_store_collected_amount' => 750,
            'front_store_collected_at' => now(),
            'front_store_collected_by' => $admin->id,
            'shop_name' => "ร้านทดสอบ\x01อักขระ",
        ]);
        $lot = Lot::firstOrCreate(
            ['lot_code' => 'GB20'],
            ['display_name' => 'GB20', 'is_active' => true]
        );
        $booking->lots()->attach($lot);

        $response = $this->actingAs($admin)->get(route('admin.dashboard.front_store_export', ['date' => $useDate]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertDownload('front-store-collection-'.$useDate.'.xlsx');

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($response->getFile()->getPathname()) === true);
        $requiredParts = [
            '[Content_Types].xml',
            '_rels/.rels',
            'docProps/app.xml',
            'docProps/core.xml',
            'xl/workbook.xml',
            'xl/_rels/workbook.xml.rels',
            'xl/styles.xml',
            'xl/worksheets/sheet1.xml',
        ];
        foreach ($requiredParts as $part) {
            $xml = $zip->getFromName($part);
            $this->assertIsString($xml, $part.' must exist in the XLSX package');
            $document = new \DOMDocument();
            $this->assertTrue($document->loadXML($xml), $part.' must contain valid XML');
        }
        $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        $this->assertStringContainsString('BKEXPORT001', $worksheet);
        $this->assertStringContainsString('GB20', $worksheet);
        $this->assertStringContainsString('ร้านทดสอบอักขระ', $worksheet);
        $this->assertStringNotContainsString("\x01", $worksheet);
        $this->assertStringContainsString('<v>750</v>', $worksheet);
    }

    public function test_staff_cannot_export_front_store_collection(): void
    {
        $staff = User::create([
            'name' => 'พนักงานทดสอบ',
            'username' => 'staff-export-test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->actingAs($staff)
            ->get(route('admin.dashboard.front_store_export'))
            ->assertForbidden();
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
