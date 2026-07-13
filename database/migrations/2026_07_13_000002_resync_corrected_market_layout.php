<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $layoutPath = resource_path('data/market-layout.json');
        if (!file_exists($layoutPath)) {
            return;
        }

        $layout = json_decode(file_get_contents($layoutPath), true);
        $lots = $layout['lots'] ?? [];
        $now = now();

        $zoneCodes = array_values(array_unique(array_column($lots, 'zone')));
        $zoneIds = [];

        foreach ($zoneCodes as $index => $zoneCode) {
            $existingId = DB::table('zones')->where('code', $zoneCode)->value('id');

            if ($existingId) {
                DB::table('zones')->where('id', $existingId)->update([
                    'name' => "โซน {$zoneCode}",
                    'sort_order' => $index + 1,
                    'updated_at' => $now,
                ]);
                $zoneIds[$zoneCode] = $existingId;
                continue;
            }

            $zoneIds[$zoneCode] = DB::table('zones')->insertGetId([
                'code' => $zoneCode,
                'name' => "โซน {$zoneCode}",
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $validCodes = [];
        foreach ($lots as $lot) {
            $validCodes[] = $lot['code'];
            $existingId = DB::table('lots')->where('lot_code', $lot['code'])->value('id');
            $payload = [
                'zone_id' => $zoneIds[$lot['zone']] ?? null,
                'display_name' => $lot['code'],
                'svg_element_id' => 'lot-' . $lot['code'],
                'position_x' => $lot['excelCol'],
                'position_y' => $lot['excelRow'],
                'width' => 1,
                'height' => 1,
                'is_active' => true,
                'note' => 'Imported from corrected actual market layout spreadsheet',
                'updated_at' => $now,
            ];

            if ($existingId) {
                DB::table('lots')->where('id', $existingId)->update($payload);
            } else {
                DB::table('lots')->insert($payload + [
                    'lot_code' => $lot['code'],
                    'created_at' => $now,
                ]);
            }
        }

        DB::table('lots')
            ->whereNotIn('lot_code', $validCodes)
            ->whereIn('zone_id', array_values($zoneIds))
            ->update([
                'is_active' => false,
                'note' => 'Disabled because this lot is not present in corrected market layout',
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        // Keep lot and booking history intact when rolling back.
    }
};
