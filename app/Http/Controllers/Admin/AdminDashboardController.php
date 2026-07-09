<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');

        // Booking status aggregates for today/general
        $stats = [
            'pending' => Booking::where('status', 'pending_admin')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'assigned' => Booking::where('status', 'assigned')->count(),
            'installing' => Booking::where('status', 'installing')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'problem' => Booking::where('status', 'problem')->count(),
        ];

        // Quick list of active bookings today
        $todayBookings = Booking::where('use_date', $today)
            ->with(['lots', 'deliveryTask.staff'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Staff count
        $staffCount = User::where('role', 'staff')->where('is_active', true)->count();

        return view('admin.dashboard', compact('stats', 'todayBookings', 'staffCount', 'today'));
    }
}
