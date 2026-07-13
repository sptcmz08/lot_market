<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Lot;
use App\Services\BookingService;
use App\Services\LotAvailabilityService;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    protected $bookingService;
    protected $lotAvailabilityService;

    public function __construct(BookingService $bookingService, LotAvailabilityService $lotAvailabilityService)
    {
        $this->bookingService = $bookingService;
        $this->lotAvailabilityService = $lotAvailabilityService;
    }

    public function create(Request $request)
    {
        $date = $request->query('date', now()->addDay()->format('Y-m-d'));
        $selectedCodes = array_filter(explode(',', $request->query('lots', '')));
        
        $lots = Lot::whereIn('lot_code', $selectedCodes)->where('is_active', true)->get();
        
        if ($lots->isEmpty()) {
            return redirect()->route('public.map', ['date' => $date])->with('error', 'กรุณาเลือกล็อคอย่างน้อย 1 ล็อคเพื่อทำการจอง');
        }

        if (!$this->lotAvailabilityService->isAvailable($lots->pluck('id')->all(), $date)) {
            return redirect()
                ->route('public.map', ['date' => $date])
                ->with('error', 'ล็อคที่เลือกถูกจองไปแล้วในวันดังกล่าว กรุณาเลือกใหม่อีกครั้ง');
        }

        $tentSizes = ['1.5', '2x2', '2x3', '3x3', '2.5x2.5', '3x4.5'];

        return view('public.booking-create', compact('date', 'lots', 'selectedCodes', 'tentSizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'use_date' => 'required|date|after_or_equal:today',
            'shop_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|regex:/^[0-9]{9,10}$/',
            'lots' => 'required|array|min:1',
            'lots.*' => 'required|string|exists:lots,lot_code',
            'tent_size' => 'required|in:1.5,2x2,2x3,3x3,2.5x2.5,3x4.5',
            'customer_note' => 'nullable|string|max:500',
        ], [
            'customer_phone.regex' => 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 9-10 หลัก',
            'use_date.after_or_equal' => 'วันที่จองต้องเป็นวันนี้หรือในอนาคต',
            'shop_name.required' => 'กรุณากรอกชื่อร้าน',
            'customer_phone.required' => 'กรุณากรอกเบอร์โทร',
        ]);

        $lots = Lot::whereIn('lot_code', $validated['lots'])->get();
        $lotIds = $lots->pluck('id')->toArray();

        // Check availability
        if (!$this->lotAvailabilityService->isAvailable($lotIds, $validated['use_date'])) {
            return back()->withErrors(['lots' => 'ล็อคที่คุณเลือกบางล็อตถูกจองไปแล้วในวันดังกล่าว กรุณาเลือกใหม่อีกครั้ง'])->withInput();
        }

        $booking = $this->bookingService->createBooking($validated, $lotIds);

        return redirect()->route('public.booking.check')->with('success', 'ส่งคำขอจองสำเร็จแล้ว! รหัสอ้างอิงของคุณคือ: ' . $booking->booking_code);
    }

    public function checkForm()
    {
        return view('public.booking-check');
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'search_query' => 'required|string|min:4',
        ], [
            'search_query.required' => 'กรุณากรอกเบอร์โทรหรือรหัสจอง',
            'search_query.min' => 'กรุณากรอกอย่างน้อย 4 ตัวอักษร',
        ]);

        $query = $validated['search_query'];

        $bookings = Booking::where(function ($q) use ($query) {
            $q->where('booking_code', 'like', "%{$query}%")
              ->orWhere('customer_phone', 'like', "%{$query}%");
        })
        ->with(['lots', 'deliveryTask.photos'])
        ->orderBy('use_date', 'desc')
        ->get();

        return view('public.booking-check', compact('bookings', 'query'));
    }
}
