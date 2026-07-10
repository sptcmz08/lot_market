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

        // 2. Define Zones
        // Left Block: GB to GJ (10 lots each)
        $leftCodes = ['GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ'];
        // Right Block: GL to GT (GL-GS = 14 lots each, GT = 9 lots)
        $rightCodes = ['GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT'];

        $zones = [];
        $order = 1;

        foreach ($leftCodes as $code) {
            $zones[$code] = Zone::create([
                'code' => $code,
                'name' => "โซน {$code}",
                'sort_order' => $order++
            ]);
        }

        foreach ($rightCodes as $code) {
            $zones[$code] = Zone::create([
                'code' => $code,
                'name' => "โซน {$code}",
                'sort_order' => $order++
            ]);
        }

        // 3. Seed Lots for Left Block (GB to GJ) - 10 lots each
        foreach ($leftCodes as $code) {
            $zone = $zones[$code];
            for ($r = 1; $r <= 10; $r++) {
                $num = str_pad($r, 2, '0', STR_PAD_LEFT);
                Lot::create([
                    'zone_id' => $zone->id,
                    'lot_code' => $code . $num,
                    'display_name' => $code . $num,
                    'svg_element_id' => 'lot-' . $code . $num,
                    'position_x' => 0, 'position_y' => 0,
                    'width' => 24, 'height' => 18,
                    'is_active' => true,
                    'note' => "แผงค้าฝั่งซ้ายแถวที่ {$r}",
                ]);
            }
        }

        // 4. Seed Lots for Right Block (GL to GS = 14 lots, GT = 9 lots)
        foreach ($rightCodes as $code) {
            $zone = $zones[$code];
            $maxLots = ($code === 'GT') ? 9 : 14;
            for ($r = 1; $r <= $maxLots; $r++) {
                $num = str_pad($r, 2, '0', STR_PAD_LEFT);
                Lot::create([
                    'zone_id' => $zone->id,
                    'lot_code' => $code . $num,
                    'display_name' => $code . $num,
                    'svg_element_id' => 'lot-' . $code . $num,
                    'position_x' => 0, 'position_y' => 0,
                    'width' => 20, 'height' => 16,
                    'is_active' => true,
                    'note' => "แผงค้าฝั่งขวาแถวที่ {$r}",
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
