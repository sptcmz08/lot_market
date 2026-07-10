@php
    $tentImg = asset('images/tent.png');
    $tentW = 32;
    $tentH = 32;
    $tentGap = 3;
    $groupGap = 34;
    $topPad = 58;
    $bottomPad = 42;
    $leftPad = 36;
    $labelGap = 22;
    $centerGap = 74;
    $centerW = 178;
    $rightGap = 64;
    $roadW = 22;
    $rowStep = $tentH + $groupGap;

    $leftGroups = [
        ['GJ'],
        ['GI', 'GH'],
        ['GG', 'GF'],
        ['GE', 'GD'],
        ['GC', 'GB'],
    ];
    $rightGroups = [
        ['GT'],
        ['GS', 'GR'],
        ['GQ', 'GP'],
        ['GO', 'GN'],
        ['GM', 'GL'],
    ];

    $groupLots = function ($groupCodes) use ($zones) {
        $lots = collect();

        foreach ($groupCodes as $zCode) {
            $zone = $zones->firstWhere('code', $zCode);
            if ($zone) {
                $lots = $lots->concat($zone->lots);
            }
        }

        return $lots->values();
    };

    $groupWidth = function ($groupCodes) use ($groupLots, $tentW, $tentGap) {
        $count = $groupLots($groupCodes)->count();

        return $count > 0 ? ($count * $tentW) + (($count - 1) * $tentGap) : 0;
    };

    $sideWidth = function ($groups) use ($groupWidth) {
        $width = 0;

        foreach ($groups as $groupCodes) {
            $width = max($width, $groupWidth($groupCodes));
        }

        return $width;
    };

    $groupsHeight = function ($groups) use ($groupLots, $tentH, $groupGap) {
        $rows = 0;

        foreach ($groups as $groupCodes) {
            if ($groupLots($groupCodes)->count() > 0) {
                $rows++;
            }
        }

        return $rows > 0 ? ($rows * $tentH) + (($rows - 1) * $groupGap) : 0;
    };

    $leftW = $sideWidth($leftGroups);
    $rightW = $sideWidth($rightGroups);
    $mapH = $topPad + max($groupsHeight($leftGroups), $groupsHeight($rightGroups), 430) + $bottomPad;
    $leftLabelX = $leftPad;
    $leftX = $leftLabelX + $labelGap;
    $centerX = $leftX + $leftW + $centerGap;
    $roadX = $centerX + $centerW + 18;
    $rightLabelX = $roadX + $roadW + $rightGap;
    $rightX = $rightLabelX + $labelGap;
    $mapW = $rightX + $rightW + 42;
    $contentH = $mapH - $topPad - $bottomPad;
    $yardY = $topPad + 10;
    $yardH = max(390, $contentH - 20);

    $renderGroup = function ($groupCodes, $labelX, $startX, $startY) use ($groupLots, $tentImg, $tentW, $tentH, $tentGap) {
        $lots = $groupLots($groupCodes);
        if ($lots->count() === 0) {
            return '';
        }

        $groupLabel = e(implode(' ', $groupCodes));
        $labelY = $startY + ($tentH / 2);
        $markup = '<text x="' . $labelX . '" y="' . $labelY . '" class="zone-label">' . $groupLabel . '</text>';

        foreach ($lots as $index => $lot) {
            $tx = $startX + ($index * ($tentW + $tentGap));
            $ty = $startY;
            $cx = $tx + ($tentW / 2);
            $cy = $ty + ($tentH / 2);
            $code = e($lot->lot_code);
            $displayName = e($lot->display_name ?? $lot->lot_code);

            $markup .= <<<SVG
                <g class="lot-group" data-lot-code="{$code}" id="lot-group-{$code}" style="cursor:pointer">
                    <image href="{$tentImg}" x="{$tx}" y="{$ty}" width="{$tentW}" height="{$tentH}" preserveAspectRatio="xMidYMid meet" />
                    <rect class="market-lot lot-available"
                          id="lot-{$code}"
                          data-lot-id="{$lot->id}"
                          data-lot-code="{$code}"
                          data-display-name="{$displayName}"
                          data-cx="{$cx}" data-cy="{$cy}"
                          x="{$tx}" y="{$ty}"
                          width="{$tentW}" height="{$tentH}" rx="5"
                          opacity="0.42" />
                </g>
            SVG;
        }

        return $markup;
    };
@endphp

<svg viewBox="0 0 {{ $mapW }} {{ $mapH }}" width="{{ $mapW }}" height="{{ $mapH }}" class="market-svg" id="market-svg-element">
    <rect x="18" y="18" width="{{ $mapW - 36 }}" height="{{ $mapH - 36 }}" rx="18" fill="#FFF9F1" stroke="#F2D6BE" stroke-width="2" />
    <path d="M18 44 H{{ $mapW - 18 }}" stroke="#DDD6CE" stroke-width="22" opacity="0.55" />
    <path d="M18 {{ $mapH - 44 }} H{{ $mapW - 18 }}" stroke="#DDD6CE" stroke-width="22" opacity="0.55" />
    <path d="M{{ $roadX + ($roadW / 2) }} 44 V{{ $mapH - 44 }}" stroke="#DDD6CE" stroke-width="{{ $roadW }}" opacity="0.55" />

    @php $currentY = $topPad; @endphp
    @foreach($leftGroups as $groupCodes)
        @if($groupLots($groupCodes)->count() > 0)
            {!! $renderGroup($groupCodes, $leftLabelX, $leftX, $currentY) !!}
            @php $currentY += $rowStep; @endphp
        @endif
    @endforeach

    <g>
        <rect x="{{ $centerX }}" y="{{ $yardY - 6 }}" width="{{ $centerW }}" height="{{ $yardH + 12 }}" fill="none" stroke="#2E7D32" stroke-width="2.5" rx="12" stroke-dasharray="7 5" opacity="0.5" />
        <rect x="{{ $centerX + 6 }}" y="{{ $yardY }}" width="{{ $centerW - 12 }}" height="{{ $yardH }}" fill="#E8F5E9" stroke="#81C784" stroke-width="1.5" rx="10" />
        <text x="{{ $centerX + ($centerW / 2) }}" y="{{ $yardY + ($yardH / 2) + 10 }}" text-anchor="middle" fill="#1B5E20" font-size="15" font-weight="900" letter-spacing="1">ลานเบียร์ช้าง</text>
        <circle cx="{{ $centerX + 45 }}" cy="{{ $yardY + 95 }}" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="{{ $centerX + 45 }}" cy="{{ $yardY + 95 }}" r="3" fill="#2E7D32" />
        <circle cx="{{ $centerX + $centerW - 45 }}" cy="{{ $yardY + 95 }}" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="{{ $centerX + $centerW - 45 }}" cy="{{ $yardY + 95 }}" r="3" fill="#2E7D32" />
        <circle cx="{{ $centerX + ($centerW / 2) }}" cy="{{ $yardY + ($yardH / 2) - 35 }}" r="10" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="{{ $centerX + ($centerW / 2) }}" cy="{{ $yardY + ($yardH / 2) - 35 }}" r="4" fill="#2E7D32" />
        <circle cx="{{ $centerX + 45 }}" cy="{{ $yardY + $yardH - 95 }}" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="{{ $centerX + 45 }}" cy="{{ $yardY + $yardH - 95 }}" r="3" fill="#2E7D32" />
        <circle cx="{{ $centerX + $centerW - 45 }}" cy="{{ $yardY + $yardH - 95 }}" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="{{ $centerX + $centerW - 45 }}" cy="{{ $yardY + $yardH - 95 }}" r="3" fill="#2E7D32" />
    </g>

    @php $currentY = $topPad; @endphp
    @foreach($rightGroups as $groupCodes)
        @if($groupLots($groupCodes)->count() > 0)
            {!! $renderGroup($groupCodes, $rightLabelX, $rightX, $currentY) !!}
            @php $currentY += $rowStep; @endphp
        @endif
    @endforeach
</svg>
