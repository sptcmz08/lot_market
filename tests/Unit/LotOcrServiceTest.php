<?php

namespace Tests\Unit;

use App\Services\LotOcrService;
use PHPUnit\Framework\TestCase;

class LotOcrServiceTest extends TestCase
{
    public function test_extracts_and_normalizes_lot_codes_from_ocr_text(): void
    {
        $service = new LotOcrService();

        $codes = $service->extractLotCodes('ป้ายร้าน GL 41 และ GI-12 อยู่ตรงนี้')->all();

        $this->assertSame(['GL41', 'GI12'], $codes);
    }
}
