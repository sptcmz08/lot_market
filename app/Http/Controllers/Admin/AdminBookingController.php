<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\Lot;
use App\Models\User;
use App\Services\StatusLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['lots', 'deliveryTask.staff']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('shop_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('lots', function ($l) use ($search) {
                      $l->where('lot_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('use_date', $request->date);
        }

        $bookings = $query->orderBy('use_date', 'desc')->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['lots', 'deliveryTask.staff', 'deliveryTask.photos']);
        $staffMembers = User::where('role', 'staff')->where('is_active', true)->get();
        
        // Fetch status logs
        $logs = \App\Models\StatusLog::where('loggable_type', Booking::class)
            ->where('loggable_id', $booking->id)
            ->with('changedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bookings.show', compact('booking', 'staffMembers', 'logs'));
    }

    public function edit(Booking $booking)
    {
        $booking->load('lots');
        $allLots = Lot::where('is_active', true)->get();
        $tentSizes = ['1.5', '2x2', '2x3', '2.5', '3x4.5'];
        $counterSizes = ['ไม่มีเคาน์เตอร์', '1 ล็อค', '2 ล็อค', '3 ล็อค'];

        return view('admin.bookings.edit', compact('booking', 'allLots', 'tentSizes', 'counterSizes'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'use_date' => 'required|date',
            'shop_name' => 'required|string|max:150',
            'customer_phone' => 'required|string',
            'tent_size' => 'required|string',
            'counter_size' => 'nullable|string',
            'lots' => 'required|array|min:1',
            'lots.*' => 'required|integer|exists:lots,id',
            'admin_note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $booking->update([
                'use_date' => $validated['use_date'],
                'shop_name' => $validated['shop_name'],
                'customer_phone' => $validated['customer_phone'],
                'tent_size' => $validated['tent_size'],
                'counter_size' => $validated['counter_size'],
                'admin_note' => $validated['admin_note'],
            ]);

            $booking->lots()->sync($validated['lots']);

            // Update task date if exists
            if ($booking->deliveryTask) {
                $booking->deliveryTask->update([
                    'task_date' => $validated['use_date']
                ]);
            }
        });

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'แก้ไขข้อมูลการจองเรียบร้อยแล้ว');
    }

    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending_admin') {
            return back()->with('error', 'ไม่สามารถยืนยันการจองในสถานะนี้ได้');
        }

        DB::transaction(function () use ($booking) {
            $oldStatus = $booking->status;
            $booking->update([
                'status' => 'confirmed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            // Create empty delivery task
            DeliveryTask::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'task_date' => $booking->use_date,
                    'status' => 'waiting'
                ]
            );

            StatusLogService::log(Booking::class, $booking->id, $oldStatus, 'confirmed', auth()->id(), 'แอดมินยืนยันการจอง');
        });

        return back()->with('success', 'ยืนยันการจองเรียบร้อยแล้ว');
    }

    public function cancel(Booking $booking)
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกการจองนี้ได้');
        }

        DB::transaction(function () use ($booking) {
            $oldStatus = $booking->status;
            $booking->update(['status' => 'cancelled']);

            // Cancel associated delivery task if exists
            if ($booking->deliveryTask) {
                $booking->deliveryTask->delete();
            }

            StatusLogService::log(Booking::class, $booking->id, $oldStatus, 'cancelled', auth()->id(), 'ยกเลิกการจอง');
        });

        return back()->with('success', 'ยกเลิกการจองเรียบร้อยแล้ว');
    }

    public function assignStaff(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'staff_id' => 'required|integer|exists:users,id',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $oldStatus = $booking->status;
            
            // Check if user is staff
            $staff = User::where('id', $validated['staff_id'])->where('role', 'staff')->firstOrFail();

            // Set booking status to assigned
            $booking->update(['status' => 'assigned']);

            // Create or update task
            $task = DeliveryTask::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'staff_id' => $staff->id,
                    'task_date' => $booking->use_date,
                    'status' => 'waiting'
                ]
            );

            StatusLogService::log(Booking::class, $booking->id, $oldStatus, 'assigned', auth()->id(), "มอบหมายงานให้: {$staff->name}");
            StatusLogService::log(DeliveryTask::class, $task->id, null, 'waiting', auth()->id(), "มอบหมายงานสำเร็จ");
        });

        return back()->with('success', 'มอบหมายงานให้พนักงานเรียบร้อยแล้ว');
    }

    public function destroy(Booking $booking)
    {
        DB::transaction(function () use ($booking) {
            if ($booking->deliveryTask) {
                $booking->deliveryTask->delete();
            }
            $booking->delete();
        });

        return redirect()->route('admin.bookings.index')->with('success', 'ลบข้อมูลการจองเรียบร้อยแล้ว');
    }
}
