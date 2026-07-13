@php
    $layoutPath = resource_path('data/market-layout.json');
    $layout = file_exists($layoutPath) ? json_decode(file_get_contents($layoutPath), true) : ['rows' => 1, 'cols' => 1, 'lots' => []];
    $lotsByCode = $zones
        ->flatMap(fn ($zone) => $zone->lots)
        ->keyBy('lot_code');

    $cellW = 15;
    $cellH = 12;
    $leftPad = 42;
    $topPad = 28;
    $rightPad = 42;
    $bottomPad = 34;
    $mapW = $leftPad + (($layout['cols'] ?? 1) * $cellW) + $rightPad;
    $mapH = $topPad + (($layout['rows'] ?? 1) * $cellH) + $bottomPad;

    $zoneColors = [
        'GB' => '#FFF06A', 'GC' => '#6EE7B7', 'GD' => '#C4B5FD', 'GE' => '#93C5FD',
        'GF' => '#F9A8D4', 'GG' => '#FDA4AF', 'GH' => '#A78BFA', 'GI' => '#60A5FA',
        'GJ' => '#FBBF24', 'GL' => '#FFF06A', 'GM' => '#6EE7B7', 'GN' => '#C4B5FD',
        'GO' => '#F9A8D4', 'GP' => '#FDA4AF', 'GQ' => '#A78BFA', 'GR' => '#60A5FA',
        'GS' => '#3B82F6', 'GT' => '#F97316', 'W' => '#E5E7EB', 'X' => '#E5E7EB',
        'Y' => '#E5E7EB', 'Z' => '#E5E7EB',
    ];

    $lots = collect($layout['lots'] ?? []);
    $rowsByZone = $lots->groupBy(fn ($lot) => $lot['zone'] . ':' . $lot['excelRow']);

    $renderLot = function ($layoutLot) use ($lotsByCode, $cellW, $cellH, $leftPad, $topPad) {
        $code = e($layoutLot['code']);
        $label = e($layoutLot['label'] ?? $layoutLot['code']);
        $dbLot = $lotsByCode->get($layoutLot['code']);
        $lotId = $dbLot?->id ?? '';
        $displayName = e($dbLot?->display_name ?? $layoutLot['code']);
        $x = $leftPad + (($layoutLot['excelCol'] - 1) * $cellW);
        $y = $topPad + (($layoutLot['excelRow'] - 1) * $cellH);
        $cx = $x + ($cellW / 2);
        $cy = $y + ($cellH / 2);

        return <<<SVG
            <g class="lot-group"
               data-lot-id="{$lotId}"
               data-lot-code="{$code}"
               data-display-name="{$displayName}"
               data-cx="{$cx}"
               data-cy="{$cy}"
               id="lot-group-{$code}"
               style="cursor:pointer">
                <rect class="market-lot lot-available"
                      id="lot-{$code}"
                      data-lot-id="{$lotId}"
                      data-lot-code="{$code}"
                      data-display-name="{$displayName}"
                      data-cx="{$cx}"
                      data-cy="{$cy}"
                      x="{$x}" y="{$y}"
                      width="{$cellW}" height="{$cellH}" rx="1.4" />
                <text class="lot-cell-text" x="{$cx}" y="{$cy}">{$label}</text>
                <rect class="lot-hit-area"
                      x="{$x}" y="{$y}"
                      width="{$cellW}" height="{$cellH}" rx="1.4"
                      fill="transparent" />
            </g>
        SVG;
    };
@endphp

<svg viewBox="0 0 {{ $mapW }} {{ $mapH }}" width="{{ $mapW }}" height="{{ $mapH }}" class="market-svg" id="market-svg-element">
    <rect x="1" y="1" width="{{ $mapW - 2 }}" height="{{ $mapH - 2 }}" rx="12" fill="#FDFDFB" stroke="#D4D4D8" stroke-width="1.5" />

    @foreach($rowsByZone as $rowKey => $rowLots)
        @php
            [$zoneCode, $excelRow] = explode(':', $rowKey);
            $firstLot = $rowLots->sortBy('excelCol')->first();
            $labelX = $leftPad + (($firstLot['excelCol'] - 2) * $cellW);
            $labelY = $topPad + (($firstLot['excelRow'] - 1) * $cellH);
            $labelColor = $zoneColors[$zoneCode] ?? '#F4F4F5';
        @endphp
        @if(str_starts_with($zoneCode, 'G'))
            <rect x="{{ $labelX }}" y="{{ $labelY }}" width="{{ $cellW }}" height="{{ $cellH }}" rx="1.5" fill="{{ $labelColor }}" stroke="#71717A" stroke-width="0.5" />
            <text x="{{ $labelX + ($cellW / 2) }}" y="{{ $labelY + ($cellH / 2) }}" class="zone-cell-text">{{ $zoneCode }}</text>
        @endif
    @endforeach

    @foreach($lots as $layoutLot)
        {!! $renderLot($layoutLot) !!}
    @endforeach
</svg>
