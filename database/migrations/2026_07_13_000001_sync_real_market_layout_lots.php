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

        $zoneIds = [];
        foreach (array_values(array_unique(array_column($lots, 'zone'))) as $index => $zoneCode) {
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

        foreach ($lots as $lot) {
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
                'note' => 'Imported from actual market layout spreadsheet',
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
    }

    public function down(): void
    {
        // Keep production lot and booking history intact when rolling back.
    }
};
