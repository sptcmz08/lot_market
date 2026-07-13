@php
    $layoutPath = resource_path('data/market-layout.json');
    $layout = file_exists($layoutPath) ? json_decode(file_get_contents($layoutPath), true) : ['lots' => []];
    $decorPath = resource_path('data/market-decor.json');
    $decor = file_exists($decorPath) ? json_decode(file_get_contents($decorPath), true) : ['tables' => [], 'labels' => []];
    $lotsByCode = $zones
        ->flatMap(fn ($zone) => $zone->lots)
        ->keyBy('lot_code');

    $cellW = 24;
    $cellH = 18;
    $mapW = (($layout['cols'] ?? 193) * $cellW) + 80;
    $mapH = (($layout['rows'] ?? 99) * $cellH) + 60;
    $tentImg = asset('images/tent.png');
    $xForCol = fn ($col) => max(0, ($col - 1) * $cellW);
    $yForRow = fn ($row) => max(0, ($row - 1) * $cellH);

    $zoneColors = [
        'GB' => '#FFF200', 'GC' => '#FF99FF', 'GD' => '#00B0F0', 'GE' => '#92D050',
        'GF' => '#FFC000', 'GG' => '#5B9BD5', 'GH' => '#7030A0', 'GI' => '#FF0000',
        'GJ' => '#FFC000', 'GL' => '#FFF200', 'GM' => '#92D050', 'GN' => '#00B0F0',
        'GO' => '#FF99FF', 'GP' => '#FF0000', 'GQ' => '#00B050', 'GR' => '#00B0F0',
        'GS' => '#7030A0', 'GT' => '#C65911',
    ];

    $renderLot = function ($layoutLot) use ($lotsByCode, $cellW, $cellH, $tentImg) {
        $code = e($layoutLot['code']);
        $dbLot = $lotsByCode->get($layoutLot['code']);
        $lotId = $dbLot?->id ?? '';
        $displayName = e($dbLot?->display_name ?? $layoutLot['code']);
        $x = max(0, ($layoutLot['excelCol'] - 1) * $cellW);
        $y = max(0, ($layoutLot['excelRow'] - 1) * $cellH);
        $cx = $x + ($cellW / 2);
        $cy = $y + ($cellH / 2);
        $imgX = $x + 1;
        $imgY = $y + 1;
        $imgW = $cellW - 2;
        $imgH = $cellH - 2;

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
                      width="{$cellW}" height="{$cellH}" rx="0.8" />
                <image href="{$tentImg}" x="{$imgX}" y="{$imgY}" width="{$imgW}" height="{$imgH}" preserveAspectRatio="xMidYMid meet" style="pointer-events:none" />
                <rect class="lot-hit-area"
                      x="{$x}" y="{$y}"
                      width="{$cellW}" height="{$cellH}" rx="0.8"
                      fill="transparent" />
            </g>
        SVG;
    };

    $renderZoneLabels = function () use ($layout, $cellW, $cellH, $zoneColors) {
        $seen = [];
        $markup = '';

        foreach ($layout['lots'] ?? [] as $lot) {
            if (!str_starts_with($lot['zone'], 'G')) {
                continue;
            }

            $key = $lot['zone'] . ':' . $lot['excelRow'];
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $x = max(0, ($lot['excelCol'] - 2) * $cellW);
            $y = max(0, ($lot['excelRow'] - 1) * $cellH);
            $color = $zoneColors[$lot['zone']] ?? '#FFF200';
            $zone = e($lot['zone']);
            $cx = $x + ($cellW / 2);
            $cy = $y + ($cellH / 2);

            $markup .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$cellW}\" height=\"{$cellH}\" fill=\"{$color}\" stroke=\"#000\" stroke-width=\"1\" />";
            $markup .= "<text x=\"{$cx}\" y=\"{$cy}\" class=\"excel-zone-label\">{$zone}</text>";
        }

        return $markup;
    };
@endphp

<svg viewBox="0 0 {{ $mapW }} {{ $mapH }}" width="{{ $mapW }}" height="{{ $mapH }}" class="market-svg" id="market-svg-element">
    <defs>
        <pattern id="excel-grid" width="{{ $cellW }}" height="{{ $cellH }}" patternUnits="userSpaceOnUse">
            <path d="M {{ $cellW }} 0 L 0 0 0 {{ $cellH }}" fill="none" stroke="#D9D9D9" stroke-width="1" />
        </pattern>
    </defs>

    <rect x="0" y="0" width="{{ $mapW }}" height="{{ $mapH }}" fill="#FFFFFF" />
    <rect x="0" y="0" width="{{ $mapW }}" height="{{ $mapH }}" fill="url(#excel-grid)" />

    <rect x="0" y="0" width="{{ $mapW }}" height="{{ $cellH * 2.6 }}" fill="#A6A6A6" />
    <rect x="0" y="{{ $cellH * 21.5 }}" width="{{ $mapW }}" height="{{ $cellH * 2.4 }}" fill="#A6A6A6" />
    <rect x="0" y="{{ $cellH * 92 }}" width="{{ $cellW * 160 }}" height="{{ $cellH * 2.7 }}" fill="#A6A6A6" />
    <rect x="0" y="0" width="{{ $cellW * 3.2 }}" height="{{ $cellH * 55 }}" fill="#A6A6A6" />
    <rect x="{{ $cellW * 34.5 }}" y="{{ $cellH * 22 }}" width="{{ $cellW * 2.4 }}" height="{{ $cellH * 34 }}" fill="#A6A6A6" />
    <rect x="{{ $cellW * 70 }}" y="{{ $cellH * 5 }}" width="{{ $cellW * 2.1 }}" height="{{ $cellH * 7 }}" fill="#A6A6A6" opacity="0.55" />
    <rect x="{{ $cellW * 159 }}" y="{{ $cellH * 31 }}" width="{{ $cellW * 2.1 }}" height="{{ $cellH * 19 }}" fill="#A6A6A6" />

    <rect x="0" y="{{ $cellH * 24 }}" width="{{ $cellW * 9 }}" height="{{ $cellH * 68 }}" fill="#A8D08D" />
    <rect x="{{ $cellW * 3.2 }}" y="{{ $cellH * 24 }}" width="{{ $cellW * 6 }}" height="{{ $cellH * 22 }}" fill="#C6E0B4" />
    <rect x="{{ $cellW * 36 }}" y="{{ $cellH * 24 }}" width="{{ $cellW * 28 }}" height="{{ $cellH * 15 }}" fill="#F4A6E8" />
    <rect x="{{ $cellW * 36 }}" y="{{ $cellH * 39 }}" width="{{ $cellW * 28 }}" height="{{ $cellH * 16 }}" fill="#BDD7EE" />

    <polygon points="{{ $cellW * 126 }},{{ $cellH * 12 }} {{ $cellW * 135 }},{{ $cellH * 0 }} {{ $cellW * 144 }},{{ $cellH * 12 }}" fill="#4472C4" stroke="#1F4E79" stroke-width="2" />
    <polygon points="{{ $cellW * 145 }},{{ $cellH * 12 }} {{ $cellW * 154 }},{{ $cellH * 0 }} {{ $cellW * 163 }},{{ $cellH * 12 }}" fill="#4472C4" stroke="#1F4E79" stroke-width="2" />
    <rect x="{{ $cellW * 127 }}" y="{{ $cellH * 12 }}" width="{{ $cellW * 35 }}" height="{{ $cellH * 12 }}" fill="#4472C4" stroke="#1F4E79" stroke-width="2" />

    @foreach(($decor['tables'] ?? []) as $tableCell)
        <rect x="{{ $xForCol($tableCell['col']) }}" y="{{ $yForRow($tableCell['row']) }}" width="{{ $cellW }}" height="{{ $cellH }}" fill="#BDD7EE" stroke="#000" stroke-width="1" />
        <text x="{{ $xForCol($tableCell['col']) + ($cellW / 2) }}" y="{{ $yForRow($tableCell['row']) + ($cellH / 2) }}" class="excel-table-label">โต๊ะ</text>
    @endforeach

    @foreach(($decor['labels'] ?? []) as $label)
        @php
            $x = $xForCol($label['fromCol']);
            $y = $yForRow($label['fromRow']);
            $w = max($cellW * 2, (($label['toCol'] - $label['fromCol'] + 1) * $cellW));
            $h = max($cellH, (($label['toRow'] - $label['fromRow'] + 1) * $cellH));
            $text = e($label['text']);
            $isRoad = str_contains($label['text'], 'ถนน');
        @endphp
        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="{{ $h }}" fill="#FFFFFF" stroke="#BFBFBF" stroke-width="1" />
        <text x="{{ $x + ($w / 2) }}" y="{{ $y + ($h / 2) }}" class="{{ $isRoad ? 'excel-road-label' : 'excel-place-label' }}">{{ $text }}</text>
    @endforeach

    {!! $renderZoneLabels() !!}

    @foreach(collect($layout['lots'] ?? []) as $layoutLot)
        {!! $renderLot($layoutLot) !!}
    @endforeach
</svg>
