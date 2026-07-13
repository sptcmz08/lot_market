<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Lot;
use App\Services\BookingService;
use App\Services\LotAvailabilityService;
use App\Services\PhotoUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicBookingController extends Controller
{
    protected $bookingService;
    protected $lotAvailabilityService;
    protected $photoUploadService;

    public function __construct(BookingService $bookingService, LotAvailabilityService $lotAvailabilityService, PhotoUploadService $photoUploadService)
    {
        $this->bookingService = $bookingService;
        $this->lotAvailabilityService = $lotAvailabilityService;
        $this->photoUploadService = $photoUploadService;
    }

    public function create(Request $request)
    {
        $date = $request->query('date', now()->addDay()->format('Y-m-d'));
        $selectedCodes = array_filter(explode(',', $request->query('lots', '')));
        
        $lots = collect();

        if (!empty($selectedCodes)) {
            $lots = Lot::whereIn('lot_code', $selectedCodes)->where('is_active', true)->get();
        }

        if ($lots->isNotEmpty() && !$this->lotAvailabilityService->isAvailable($lots->pluck('id')->all(), $date)) {
            return redirect()
                ->route('public.map', ['date' => $date])
                ->with('error', 'ล็อคที่เลือกมีคำสั่งจองอุปกรณ์อยู่แล้วในวันดังกล่าว กรุณาตรวจสอบรายการเดิมก่อน');
        }

        $tentSizes = ['1.5', '2x2', '2x3', '3x3', '2.5x2.5', '3x4.5'];
        $counterSizes = ['1 ล็อค', '2 ล็อค', '3 ล็อค'];
        $equipmentColors = ['ขาว', 'ดำ', 'น้ำเงิน', 'แดง', 'เขียว', 'เหลือง'];
        $lotPrefixes = Lot::where('is_active', true)
            ->pluck('lot_code')
            ->map(fn ($code) => preg_replace('/\d+$/', '', $code))
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $firstSelected = $selectedCodes[0] ?? null;
        $selectedPrefix = $firstSelected ? preg_replace('/\d+$/', '', $firstSelected) : null;
        $selectedNumbers = collect($selectedCodes)
            ->map(fn ($code) => (int) Str::after($code, preg_replace('/\d+$/', '', $code)))
            ->filter();
        $selectedFrom = $selectedNumbers->min();
        $selectedTo = $selectedNumbers->max();

        return view('public.booking-create', compact(
            'date',
            'lots',
            'selectedCodes',
            'tentSizes',
            'counterSizes',
            'equipmentColors',
            'lotPrefixes',
            'selectedPrefix',
            'selectedFrom',
            'selectedTo'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'use_date' => 'required|date|after_or_equal:today',
            'shop_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|regex:/^[0-9]{9,10}$/',
            'lots' => 'nullable|array',
            'lots.*' => 'required|string|exists:lots,lot_code',
            'lot_prefix' => 'nullable|required_without:lots|string|max:20',
            'lot_number_from' => 'nullable|required_without:lots|integer|min:1|max:999',
            'lot_number_to' => 'nullable|required_without:lots|integer|min:1|max:999',
            'wants_tent' => 'nullable|boolean',
            'tent_size' => 'nullable|required_if:wants_tent,1|in:1.5,2x2,2x3,3x3,2.5x2.5,3x4.5',
            'tent_color' => 'nullable|required_if:wants_tent,1|string|max:50',
            'wants_counter' => 'nullable|boolean',
            'counter_size' => 'nullable|required_if:wants_counter,1|in:1 ล็อค,2 ล็อค,3 ล็อค',
            'counter_color' => 'nullable|string|max:50',
            'payment_slip' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'collect_front_store' => 'nullable|boolean',
            'customer_note' => 'nullable|string|max:500',
        ], [
            'customer_phone.regex' => 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 9-10 หลัก',
            'use_date.after_or_equal' => 'วันที่จองต้องเป็นวันนี้หรือในอนาคต',
            'shop_name.required' => 'กรุณากรอกชื่อร้าน',
            'customer_phone.required' => 'กรุณากรอกเบอร์โทร',
            'lot_prefix.required_without' => 'กรุณาเลือกอักษรล็อค',
            'lot_number_from.required_without' => 'กรุณากรอกเลขล็อคเริ่มต้น',
            'lot_number_to.required_without' => 'กรุณากรอกเลขล็อคสิ้นสุด',
            'tent_size.required_if' => 'กรุณาเลือกขนาดเต็นท์',
            'tent_color.required_if' => 'กรุณาเลือกสีเต็นท์',
            'counter_size.required_if' => 'กรุณาเลือกขนาดเคาน์เตอร์',
            'payment_slip.image' => 'ไฟล์สลิปต้องเป็นรูปภาพ',
            'payment_slip.max' => 'รูปสลิปต้องมีขนาดไม่เกิน 5MB',
        ]);

        $validated['wants_tent'] = $request->boolean('wants_tent');
        $validated['wants_counter'] = $request->boolean('wants_counter');
        $validated['collect_front_store'] = $request->boolean('collect_front_store');

        if (!$validated['wants_tent'] && !$validated['wants_counter']) {
            return back()
                ->withErrors(['equipment' => 'กรุณาเลือกอย่างน้อย 1 รายการ: เต็นท์ หรือ เคาน์เตอร์'])
                ->withInput();
        }

        if (empty($validated['lots'])) {
            if ((int) $validated['lot_number_from'] > (int) $validated['lot_number_to']) {
                return back()
                    ->withErrors(['lot_number_to' => 'เลขล็อคสิ้นสุดต้องมากกว่าหรือเท่ากับเลขเริ่มต้น'])
                    ->withInput();
            }

            $validated['lots'] = collect(range((int) $validated['lot_number_from'], (int) $validated['lot_number_to']))
                ->map(fn ($number) => $validated['lot_prefix'] . $number)
                ->all();
        }

        $lots = Lot::whereIn('lot_code', $validated['lots'])->where('is_active', true)->get();

        if ($lots->count() !== count(array_unique($validated['lots']))) {
            return back()
                ->withErrors(['lots' => 'ไม่พบเลขล็อคบางรายการในระบบ กรุณาตรวจสอบอักษรล็อคและช่วงเลขอีกครั้ง'])
                ->withInput();
        }

        $lotIds = $lots->pluck('id')->toArray();

        // Check availability
        if (!$this->lotAvailabilityService->isAvailable($lotIds, $validated['use_date'])) {
            return back()->withErrors(['lots' => 'ล็อคที่เลือกมีคำสั่งจองอุปกรณ์อยู่แล้วในวันดังกล่าว กรุณาตรวจสอบรายการเดิมก่อน'])->withInput();
        }

        if ($request->hasFile('payment_slip')) {
            $validated['payment_slip_path'] = $this->photoUploadService->upload($request->file('payment_slip'), 'payment-slips');
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
