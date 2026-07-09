<?php

namespace Database\Seeders;

use App\Models\User;
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

        // 2. Seed Zones
        $zonesData = [
            ['code' => 'GB', 'name' => 'โซนบี (สีเขียว)', 'sort_order' => 1],
            ['code' => 'GC', 'name' => 'โซนซี (สีฟ้า)', 'sort_order' => 2],
            ['code' => 'GD', 'name' => 'โซนดี (สีทอง)', 'sort_order' => 3],
            ['code' => 'GY', 'name' => 'โซนวาย (สีเทา)', 'sort_order' => 4],
        ];

        $zones = [];
        foreach ($zonesData as $zd) {
            $zones[$zd['code']] = \App\Models\Zone::create($zd);
        }

        // 3. Seed Lots with layout positions for the interactive SVG Map
        $zoneConfig = [
            'GB' => ['y' => 50, 'color' => '#6FD08C'],
            'GC' => ['y' => 120, 'color' => '#8BD3DD'],
            'GD' => ['y' => 190, 'color' => '#FFD166'],
            'GY' => ['y' => 260, 'color' => '#BDBDBD'],
        ];

        foreach ($zoneConfig as $code => $cfg) {
            $zone = $zones[$code];
            for ($i = 1; $i <= 10; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $lotCode = $code . $num;
                
                // standard grid
                $x = 40 + (($i - 1) * 55);
                
                \App\Models\Lot::create([
                    'zone_id' => $zone->id,
                    'lot_code' => $lotCode,
                    'display_name' => $lotCode,
                    'svg_element_id' => 'lot-' . $lotCode,
                    'position_x' => $x,
                    'position_y' => $cfg['y'],
                    'width' => 45,
                    'height' => 45,
                    'is_active' => true,
                    'note' => 'ล็อคมาตรฐานโซน ' . $code,
                ]);
            }
        }

        // 4. Seed Settings
        \App\Models\Setting::create([
            'setting_key' => 'show_shop_name_public',
            'setting_value' => 'true',
        ]);
    }
}
