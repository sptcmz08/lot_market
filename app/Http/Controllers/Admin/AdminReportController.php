<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->query('year', now()->format('Y'));
        $month = $request->query('month', now()->format('m'));

        // Monthly breakdown
        $monthlyReport = Booking::select(
                DB::raw('DATE(use_date) as date'),
                DB::raw('count(*) as total_bookings'),
                DB::raw("sum(case when status = 'completed' then 1 else 0 end) as completed_bookings"),
                DB::raw("sum(case when status = 'problem' then 1 else 0 end) as problem_bookings"),
                DB::raw("sum(case when status = 'cancelled' then 1 else 0 end) as cancelled_bookings")
            )
            ->whereYear('use_date', $year)
            ->whereMonth('use_date', $month)
            ->groupBy(DB::raw('DATE(use_date)'))
            ->orderBy('date', 'asc')
            ->get();

        // Status summary for selected month
        $monthStats = [
            'total' => Booking::whereYear('use_date', $year)->whereMonth('use_date', $month)->count(),
            'completed' => Booking::whereYear('use_date', $year)->whereMonth('use_date', $month)->where('status', 'completed')->count(),
            'problem' => Booking::whereYear('use_date', $year)->whereMonth('use_date', $month)->where('status', 'problem')->count(),
            'cancelled' => Booking::whereYear('use_date', $year)->whereMonth('use_date', $month)->where('status', 'cancelled')->count(),
        ];

        // Overall stats group by month
        $yearlyReport = Booking::select(
                DB::raw("strftime('%m', use_date) as month_num"), // SQLite date format compatible
                DB::raw('count(*) as total_bookings'),
                DB::raw("sum(case when status = 'completed' then 1 else 0 end) as completed_bookings")
            )
            ->whereYear('use_date', $year)
            ->groupBy('month_num')
            ->orderBy('month_num', 'asc')
            ->get();

        return view('admin.reports.index', compact('monthlyReport', 'yearlyReport', 'monthStats', 'year', 'month'));
    }
}
