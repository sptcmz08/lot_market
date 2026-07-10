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
        $zones = [];
        $zones["F28"] = Zone::create([
            "code" => "F28",
            "name" => "โซนอาหาร (2,800/วัน)",
            "sort_order" => 0
        ]);

        $zones["F26"] = Zone::create([
            "code" => "F26",
            "name" => "โซนอาหาร (2,600/วัน)",
            "sort_order" => 0
        ]);

        $zones["F25"] = Zone::create([
            "code" => "F25",
            "name" => "โซนอาหาร (2,500/วัน)",
            "sort_order" => 0
        ]);

        $zones["F22"] = Zone::create([
            "code" => "F22",
            "name" => "โซนอาหาร (2,200/วัน)",
            "sort_order" => 0
        ]);

        $zones["F16"] = Zone::create([
            "code" => "F16",
            "name" => "โซนอาหาร (1,600/วัน)",
            "sort_order" => 0
        ]);

        $zones["S18"] = Zone::create([
            "code" => "S18",
            "name" => "โซนแฟชั่น/อื่นๆ (1,800/วัน)",
            "sort_order" => 0
        ]);

        $zones["S15"] = Zone::create([
            "code" => "S15",
            "name" => "โซนแฟชั่น/อื่นๆ (1,500/วัน)",
            "sort_order" => 0
        ]);

        $zones["S13"] = Zone::create([
            "code" => "S13",
            "name" => "โซนแฟชั่น/อื่นๆ (1,300/วัน)",
            "sort_order" => 0
        ]);

        $zones["S12"] = Zone::create([
            "code" => "S12",
            "name" => "โซนแฟชั่น/อื่นๆ (1,200/วัน)",
            "sort_order" => 0
        ]);

        $zones["S10"] = Zone::create([
            "code" => "S10",
            "name" => "โซนแฟชั่น/อื่นๆ (1,000/วัน)",
            "sort_order" => 0
        ]);

        // 3. Seed Lots
        $lotsData = [
            [
                "zone_code" => "F25",
                "lot_code" => "261",
                "display_name" => "261",
                "position_x" => 390,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F25",
                "lot_code" => "260",
                "display_name" => "260",
                "position_x" => 330,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F25",
                "lot_code" => "259",
                "display_name" => "259",
                "position_x" => 270,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F25",
                "lot_code" => "258",
                "display_name" => "258",
                "position_x" => 210,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F25",
                "lot_code" => "262",
                "display_name" => "262",
                "position_x" => 565,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F25",
                "lot_code" => "263",
                "display_name" => "263",
                "position_x" => 625,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F25",
                "lot_code" => "264",
                "display_name" => "264",
                "position_x" => 685,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F25",
                "lot_code" => "265",
                "display_name" => "265",
                "position_x" => 745,
                "position_y" => 150,
                "width" => 45,
                "height" => 45
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "252",
                "display_name" => "252",
                "position_x" => 130,
                "position_y" => 260,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "251",
                "display_name" => "251",
                "position_x" => 130,
                "position_y" => 286,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "254",
                "display_name" => "254",
                "position_x" => 160,
                "position_y" => 260,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "253",
                "display_name" => "253",
                "position_x" => 160,
                "position_y" => 286,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "224",
                "display_name" => "224",
                "position_x" => 105,
                "position_y" => 340,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "225",
                "display_name" => "225",
                "position_x" => 105,
                "position_y" => 366,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "226",
                "display_name" => "226",
                "position_x" => 135,
                "position_y" => 340,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "227",
                "display_name" => "227",
                "position_x" => 135,
                "position_y" => 366,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "176",
                "display_name" => "176",
                "position_x" => 90,
                "position_y" => 420,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "175",
                "display_name" => "175",
                "position_x" => 95,
                "position_y" => 470,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "125",
                "display_name" => "125",
                "position_x" => 105,
                "position_y" => 520,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "124",
                "display_name" => "124",
                "position_x" => 115,
                "position_y" => 570,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "74",
                "display_name" => "74",
                "position_x" => 130,
                "position_y" => 630,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "73",
                "display_name" => "73",
                "position_x" => 145,
                "position_y" => 680,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "45",
                "display_name" => "45",
                "position_x" => 170,
                "position_y" => 735,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "44",
                "display_name" => "44",
                "position_x" => 190,
                "position_y" => 785,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "32",
                "display_name" => "32",
                "position_x" => 215,
                "position_y" => 840,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "31",
                "display_name" => "31",
                "position_x" => 235,
                "position_y" => 890,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "22",
                "display_name" => "22",
                "position_x" => 260,
                "position_y" => 940,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "21",
                "display_name" => "21",
                "position_x" => 280,
                "position_y" => 990,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "256",
                "display_name" => "256",
                "position_x" => 844,
                "position_y" => 260,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "255",
                "display_name" => "255",
                "position_x" => 844,
                "position_y" => 286,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "258W",
                "display_name" => "258",
                "position_x" => 814,
                "position_y" => 260,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F16",
                "lot_code" => "257",
                "display_name" => "257",
                "position_x" => 814,
                "position_y" => 286,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "216",
                "display_name" => "216",
                "position_x" => 869,
                "position_y" => 340,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "217",
                "display_name" => "217",
                "position_x" => 869,
                "position_y" => 366,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "218",
                "display_name" => "218",
                "position_x" => 839,
                "position_y" => 340,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S18",
                "lot_code" => "219",
                "display_name" => "219",
                "position_x" => 839,
                "position_y" => 366,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "174",
                "display_name" => "174",
                "position_x" => 884,
                "position_y" => 420,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "173",
                "display_name" => "173",
                "position_x" => 879,
                "position_y" => 470,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "121",
                "display_name" => "121",
                "position_x" => 869,
                "position_y" => 520,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "120",
                "display_name" => "120",
                "position_x" => 859,
                "position_y" => 570,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "72",
                "display_name" => "72",
                "position_x" => 844,
                "position_y" => 630,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "71",
                "display_name" => "71",
                "position_x" => 829,
                "position_y" => 680,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "43",
                "display_name" => "43",
                "position_x" => 804,
                "position_y" => 735,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "42",
                "display_name" => "42",
                "position_x" => 784,
                "position_y" => 785,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "30",
                "display_name" => "30",
                "position_x" => 759,
                "position_y" => 840,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "29",
                "display_name" => "29",
                "position_x" => 739,
                "position_y" => 890,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "24",
                "display_name" => "24",
                "position_x" => 714,
                "position_y" => 940,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "23",
                "display_name" => "23",
                "position_x" => 694,
                "position_y" => 990,
                "width" => 26,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "218W",
                "display_name" => "218",
                "position_x" => 180,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "219W",
                "display_name" => "219",
                "position_x" => 180,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "226W",
                "display_name" => "226",
                "position_x" => 212,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "227W",
                "display_name" => "227",
                "position_x" => 212,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "220",
                "display_name" => "220",
                "position_x" => 250,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "221",
                "display_name" => "221",
                "position_x" => 250,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "228",
                "display_name" => "228",
                "position_x" => 282,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "229",
                "display_name" => "229",
                "position_x" => 282,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "222",
                "display_name" => "222",
                "position_x" => 320,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "223",
                "display_name" => "223",
                "position_x" => 320,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "230",
                "display_name" => "230",
                "position_x" => 352,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "231",
                "display_name" => "231",
                "position_x" => 352,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "224W",
                "display_name" => "224",
                "position_x" => 390,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "225W",
                "display_name" => "225",
                "position_x" => 390,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "232",
                "display_name" => "232",
                "position_x" => 422,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "233",
                "display_name" => "233",
                "position_x" => 422,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "234",
                "display_name" => "234",
                "position_x" => 550,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "235",
                "display_name" => "235",
                "position_x" => 550,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "242",
                "display_name" => "242",
                "position_x" => 582,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "243",
                "display_name" => "243",
                "position_x" => 582,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "236",
                "display_name" => "236",
                "position_x" => 620,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "237",
                "display_name" => "237",
                "position_x" => 620,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "244",
                "display_name" => "244",
                "position_x" => 652,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "245",
                "display_name" => "245",
                "position_x" => 652,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "238",
                "display_name" => "238",
                "position_x" => 690,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "239",
                "display_name" => "239",
                "position_x" => 690,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "246",
                "display_name" => "246",
                "position_x" => 722,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "247",
                "display_name" => "247",
                "position_x" => 722,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "240",
                "display_name" => "240",
                "position_x" => 760,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "241",
                "display_name" => "241",
                "position_x" => 760,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "248",
                "display_name" => "248",
                "position_x" => 792,
                "position_y" => 240,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "249",
                "display_name" => "249",
                "position_x" => 792,
                "position_y" => 266,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "178",
                "display_name" => "178",
                "position_x" => 180,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "177",
                "display_name" => "177",
                "position_x" => 180,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "186",
                "display_name" => "186",
                "position_x" => 212,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "187",
                "display_name" => "187",
                "position_x" => 212,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "180",
                "display_name" => "180",
                "position_x" => 250,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "179",
                "display_name" => "179",
                "position_x" => 250,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "188",
                "display_name" => "188",
                "position_x" => 282,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "189",
                "display_name" => "189",
                "position_x" => 282,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "182",
                "display_name" => "182",
                "position_x" => 320,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "181",
                "display_name" => "181",
                "position_x" => 320,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "190",
                "display_name" => "190",
                "position_x" => 352,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "191",
                "display_name" => "191",
                "position_x" => 352,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "184",
                "display_name" => "184",
                "position_x" => 390,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "183",
                "display_name" => "183",
                "position_x" => 390,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "192",
                "display_name" => "192",
                "position_x" => 422,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "193",
                "display_name" => "193",
                "position_x" => 422,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "194",
                "display_name" => "194",
                "position_x" => 550,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "195",
                "display_name" => "195",
                "position_x" => 550,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "202",
                "display_name" => "202",
                "position_x" => 582,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "203",
                "display_name" => "203",
                "position_x" => 582,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "196",
                "display_name" => "196",
                "position_x" => 620,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "197",
                "display_name" => "197",
                "position_x" => 620,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "204",
                "display_name" => "204",
                "position_x" => 652,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "205",
                "display_name" => "205",
                "position_x" => 652,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "198",
                "display_name" => "198",
                "position_x" => 690,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "199",
                "display_name" => "199",
                "position_x" => 690,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "206",
                "display_name" => "206",
                "position_x" => 722,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "207",
                "display_name" => "207",
                "position_x" => 722,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "200",
                "display_name" => "200",
                "position_x" => 760,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "201",
                "display_name" => "201",
                "position_x" => 760,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "208",
                "display_name" => "208",
                "position_x" => 792,
                "position_y" => 320,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "209",
                "display_name" => "209",
                "position_x" => 792,
                "position_y" => 346,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "138",
                "display_name" => "138",
                "position_x" => 180,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "137",
                "display_name" => "137",
                "position_x" => 180,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "146",
                "display_name" => "146",
                "position_x" => 212,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "147",
                "display_name" => "147",
                "position_x" => 212,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "140",
                "display_name" => "140",
                "position_x" => 250,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "139",
                "display_name" => "139",
                "position_x" => 250,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "148",
                "display_name" => "148",
                "position_x" => 282,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "149",
                "display_name" => "149",
                "position_x" => 282,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "142",
                "display_name" => "142",
                "position_x" => 320,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "141",
                "display_name" => "141",
                "position_x" => 320,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "150",
                "display_name" => "150",
                "position_x" => 352,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "151",
                "display_name" => "151",
                "position_x" => 352,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "144",
                "display_name" => "144",
                "position_x" => 390,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "143",
                "display_name" => "143",
                "position_x" => 390,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "152",
                "display_name" => "152",
                "position_x" => 422,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "153",
                "display_name" => "153",
                "position_x" => 422,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "154",
                "display_name" => "154",
                "position_x" => 550,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "155",
                "display_name" => "155",
                "position_x" => 550,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "162",
                "display_name" => "162",
                "position_x" => 582,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "163",
                "display_name" => "163",
                "position_x" => 582,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "156",
                "display_name" => "156",
                "position_x" => 620,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "157",
                "display_name" => "157",
                "position_x" => 620,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "164",
                "display_name" => "164",
                "position_x" => 652,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "165",
                "display_name" => "165",
                "position_x" => 652,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "158",
                "display_name" => "158",
                "position_x" => 690,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "159",
                "display_name" => "159",
                "position_x" => 690,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "166",
                "display_name" => "166",
                "position_x" => 722,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "167",
                "display_name" => "167",
                "position_x" => 722,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "160",
                "display_name" => "160",
                "position_x" => 760,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "161",
                "display_name" => "161",
                "position_x" => 760,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "168",
                "display_name" => "168",
                "position_x" => 792,
                "position_y" => 400,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S13",
                "lot_code" => "169",
                "display_name" => "169",
                "position_x" => 792,
                "position_y" => 426,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "108",
                "display_name" => "108",
                "position_x" => 180,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "107",
                "display_name" => "107",
                "position_x" => 180,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "116",
                "display_name" => "116",
                "position_x" => 212,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "117",
                "display_name" => "117",
                "position_x" => 212,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "110",
                "display_name" => "110",
                "position_x" => 250,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "109",
                "display_name" => "109",
                "position_x" => 250,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "118",
                "display_name" => "118",
                "position_x" => 282,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "119",
                "display_name" => "119",
                "position_x" => 282,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "112",
                "display_name" => "112",
                "position_x" => 320,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "111",
                "display_name" => "111",
                "position_x" => 320,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "120W",
                "display_name" => "120",
                "position_x" => 352,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "121W",
                "display_name" => "121",
                "position_x" => 352,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "114",
                "display_name" => "114",
                "position_x" => 390,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "113",
                "display_name" => "113",
                "position_x" => 390,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "122",
                "display_name" => "122",
                "position_x" => 422,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "123",
                "display_name" => "123",
                "position_x" => 422,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "124W",
                "display_name" => "124",
                "position_x" => 550,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "123W",
                "display_name" => "123",
                "position_x" => 550,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "132",
                "display_name" => "132",
                "position_x" => 582,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "131",
                "display_name" => "131",
                "position_x" => 582,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "126",
                "display_name" => "126",
                "position_x" => 620,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "125W",
                "display_name" => "125",
                "position_x" => 620,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "134",
                "display_name" => "134",
                "position_x" => 652,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "133",
                "display_name" => "133",
                "position_x" => 652,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "128",
                "display_name" => "128",
                "position_x" => 690,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "127",
                "display_name" => "127",
                "position_x" => 690,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "136",
                "display_name" => "136",
                "position_x" => 722,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "135",
                "display_name" => "135",
                "position_x" => 722,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "130",
                "display_name" => "130",
                "position_x" => 760,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "129",
                "display_name" => "129",
                "position_x" => 760,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "138W",
                "display_name" => "138",
                "position_x" => 792,
                "position_y" => 580,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "S10",
                "lot_code" => "137W",
                "display_name" => "137",
                "position_x" => 792,
                "position_y" => 606,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "76",
                "display_name" => "76",
                "position_x" => 180,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "75",
                "display_name" => "75",
                "position_x" => 180,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "84",
                "display_name" => "84",
                "position_x" => 212,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "85",
                "display_name" => "85",
                "position_x" => 212,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "78",
                "display_name" => "78",
                "position_x" => 250,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "77",
                "display_name" => "77",
                "position_x" => 250,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "86",
                "display_name" => "86",
                "position_x" => 282,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "87",
                "display_name" => "87",
                "position_x" => 282,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "80",
                "display_name" => "80",
                "position_x" => 320,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "79",
                "display_name" => "79",
                "position_x" => 320,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "88",
                "display_name" => "88",
                "position_x" => 352,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "89",
                "display_name" => "89",
                "position_x" => 352,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "82",
                "display_name" => "82",
                "position_x" => 390,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "81",
                "display_name" => "81",
                "position_x" => 390,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "90",
                "display_name" => "90",
                "position_x" => 422,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "91",
                "display_name" => "91",
                "position_x" => 422,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "92",
                "display_name" => "92",
                "position_x" => 550,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "93",
                "display_name" => "93",
                "position_x" => 550,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "100",
                "display_name" => "100",
                "position_x" => 582,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "101",
                "display_name" => "101",
                "position_x" => 582,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "94",
                "display_name" => "94",
                "position_x" => 620,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "95",
                "display_name" => "95",
                "position_x" => 620,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "102",
                "display_name" => "102",
                "position_x" => 652,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "103",
                "display_name" => "103",
                "position_x" => 652,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "96",
                "display_name" => "96",
                "position_x" => 690,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "97",
                "display_name" => "97",
                "position_x" => 690,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "104",
                "display_name" => "104",
                "position_x" => 722,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "105",
                "display_name" => "105",
                "position_x" => 722,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "98",
                "display_name" => "98",
                "position_x" => 760,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "99",
                "display_name" => "99",
                "position_x" => 760,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "106",
                "display_name" => "106",
                "position_x" => 792,
                "position_y" => 660,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "107W",
                "display_name" => "107",
                "position_x" => 792,
                "position_y" => 686,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "48",
                "display_name" => "48",
                "position_x" => 250,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "47",
                "display_name" => "47",
                "position_x" => 250,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "54",
                "display_name" => "54",
                "position_x" => 282,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "55",
                "display_name" => "55",
                "position_x" => 282,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "50",
                "display_name" => "50",
                "position_x" => 320,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "49",
                "display_name" => "49",
                "position_x" => 320,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "56",
                "display_name" => "56",
                "position_x" => 352,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "57",
                "display_name" => "57",
                "position_x" => 352,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "52",
                "display_name" => "52",
                "position_x" => 390,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "51",
                "display_name" => "51",
                "position_x" => 390,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "58",
                "display_name" => "58",
                "position_x" => 422,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "59",
                "display_name" => "59",
                "position_x" => 422,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "60",
                "display_name" => "60",
                "position_x" => 550,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "59W",
                "display_name" => "59",
                "position_x" => 550,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "66",
                "display_name" => "66",
                "position_x" => 582,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "67",
                "display_name" => "67",
                "position_x" => 582,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "62",
                "display_name" => "62",
                "position_x" => 620,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "61",
                "display_name" => "61",
                "position_x" => 620,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "68",
                "display_name" => "68",
                "position_x" => 652,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "69",
                "display_name" => "69",
                "position_x" => 652,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "64",
                "display_name" => "64",
                "position_x" => 690,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "63",
                "display_name" => "63",
                "position_x" => 690,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "70",
                "display_name" => "70",
                "position_x" => 722,
                "position_y" => 740,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F22",
                "lot_code" => "71W",
                "display_name" => "71",
                "position_x" => 722,
                "position_y" => 766,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "26",
                "display_name" => "26",
                "position_x" => 320,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "25",
                "display_name" => "25",
                "position_x" => 320,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "32W",
                "display_name" => "32",
                "position_x" => 352,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "33",
                "display_name" => "33",
                "position_x" => 352,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "28",
                "display_name" => "28",
                "position_x" => 390,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "27",
                "display_name" => "27",
                "position_x" => 390,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "34",
                "display_name" => "34",
                "position_x" => 422,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "35",
                "display_name" => "35",
                "position_x" => 422,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "34W",
                "display_name" => "34",
                "position_x" => 550,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "33W",
                "display_name" => "33",
                "position_x" => 550,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "40",
                "display_name" => "40",
                "position_x" => 582,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "39",
                "display_name" => "39",
                "position_x" => 582,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "36",
                "display_name" => "36",
                "position_x" => 620,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "35W",
                "display_name" => "35",
                "position_x" => 620,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "42W",
                "display_name" => "42",
                "position_x" => 652,
                "position_y" => 820,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F26",
                "lot_code" => "41",
                "display_name" => "41",
                "position_x" => 652,
                "position_y" => 846,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "2",
                "display_name" => "2",
                "position_x" => 285,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "1",
                "display_name" => "1",
                "position_x" => 285,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "4",
                "display_name" => "4",
                "position_x" => 317,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "3",
                "display_name" => "3",
                "position_x" => 317,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "6",
                "display_name" => "6",
                "position_x" => 355,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "5",
                "display_name" => "5",
                "position_x" => 355,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "8",
                "display_name" => "8",
                "position_x" => 387,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "7",
                "display_name" => "7",
                "position_x" => 387,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "10",
                "display_name" => "10",
                "position_x" => 425,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "9",
                "display_name" => "9",
                "position_x" => 425,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "12",
                "display_name" => "12",
                "position_x" => 457,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "11",
                "display_name" => "11",
                "position_x" => 457,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "14",
                "display_name" => "14",
                "position_x" => 535,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "13",
                "display_name" => "13",
                "position_x" => 535,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "16",
                "display_name" => "16",
                "position_x" => 567,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "15",
                "display_name" => "15",
                "position_x" => 567,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "18",
                "display_name" => "18",
                "position_x" => 605,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "17",
                "display_name" => "17",
                "position_x" => 605,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "20",
                "display_name" => "20",
                "position_x" => 637,
                "position_y" => 900,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "19",
                "display_name" => "19",
                "position_x" => 637,
                "position_y" => 926,
                "width" => 28,
                "height" => 22
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "C",
                "display_name" => "C",
                "position_x" => 425,
                "position_y" => 1070,
                "width" => 32,
                "height" => 24
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "D",
                "display_name" => "D",
                "position_x" => 465,
                "position_y" => 1070,
                "width" => 32,
                "height" => 24
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "B",
                "display_name" => "B",
                "position_x" => 425,
                "position_y" => 1105,
                "width" => 32,
                "height" => 24
            ],
            [
                "zone_code" => "F28",
                "lot_code" => "A",
                "display_name" => "A",
                "position_x" => 465,
                "position_y" => 1105,
                "width" => 32,
                "height" => 24
            ],
        ];

        foreach ($lotsData as $ld) {
            $zone = $zones[$ld["zone_code"]];
            Lot::create([
                "zone_id" => $zone->id,
                "lot_code" => $ld["lot_code"],
                "display_name" => $ld["display_name"],
                "svg_element_id" => "lot-" . $ld["lot_code"],
                "position_x" => $ld["position_x"],
                "position_y" => $ld["position_y"],
                "width" => $ld["width"],
                "height" => $ld["height"],
                "is_active" => true
            ]);
        }

        // 6. Seed Settings
        Setting::create([
            'setting_key' => 'show_shop_name_public',
            'setting_value' => 'true',
        ]);
    }
}
