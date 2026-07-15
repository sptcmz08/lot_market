<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\Lot;
use App\Models\User;
use App\Services\LotAvailabilityService;
use App\Services\PhotoUploadService;
use App\Services\StatusLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminBookingController extends Controller
{
    public function __construct(
        private LotAvailabilityService $lotAvailabilityService,
        private PhotoUploadService $photoUploadService
    ) {
    }

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
        $tentSizes = ['1.5x1.5', '2x2', '2x3', '2.5x2.5', '3x4.5'];
        $counterSizes = ['1 ล็อค 70x75 cm. มีหลังคา', '2 ล็อค 140x75 cm.', '3 ล็อค 180x75 cm.'];
        $equipmentColors = ['แดง', 'ขาว', 'น้ำเงิน', 'เขียว', 'ดำ'];

        return view('admin.bookings.edit', compact('booking', 'allLots', 'tentSizes', 'counterSizes', 'equipmentColors'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'use_date' => 'required|date',
            'shop_name' => 'required|string|max:150',
            'customer_phone' => 'required|string',
            'wants_tent' => 'nullable|boolean',
            'tent_size' => 'nullable|required_if:wants_tent,1|in:1.5x1.5,2x2,2x3,2.5x2.5,3x4.5',
            'tent_color' => 'nullable|required_if:wants_tent,1|string|max:50',
            'wants_counter' => 'nullable|boolean',
            'counter_size' => 'nullable|required_if:wants_counter,1|in:1 ล็อค 70x75 cm. มีหลังคา,2 ล็อค 140x75 cm.,3 ล็อค 180x75 cm.',
            'counter_color' => 'nullable|string|max:50',
            'lots' => 'required|array|min:1',
            'lots.*' => 'required|integer|exists:lots,id',
            'admin_note' => 'nullable|string',
        ]);

        $validated['wants_tent'] = $request->boolean('wants_tent');
        $validated['wants_counter'] = $request->boolean('wants_counter');

        if (!$validated['wants_tent'] && !$validated['wants_counter']) {
            return back()
                ->withErrors(['equipment' => 'กรุณาเลือกอย่างน้อย 1 รายการ: เต็นท์ หรือ เคาน์เตอร์'])
                ->withInput();
        }

        if (!$this->lotAvailabilityService->isAvailable($validated['lots'], $validated['use_date'], $booking->id)) {
            return back()
                ->withErrors(['lots' => 'ล็อคที่เลือกมีคำสั่งจองอุปกรณ์อยู่แล้วในวันที่ใช้งานนี้ กรุณาตรวจสอบรายการเดิมก่อน'])
                ->withInput();
        }

        DB::transaction(function () use ($booking, $validated) {
            $booking->update([
                'use_date' => $validated['use_date'],
                'shop_name' => $validated['shop_name'],
                'customer_phone' => $validated['customer_phone'],
                'tent_size' => $validated['wants_tent'] ? ($validated['tent_size'] ?? null) : null,
                'tent_color' => $validated['wants_tent'] ? ($validated['tent_color'] ?? null) : null,
                'counter_size' => $validated['wants_counter'] ? ($validated['counter_size'] ?? null) : null,
                'counter_color' => $validated['wants_counter'] ? ($validated['counter_color'] ?? null) : null,
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

        if (!$booking->payment_slip_path && !$booking->collect_front_store) {
            return back()->with('error', 'กรุณาแนบรูปสลิปการชำระเงินก่อนยืนยันการจอง');
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

    public function uploadPaymentSlip(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'payment_slip' => 'required|image|mimes:jpg,jpeg,png,webp',
        ], [
            'payment_slip.required' => 'กรุณาเลือกรูปสลิปการชำระเงิน',
            'payment_slip.image' => 'ไฟล์สลิปต้องเป็นรูปภาพ',
            'payment_slip.mimes' => 'รองรับรูปสลิปประเภท JPG, PNG และ WEBP เท่านั้น',
        ]);

        $oldPath = $booking->payment_slip_path;
        $newPath = $this->photoUploadService->upload($validated['payment_slip'], 'payment-slips');

        $booking->update(['payment_slip_path' => $newPath]);

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        StatusLogService::log(
            Booking::class,
            $booking->id,
            $booking->status,
            $booking->status,
            auth()->id(),
            $oldPath ? 'แอดมินเปลี่ยนรูปสลิปการชำระเงิน' : 'แอดมินแนบรูปสลิปการชำระเงิน'
        );

        return back()->with('success', $oldPath
            ? 'เปลี่ยนรูปสลิปการชำระเงินเรียบร้อยแล้ว'
            : 'แนบรูปสลิปการชำระเงินเรียบร้อยแล้ว สามารถยืนยันการจองต่อได้');
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
