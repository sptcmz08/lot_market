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
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'แอดมินใจดี',
                'username' => 'admin',
                'phone' => '0888888888',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'พนักงานส่งเต็นท์ A',
                'username' => 'staff',
                'phone' => '0999999999',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff2@example.com'],
            [
                'name' => 'พนักงานส่งเต็นท์ B',
                'username' => 'staff2',
                'phone' => '0777777777',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // 2. Seed Zones and Lots from the real market layout spreadsheet export.
        $layoutPath = resource_path('data/market-layout.json');
        $layout = file_exists($layoutPath) ? json_decode(file_get_contents($layoutPath), true) : ['lots' => []];
        $zones = [];
        $zoneCodes = array_values(array_unique(array_column($layout['lots'], 'zone')));

        foreach ($zoneCodes as $order => $code) {
            $zones[$code] = Zone::updateOrCreate(
                ['code' => $code],
                [
                    'name' => "โซน {$code}",
                    'sort_order' => $order + 1,
                ]
            );
        }

        foreach ($layout['lots'] as $lot) {
            Lot::updateOrCreate(
                ['lot_code' => $lot['code']],
                [
                    'zone_id' => $zones[$lot['zone']]->id ?? null,
                    'display_name' => $lot['code'],
                    'svg_element_id' => 'lot-' . $lot['code'],
                    'position_x' => $lot['excelCol'],
                    'position_y' => $lot['excelRow'],
                    'width' => 1,
                    'height' => 1,
                    'is_active' => true,
                    'note' => 'Imported from actual market layout spreadsheet',
                ]
            );
        }

        // 3. Seed Settings
        Setting::updateOrCreate(
            ['setting_key' => 'show_shop_name_public'],
            ['setting_value' => 'true']
        );
    }
}
