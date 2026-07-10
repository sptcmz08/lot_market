@php
    $tentImg = asset('images/tent.png');
    $tentW = 32;
    $tentH = 32;
    $tentGap = 3;
    $rowGap = 8;
    $zoneGap = 2;
    $groupGap = 24;
    $topPad = 58;
    $bottomPad = 42;
    $leftPad = 34;
    $labelGap = 18;
    $centerGap = 74;
    $centerW = 178;
    $rightGap = 64;
    $roadW = 22;
    $lotsPerRow = 5;
    $rowStep = $tentH + $rowGap;
    $rowW = ($lotsPerRow * $tentW) + (($lotsPerRow - 1) * $tentGap);

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

    $zoneRows = function ($zone) use ($lotsPerRow) {
        if (!$zone) {
            return 0;
        }

        return max(1, (int) ceil($zone->lots->count() / $lotsPerRow));
    };

    $zoneHeight = function ($zone) use ($zoneRows, $tentH, $rowStep) {
        $rows = $zoneRows($zone);

        return $rows > 0 ? (($rows - 1) * $rowStep) + $tentH : 0;
    };

    $groupsHeight = function ($groups) use ($zones, $zoneHeight, $zoneGap, $groupGap) {
        $height = 0;
        foreach ($groups as $groupIndex => $groupCodes) {
            $groupHeight = 0;
            foreach ($groupCodes as $zoneIndex => $zCode) {
                $zone = $zones->firstWhere('code', $zCode);
                if (!$zone) {
                    continue;
                }

                if ($groupHeight > 0) {
                    $groupHeight += $zoneGap;
                }
                $groupHeight += $zoneHeight($zone);
            }

            if ($groupHeight === 0) {
                continue;
            }

            if ($height > 0) {
                $height += $groupGap;
            }
            $height += $groupHeight;
        }

        return $height;
    };

    $mapH = $topPad + max($groupsHeight($leftGroups), $groupsHeight($rightGroups), 430) + $bottomPad;
    $leftLabelX = $leftPad;
    $leftX = $leftLabelX + $labelGap;
    $centerX = $leftX + $rowW + $centerGap;
    $roadX = $centerX + $centerW + 18;
    $rightLabelX = $roadX + $roadW + $rightGap;
    $rightX = $rightLabelX + $labelGap;
    $mapW = $rightX + $rowW + 42;
    $contentH = $mapH - $topPad - $bottomPad;
    $yardY = $topPad + 10;
    $yardH = max(390, $contentH - 20);

    $renderZone = function ($zone, $labelX, $startX, $startY) use ($tentImg, $tentW, $tentH, $tentGap, $rowStep, $lotsPerRow, $zoneHeight) {
        if (!$zone) {
            return '';
        }

        $zoneCode = e($zone->code);
        $labelY = $startY + ($zoneHeight($zone) / 2);
        $markup = '<text x="' . $labelX . '" y="' . $labelY . '" class="zone-label">' . $zoneCode . '</text>';

        foreach ($zone->lots->values() as $index => $lot) {
            $row = intdiv($index, $lotsPerRow);
            $col = $index % $lotsPerRow;
            $tx = $startX + ($col * ($tentW + $tentGap));
            $ty = $startY + ($row * $rowStep);
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
        @php $groupRendered = false; @endphp
        @foreach($groupCodes as $zCode)
            @php
                $zone = $zones->firstWhere('code', $zCode);
                $height = $zoneHeight($zone);
            @endphp
            @if($zone)
                {!! $renderZone($zone, $leftLabelX, $leftX, $currentY) !!}
                @php
                    $currentY += $height + $zoneGap;
                    $groupRendered = true;
                @endphp
            @endif
        @endforeach
        @if($groupRendered)
            @php $currentY += $groupGap - $zoneGap; @endphp
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
        @php $groupRendered = false; @endphp
        @foreach($groupCodes as $zCode)
            @php
                $zone = $zones->firstWhere('code', $zCode);
                $height = $zoneHeight($zone);
            @endphp
            @if($zone)
                {!! $renderZone($zone, $rightLabelX, $rightX, $currentY) !!}
                @php
                    $currentY += $height + $zoneGap;
                    $groupRendered = true;
                @endphp
            @endif
        @endforeach
        @if($groupRendered)
            @php $currentY += $groupGap - $zoneGap; @endphp
        @endif
    @endforeach
</svg>
