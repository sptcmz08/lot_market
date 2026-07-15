<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\SimpleXlsxWriter;
use App\Services\StatusLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $selectedDate = Carbon::createFromFormat('Y-m-d', $request->query('date', now()->format('Y-m-d')))
                ->format('Y-m-d');
        } catch (\Throwable) {
            $selectedDate = now()->format('Y-m-d');
        }

        // Booking status aggregates for today/general
        $stats = [
            'pending' => Booking::where('status', 'pending_admin')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'assigned' => Booking::where('status', 'assigned')->count(),
            'installing' => Booking::where('status', 'installing')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'problem' => Booking::where('status', 'problem')->count(),
        ];

        $todayBookings = Booking::whereDate('use_date', $selectedDate)
            ->with(['lots', 'deliveryTasks.staff', 'frontStoreCollectedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTodayBookings = $todayBookings->where('status', '!=', 'cancelled');
        $frontStoreBookings = $activeTodayBookings
            ->where('collect_front_store', true)
            ->values();
        $dailySummary = [
            'bookings' => $activeTodayBookings->count(),
            'lots' => $activeTodayBookings->sum(fn (Booking $booking) => $booking->lots->count()),
            'front_store_pending' => $frontStoreBookings->whereNull('front_store_collected_at')->count(),
            'front_store_collected_amount' => $frontStoreBookings
                ->whereNotNull('front_store_collected_at')
                ->sum(fn (Booking $booking) => (float) $booking->front_store_collected_amount),
        ];

        // Staff count
        $staffCount = User::where('role', 'staff')->where('is_active', true)->count();

        return view('admin.dashboard', compact(
            'stats',
            'todayBookings',
            'frontStoreBookings',
            'dailySummary',
            'staffCount',
            'selectedDate'
        ));
    }

    public function collectFrontStore(Request $request, Booking $booking)
    {
        if (!$booking->collect_front_store) {
            return back()->with('error', 'รายการนี้ไม่ได้เลือกเก็บเงินหน้าร้าน');
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'ไม่สามารถบันทึกยอดของรายการที่ยกเลิกแล้ว');
        }

        $validated = $request->validate([
            'front_store_collected_amount' => 'required|numeric|min:0.01|max:99999999.99',
        ], [
            'front_store_collected_amount.required' => 'กรุณากรอกยอดที่เก็บหน้าร้าน',
            'front_store_collected_amount.numeric' => 'ยอดเก็บหน้าร้านต้องเป็นตัวเลข',
            'front_store_collected_amount.min' => 'ยอดเก็บหน้าร้านต้องมากกว่า 0 บาท',
            'front_store_collected_amount.max' => 'ยอดเก็บหน้าร้านสูงเกินขอบเขตที่ระบบรองรับ',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $wasCollected = $booking->front_store_collected_at !== null;

            $booking->update([
                'front_store_collected_amount' => $validated['front_store_collected_amount'],
                'front_store_collected_at' => now(),
                'front_store_collected_by' => auth()->id(),
            ]);

            StatusLogService::log(
                Booking::class,
                $booking->id,
                $booking->status,
                $booking->status,
                auth()->id(),
                ($wasCollected ? 'แก้ไขยอดเก็บหน้าร้านเป็น ' : 'บันทึกเก็บเงินหน้าร้าน ') .
                    number_format((float) $validated['front_store_collected_amount'], 2) . ' บาท'
            );
        });

        return back()->with('success', 'บันทึกยอดเก็บหน้าร้านเรียบร้อยแล้ว');
    }

    public function exportFrontStore(Request $request, SimpleXlsxWriter $writer)
    {
        try {
            $selectedDate = Carbon::createFromFormat('Y-m-d', $request->query('date', now()->format('Y-m-d')))
                ->format('Y-m-d');
        } catch (\Throwable) {
            $selectedDate = now()->format('Y-m-d');
        }

        $bookings = Booking::whereDate('use_date', $selectedDate)
            ->where('collect_front_store', true)
            ->where('status', '!=', 'cancelled')
            ->with(['lots', 'deliveryTasks.staff', 'frontStoreCollectedBy'])
            ->orderBy('shop_name')
            ->get();

        $rows = [
            ['รายการเก็บเงินหน้าร้าน วันที่ '.Carbon::parse($selectedDate)->format('d/m/Y'), '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['ลำดับ', 'รหัสจอง', 'วันที่ใช้', 'เลข LOT', 'ร้านค้า', 'เบอร์โทร', 'งานเต็นท์', 'คนส่งเต็นท์', 'งานเคาน์เตอร์', 'คนส่งเคาน์เตอร์', 'อุปกรณ์อื่น', 'คนส่งอุปกรณ์อื่น', 'สถานะเก็บเงิน', 'ยอดเงิน (บาท)', 'ผู้บันทึก / เวลา'],
        ];

        foreach ($bookings as $index => $booking) {
            $tasks = $booking->deliveryTasks->keyBy('task_type');
            $rows[] = [
                $index + 1,
                $booking->booking_code,
                $booking->use_date->format('d/m/Y'),
                $booking->lots->pluck('lot_code')->implode(', '),
                $booking->shop_name,
                $booking->customer_phone,
                $tasks->get('tent')?->equipmentSummary() ?? '-',
                $tasks->get('tent')?->staff?->name ?? '',
                $tasks->get('counter')?->equipmentSummary() ?? '-',
                $tasks->get('counter')?->staff?->name ?? '',
                $tasks->get('other')?->equipmentSummary() ?? '-',
                $tasks->get('other')?->staff?->name ?? '',
                $booking->front_store_collected_at ? 'เก็บแล้ว' : 'รอเก็บ',
                $booking->front_store_collected_at ? (float) $booking->front_store_collected_amount : '',
                $booking->front_store_collected_at
                    ? trim(($booking->frontStoreCollectedBy?->name ?? '-').' '.$booking->front_store_collected_at->format('d/m/Y H:i').' น.')
                    : '',
            ];
        }

        $rows[] = ['', '', '', '', '', '', '', '', '', '', '', '', 'รวมยอดที่เก็บแล้ว', (float) $bookings->whereNotNull('front_store_collected_at')->sum('front_store_collected_amount'), ''];

        $path = $writer->create($rows, [8, 24, 14, 24, 24, 16, 26, 20, 30, 20, 26, 20, 18, 18, 30], [13]);

        return response()->download(
            $path,
            'front-store-collection-'.$selectedDate.'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
}
