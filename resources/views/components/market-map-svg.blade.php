@php
    $layoutPath = resource_path('data/market-layout.json');
    $layout = file_exists($layoutPath) ? json_decode(file_get_contents($layoutPath), true) : ['lots' => []];
    $lotsByCode = $zones
        ->flatMap(fn ($zone) => $zone->lots)
        ->keyBy('lot_code');

    $mapW = 4828;
    $mapH = 1854;
    $cellW = 24;
    $cellH = 18;
    $background = asset('images/market-layout-excel.png');

    $renderLot = function ($layoutLot) use ($lotsByCode, $cellW, $cellH) {
        $code = e($layoutLot['code']);
        $dbLot = $lotsByCode->get($layoutLot['code']);
        $lotId = $dbLot?->id ?? '';
        $displayName = e($dbLot?->display_name ?? $layoutLot['code']);
        $x = max(0, ($layoutLot['excelCol'] - 1) * $cellW);
        $y = max(0, ($layoutLot['excelRow'] - 1) * $cellH);
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
                      width="{$cellW}" height="{$cellH}" rx="0.8" />
                <rect class="lot-hit-area"
                      x="{$x}" y="{$y}"
                      width="{$cellW}" height="{$cellH}" rx="0.8"
                      fill="transparent" />
            </g>
        SVG;
    };
@endphp

<svg viewBox="0 0 {{ $mapW }} {{ $mapH }}" width="{{ $mapW }}" height="{{ $mapH }}" class="market-svg" id="market-svg-element">
    <image href="{{ $background }}" x="0" y="0" width="{{ $mapW }}" height="{{ $mapH }}" preserveAspectRatio="xMinYMin meet" />

    @foreach(collect($layout['lots'] ?? []) as $layoutLot)
        {!! $renderLot($layoutLot) !!}
    @endforeach
</svg>
