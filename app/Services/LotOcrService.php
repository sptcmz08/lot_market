<?php

namespace App\Services;

use App\Models\DeliveryTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class LotOcrService
{
    public function verifyPhoto(DeliveryTask $task, string $imagePath): array
    {
        $expectedLots = $this->expectedLots($task);
        $text = $this->readText($imagePath);
        $detectedLots = $this->extractLotCodes($text);
        $matchedLots = $detectedLots->intersect($expectedLots)->values();

        if ($matchedLots->isNotEmpty()) {
            return [
                'status' => 'matched',
                'text' => $text,
                'confidence' => 100,
                'detected_lots' => $detectedLots->all(),
                'matched_lots' => $matchedLots->all(),
                'expected_lots' => $expectedLots->all(),
            ];
        }

        return [
            'status' => $detectedLots->isEmpty() ? 'failed' : 'mismatch',
            'text' => $text,
            'confidence' => 0,
            'detected_lots' => $detectedLots->all(),
            'matched_lots' => [],
            'expected_lots' => $expectedLots->all(),
        ];
    }

    public function hasPassingLotPhoto(DeliveryTask $task): bool
    {
        return $task->photos()
            ->where('photo_type', 'lot_number')
            ->where('ocr_status', 'matched')
            ->exists();
    }

    public function expectedLots(DeliveryTask $task): Collection
    {
        $task->loadMissing('booking.lots');

        return $task->booking
            ? $task->booking->lots->pluck('lot_code')->map(fn ($code) => $this->normalizeLotCode($code))->unique()->values()
            : collect();
    }

    public function extractLotCodes(string $text): Collection
    {
        preg_match_all('/[A-Z]{1,3}\s*-?\s*\d{1,3}/i', $text, $matches);

        return collect($matches[0] ?? [])
            ->map(fn ($code) => $this->normalizeLotCode($code))
            ->filter()
            ->unique()
            ->values();
    }

    private function readText(string $imagePath): string
    {
        $absolutePath = Storage::disk('public')->path($imagePath);
        $binary = config('services.ocr.tesseract_path', 'tesseract');
        $language = config('services.ocr.language', 'eng');
        $timeout = (int) config('services.ocr.timeout', 20);

        if (!is_file($absolutePath)) {
            return '';
        }

        $process = new Process([$binary, $absolutePath, 'stdout', '--psm', '6', '-l', $language]);
        $process->setTimeout($timeout);

        try {
            $process->run();
        } catch (\Throwable $e) {
            return 'OCR engine unavailable: ' . $e->getMessage();
        }

        if (!$process->isSuccessful()) {
            return trim($process->getErrorOutput());
        }

        return trim($process->getOutput());
    }

    private function normalizeLotCode(string $code): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $code));
    }
}
