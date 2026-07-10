<?php

use App\Models\Lot;
use App\Models\Zone;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $leftCodes = ['GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ'];

        foreach ($leftCodes as $code) {
            $zone = Zone::where('code', $code)->first();
            if (!$zone) {
                continue;
            }

            for ($r = 11; $r <= 20; $r++) {
                $num = str_pad((string) $r, 2, '0', STR_PAD_LEFT);
                $lotCode = $code . $num;

                Lot::firstOrCreate(
                    ['lot_code' => $lotCode],
                    [
                        'zone_id' => $zone->id,
                        'display_name' => $lotCode,
                        'svg_element_id' => 'lot-' . $lotCode,
                        'position_x' => 0,
                        'position_y' => 0,
                        'width' => 24,
                        'height' => 18,
                        'is_active' => true,
                        'note' => "แผงค้าฝั่งซ้ายแถวที่ {$r}",
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        $leftCodes = ['GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ'];

        foreach ($leftCodes as $code) {
            for ($r = 11; $r <= 20; $r++) {
                Lot::where('lot_code', $code . str_pad((string) $r, 2, '0', STR_PAD_LEFT))->delete();
            }
        }
    }
};
