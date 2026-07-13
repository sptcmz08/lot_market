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
    $emuToSvg = fn ($emu) => $emu / 9525;

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
                <rect class="lot-cell-border"
                      x="{$x}" y="{$y}"
                      width="{$cellW}" height="{$cellH}" rx="0" />
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

    @foreach(($decor['cells'] ?? []) as $cell)
        <rect x="{{ $xForCol($cell['col']) }}" y="{{ $yForRow($cell['row']) }}" width="{{ $cellW }}" height="{{ $cellH }}" fill="{{ $cell['fill'] }}" stroke="#D9D9D9" stroke-width="1" />
    @endforeach

    <rect x="0" y="0" width="{{ $mapW }}" height="{{ $mapH }}" fill="url(#excel-grid)" opacity="0.82" pointer-events="none" />

    <polygon points="{{ $cellW * 126 }},{{ $cellH * 12 }} {{ $cellW * 135 }},{{ $cellH * 0 }} {{ $cellW * 144 }},{{ $cellH * 12 }}" fill="#4472C4" stroke="#1F4E79" stroke-width="2" />
    <polygon points="{{ $cellW * 145 }},{{ $cellH * 12 }} {{ $cellW * 154 }},{{ $cellH * 0 }} {{ $cellW * 163 }},{{ $cellH * 12 }}" fill="#4472C4" stroke="#1F4E79" stroke-width="2" />
    <rect x="{{ $cellW * 127 }}" y="{{ $cellH * 12 }}" width="{{ $cellW * 35 }}" height="{{ $cellH * 12 }}" fill="#4472C4" stroke="#1F4E79" stroke-width="2" />

    @foreach(($decor['tables'] ?? []) as $tableCell)
        <rect x="{{ $xForCol($tableCell['col']) }}" y="{{ $yForRow($tableCell['row']) }}" width="{{ $cellW }}" height="{{ $cellH }}" fill="#BDD7EE" stroke="#000" stroke-width="1" />
        <text x="{{ $xForCol($tableCell['col']) + ($cellW / 2) }}" y="{{ $yForRow($tableCell['row']) + ($cellH / 2) }}" class="excel-table-label">โต๊ะ</text>
    @endforeach

    @foreach(($decor['labels'] ?? []) as $label)
        @php
            $x = $xForCol($label['fromCol']) + $emuToSvg($label['fromColOffset'] ?? 0);
            $y = $yForRow($label['fromRow']) + $emuToSvg($label['fromRowOffset'] ?? 0);
            $x2 = $xForCol($label['toCol']) + $emuToSvg($label['toColOffset'] ?? 0);
            $y2 = $yForRow($label['toRow']) + $emuToSvg($label['toRowOffset'] ?? 0);
            $w = max($cellW * 1.8, $x2 - $x);
            $h = max($cellH * 0.9, $y2 - $y);
            $text = e($label['text']);
            $isRoad = str_contains($label['text'], 'ถนน');
            $isVertical = $h > ($w * 1.25);
            $boxClass = match (true) {
                str_contains($label['text'], 'ซุ้ม') => 'excel-label-box excel-label-booth',
                str_contains($label['text'], 'ศาล') => 'excel-label-box excel-label-shrine',
                str_contains($label['text'], 'ร้านน้ำแข็ง') => 'excel-label-box excel-label-ice',
                default => 'excel-label-box',
            };
            $textClass = $isRoad ? 'excel-road-label' : 'excel-place-label';
            $textTransform = $isVertical ? ' transform="rotate(-90 ' . ($x + ($w / 2)) . ' ' . ($y + ($h / 2)) . ')"' : '';
        @endphp
        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="{{ $h }}" class="{{ $boxClass }}" />
        <text x="{{ $x + ($w / 2) }}" y="{{ $y + ($h / 2) }}" class="{{ $textClass }}"{!! $textTransform !!}>{{ $text }}</text>
    @endforeach

    {!! $renderZoneLabels() !!}

    @foreach(collect($layout['lots'] ?? []) as $layoutLot)
        {!! $renderLot($layoutLot) !!}
    @endforeach
</svg>
