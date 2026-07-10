@php
    $tentImg = asset('images/tent.png');
    $tentW = 24;
    $tentH = 24;
    $tentGap = 2;
    $lotsPerBlock = 5;
    $blockGap = 14;
    $rowGap = 6;
    $groupGap = 20;
    $topPad = 46;
    $bottomPad = 34;
    $leftPad = 32;
    $labelGap = 30;
    $centerGap = 54;
    $centerW = 154;
    $rightGap = 48;
    $roadW = 18;
    $rowStep = $tentH + $rowGap;

    $leftRows = [
        ['GJ'],
        ['GI', 'GH'],
        ['GG', 'GF'],
        ['GE', 'GD'],
        ['GC', 'GB'],
    ];
    $rightRows = [
        ['GT'],
        ['GS', 'GR'],
        ['GQ', 'GP'],
        ['GO', 'GN'],
        ['GM', 'GL'],
    ];

    $zoneLots = function ($zCode) use ($zones) {
        $zone = $zones->firstWhere('code', $zCode);

        return $zone ? $zone->lots->values() : collect();
    };

    $zoneWidth = function ($zCode) use ($zoneLots, $tentW, $tentGap, $lotsPerBlock, $blockGap) {
        $count = $zoneLots($zCode)->count();
        $blocks = $count > 0 ? (int) ceil($count / $lotsPerBlock) : 0;

        return $count > 0 ? ($count * $tentW) + (($count - 1) * $tentGap) + (max(0, $blocks - 1) * $blockGap) : 0;
    };

    $sideWidth = function ($rows) use ($zoneWidth) {
        $width = 0;

        foreach ($rows as $rowCodes) {
            foreach ($rowCodes as $zCode) {
                $width = max($width, $zoneWidth($zCode));
            }
        }

        return $width;
    };

    $rowsHeight = function ($rowGroups) use ($zoneLots, $tentH, $rowGap, $groupGap) {
        $rowCount = 0;
        $groupCount = 0;

        foreach ($rowGroups as $rowCodes) {
            $groupHasLots = false;
            foreach ($rowCodes as $zCode) {
                if ($zoneLots($zCode)->count() > 0) {
                    $rowCount++;
                    $groupHasLots = true;
                }
            }
            if ($groupHasLots) {
                $groupCount++;
            }
        }

        if ($rowCount === 0) {
            return 0;
        }

        return ($rowCount * $tentH) + (($rowCount - $groupCount) * $rowGap) + (max(0, $groupCount - 1) * $groupGap);
    };

    $leftW = $sideWidth($leftRows);
    $rightW = $sideWidth($rightRows);
    $mapH = $topPad + max($rowsHeight($leftRows), $rowsHeight($rightRows), 340) + $bottomPad;
    $leftLabelX = $leftPad;
    $leftX = $leftLabelX + $labelGap;
    $centerX = $leftX + $leftW + $centerGap;
    $roadX = $centerX + $centerW + 18;
    $rightLabelX = $roadX + $roadW + $rightGap;
    $rightX = $rightLabelX + $labelGap;
    $mapW = $rightX + $rightW + 34;
    $contentH = $mapH - $topPad - $bottomPad;
    $yardY = $topPad + 8;
    $yardH = max(315, $contentH - 16);

    $renderZone = function ($zCode, $labelX, $startX, $startY) use ($zoneLots, $tentImg, $tentW, $tentH, $tentGap, $lotsPerBlock, $blockGap) {
        $lots = $zoneLots($zCode);
        if ($lots->count() === 0) {
            return '';
        }

        $zoneLabel = e($zCode);
        $labelY = $startY + ($tentH / 2);
        $markup = '<rect x="' . ($labelX - 17) . '" y="' . ($labelY - 13) . '" width="34" height="26" rx="7" class="zone-label-bg" />';
        $markup .= '<text x="' . $labelX . '" y="' . $labelY . '" class="zone-label">' . $zoneLabel . '</text>';

        foreach ($lots as $index => $lot) {
            $tx = $startX + ($index * ($tentW + $tentGap)) + (intdiv($index, $lotsPerBlock) * $blockGap);
            $ty = $startY;
            $cx = $tx + ($tentW / 2);
            $cy = $ty + ($tentH / 2);
            $code = e($lot->lot_code);
            $displayName = e($lot->display_name ?? $lot->lot_code);

            $markup .= <<<SVG
                <g class="lot-group"
                   data-lot-id="{$lot->id}"
                   data-lot-code="{$code}"
                   data-display-name="{$displayName}"
                   data-cx="{$cx}"
                   data-cy="{$cy}"
                   id="lot-group-{$code}"
                   style="cursor:pointer">
                    <image href="{$tentImg}" x="{$tx}" y="{$ty}" width="{$tentW}" height="{$tentH}" preserveAspectRatio="xMidYMid meet" style="pointer-events:none" />
                    <rect class="market-lot lot-available"
                          id="lot-{$code}"
                          data-lot-id="{$lot->id}"
                          data-lot-code="{$code}"
                          data-display-name="{$displayName}"
                          data-cx="{$cx}" data-cy="{$cy}"
                          x="{$tx}" y="{$ty}"
                          width="{$tentW}" height="{$tentH}" rx="5"
                          opacity="0.42" />
                    <rect class="lot-hit-area"
                          x="{$tx}" y="{$ty}"
                          width="{$tentW}" height="{$tentH}" rx="5"
                          fill="transparent" />
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
    @foreach($leftRows as $rowCodes)
        @php $groupHasLots = false; @endphp
        @foreach($rowCodes as $zCode)
            @if($zoneLots($zCode)->count() > 0)
                {!! $renderZone($zCode, $leftLabelX, $leftX, $currentY) !!}
                @php
                    $currentY += $rowStep;
                    $groupHasLots = true;
                @endphp
            @endif
        @endforeach
        @if($groupHasLots)
            @php $currentY += $groupGap - $rowGap; @endphp
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
    @foreach($rightRows as $rowCodes)
        @php $groupHasLots = false; @endphp
        @foreach($rowCodes as $zCode)
            @if($zoneLots($zCode)->count() > 0)
                {!! $renderZone($zCode, $rightLabelX, $rightX, $currentY) !!}
                @php
                    $currentY += $rowStep;
                    $groupHasLots = true;
                @endphp
            @endif
        @endforeach
        @if($groupHasLots)
            @php $currentY += $groupGap - $rowGap; @endphp
        @endif
    @endforeach
</svg>
