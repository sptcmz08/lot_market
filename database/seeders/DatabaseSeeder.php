<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Zone;
use App\Models\Lot;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        User::create([
            'name' => 'แอดมินใจดี',
            'email' => 'admin@example.com',
            'phone' => '0888888888',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'พนักงานส่งเต็นท์ A',
            'email' => 'staff@example.com',
            'phone' => '0999999999',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'พนักงานส่งเต็นท์ B',
            'email' => 'staff2@example.com',
            'phone' => '0777777777',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        // 2. Define Zones matching the image
        // Left Block: GB to GJ
        $leftCodes = ['GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ'];
        // Bottom Block: GL to GT
        $bottomCodes = ['GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT'];
        // Right Block (Orange Tents): GW, GX, GY, GZ
        $rightCodes = ['GW', 'GX', 'GY', 'GZ'];

        $zones = [];
        $order = 1;

        foreach ($leftCodes as $code) {
            $zones[$code] = Zone::create([
                'code' => $code,
                'name' => "โซน {$code}",
                'sort_order' => $order++
            ]);
        }

        foreach ($bottomCodes as $code) {
            $zones[$code] = Zone::create([
                'code' => $code,
                'name' => "โซน {$code}",
                'sort_order' => $order++
            ]);
        }

        foreach ($rightCodes as $code) {
            $zones[$code] = Zone::create([
                'code' => $code,
                'name' => "โซน {$code} (เต็นท์ส้ม)",
                'sort_order' => $order++
            ]);
        }

        // 3. Seed Lots for Left Block (GB to GJ)
        // c: column index, r: stall index
        foreach ($leftCodes as $cIndex => $code) {
            $zone = $zones[$code];
            for ($r = 1; $r <= 10; $r++) {
                $num = str_pad($r, 2, '0', STR_PAD_LEFT);
                $lotCode = $code . $num;

                // 2D grid position inside the isometric group
                $x = $cIndex * 34;
                $y = ($r - 1) * 26;

                Lot::create([
                    'zone_id' => $zone->id,
                    'lot_code' => $lotCode,
                    'display_name' => $lotCode,
                    'svg_element_id' => 'lot-' . $lotCode,
                    'position_x' => $x,
                    'position_y' => $y,
                    'width' => 24,
                    'height' => 18,
                    'is_active' => true,
                    'note' => "แผงค้าทั่วไปแถวที่ {$r}",
                ]);
            }
        }

        // 4. Seed Lots for Bottom Block (GL to GT)
        foreach ($bottomCodes as $cIndex => $code) {
            $zone = $zones[$code];
            for ($r = 1; $r <= 10; $r++) {
                $num = str_pad($r, 2, '0', STR_PAD_LEFT);
                $lotCode = $code . $num;

                $x = $cIndex * 34;
                $y = ($r - 1) * 26;

                Lot::create([
                    'zone_id' => $zone->id,
                    'lot_code' => $lotCode,
                    'display_name' => $lotCode,
                    'svg_element_id' => 'lot-' . $lotCode,
                    'position_x' => $x,
                    'position_y' => $y,
                    'width' => 24,
                    'height' => 18,
                    'is_active' => true,
                    'note' => "แผงค้าทั่วไปแถวที่ {$r}",
                ]);
            }
        }

        // 5. Seed Lots for Right Block (GW to GZ) - Large Orange Tents
        foreach ($rightCodes as $cIndex => $code) {
            $zone = $zones[$code];
            for ($r = 1; $r <= 10; $r++) {
                $num = str_pad($r, 2, '0', STR_PAD_LEFT);
                $lotCode = $code . $num;

                $x = $cIndex * 54;
                $y = ($r - 1) * 36;

                Lot::create([
                    'zone_id' => $zone->id,
                    'lot_code' => $lotCode,
                    'display_name' => $lotCode,
                    'svg_element_id' => 'lot-' . $lotCode,
                    'position_x' => $x,
                    'position_y' => $y,
                    'width' => 38,
                    'height' => 24,
                    'is_active' => true,
                    'note' => "เต็นท์ส้มขนาดใหญ่แถวที่ {$r}",
                ]);
            }
        }

        // 6. Seed Settings
        Setting::create([
            'setting_key' => 'show_shop_name_public',
            'setting_value' => 'true',
        ]);
    }
}
