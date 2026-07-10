<svg viewBox="0 0 1320 620" width="100%" height="100%" class="market-svg" id="market-svg-element">
    @php
        $tentImg = asset('images/tent.png');
        $tentW = 32;
        $tentH = 32;
        $tentGap = 3;
        $blockGap = 18;
        $rowGap = 43;
        $rowStep = $tentH + $rowGap;

        $leftCodes = ['GJ', 'GI', 'GH', 'GG', 'GF', 'GE', 'GD', 'GC', 'GB'];
        $rightCodes = ['GT', 'GS', 'GR', 'GQ', 'GP', 'GO', 'GN', 'GM', 'GL'];

        $renderLots = function ($zone, $startX, $startY) use ($tentImg, $tentW, $tentH, $tentGap, $blockGap) {
            $markup = '';
            foreach ($zone->lots->values() as $index => $lot) {
                $block = intdiv($index, 5);
                $col = $index % 5;
                $tx = $startX + ($block * ((5 * ($tentW + $tentGap)) + $blockGap)) + ($col * ($tentW + $tentGap));
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

    <rect x="18" y="18" width="1284" height="584" rx="18" fill="#FFF9F1" stroke="#F2D6BE" stroke-width="2" />
    <path d="M18 44 H1302" stroke="#DDD6CE" stroke-width="24" opacity="0.55" />
    <path d="M18 576 H1302" stroke="#DDD6CE" stroke-width="24" opacity="0.55" />
    <path d="M700 44 V576" stroke="#DDD6CE" stroke-width="24" opacity="0.55" />

    @foreach($leftCodes as $index => $zCode)
        @php
            $zone = $zones->firstWhere('code', $zCode);
            $y = 74 + ($index * $rowStep);
        @endphp
        @if($zone)
            <text x="52" y="{{ $y + 22 }}" class="zone-label">{{ $zCode }}</text>
            {!! $renderLots($zone, 72, $y) !!}
        @endif
    @endforeach

    <g>
        <rect x="510" y="96" width="170" height="428" fill="none" stroke="#2E7D32" stroke-width="2.5" rx="12" stroke-dasharray="7 5" opacity="0.5" />
        <rect x="516" y="102" width="158" height="416" fill="#E8F5E9" stroke="#81C784" stroke-width="1.5" rx="10" />
        <text x="595" y="314" text-anchor="middle" fill="#1B5E20" font-size="15" font-weight="900" letter-spacing="1">ลานเบียร์ช้าง</text>
        <circle cx="555" cy="205" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="555" cy="205" r="3" fill="#2E7D32" />
        <circle cx="635" cy="205" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="635" cy="205" r="3" fill="#2E7D32" />
        <circle cx="595" cy="260" r="10" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="595" cy="260" r="4" fill="#2E7D32" />
        <circle cx="555" cy="400" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="555" cy="400" r="3" fill="#2E7D32" />
        <circle cx="635" cy="400" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="635" cy="400" r="3" fill="#2E7D32" />
    </g>

    @foreach($rightCodes as $index => $zCode)
        @php
            $zone = $zones->firstWhere('code', $zCode);
            $y = 74 + ($index * $rowStep);
        @endphp
        @if($zone)
            <text x="728" y="{{ $y + 22 }}" class="zone-label">{{ $zCode }}</text>
            {!! $renderLots($zone, 748, $y) !!}
        @endif
    @endforeach
</svg>
