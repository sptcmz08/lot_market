<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Setting;
use Illuminate\Http\Request;

class PublicMapController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $zones = \App\Models\Zone::with(['lots' => function ($q) {
            $q->orderBy('lot_code');
        }])->orderBy('sort_order')->get();
        
        return view('public.map', compact('date', 'zones'));
    }

    public function lotStatus(Request $request)
    {
        $date = $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ])['date'];

        $lots = Lot::with([
            'bookings' => function ($query) use ($date) {
                $query->where('use_date', $date)
                      ->where('status', '!=', 'cancelled')
                      ->with(['lots', 'deliveryTask.photos']);
            },
            'zone'
        ])->get();

        $showShopName = Setting::getVal('show_shop_name_public', 'false') === 'true';

        $mappedLots = $lots->map(function ($lot) use ($showShopName) {
            $status = 'available';
            $shopName = null;
            $bookingCode = null;
            $bookingDetails = null;

            if (!$lot->is_active) {
                $status = 'blocked';
            } else {
                $activeBooking = $lot->bookings->first();
                if ($activeBooking) {
                    $bookingCode = $activeBooking->booking_code;
                    $shopName = $showShopName ? $activeBooking->shop_name : 'มีคำสั่งจอง';

                    switch ($activeBooking->status) {
                        case 'pending_admin':
                            $status = 'pending';
                            break;
                        case 'confirmed':
                        case 'assigned':
                            $status = 'booked';
                            break;
                        case 'installing':
                            $status = 'installing';
                            break;
                        case 'completed':
                            $status = 'completed';
                            break;
                        case 'problem':
                            $status = 'problem';
                            break;
                        default:
                            $status = 'available';
                    }

                    // Detailed booking info for the popover/tooltip
                    $bookingLots = $activeBooking->lots->pluck('lot_code')->toArray();
                    $photoPaths = [];
                    if ($activeBooking->deliveryTask && $activeBooking->deliveryTask->photos) {
                        foreach ($activeBooking->deliveryTask->photos as $p) {
                            $photoPaths[$p->photo_type] = asset('storage/' . $p->image_path);
                        }
                    }

                    $bookingDetails = [
                        'booking_code' => $activeBooking->booking_code,
                        'shop_name' => $activeBooking->shop_name,
                        'status' => $activeBooking->status,
                        'lots' => $bookingLots,
                        'equipment_summary' => $activeBooking->equipmentSummary(),
                        'tent_size' => $activeBooking->tent_size,
                        'tent_color' => $activeBooking->tent_color,
                        'counter_size' => $activeBooking->counter_size,
                        'counter_color' => $activeBooking->counter_color,
                        'photos' => (object)$photoPaths
                    ];
                }
            }

            return [
                'id' => $lot->id,
                'lot_code' => $lot->lot_code,
                'display_name' => $lot->display_name ?? $lot->lot_code,
                'status' => $status,
                'shop_name' => $shopName,
                'booking_code' => $bookingCode,
                'zone_code' => $lot->zone ? $lot->zone->code : null,
                'booking_details' => $bookingDetails
            ];
        });

        return response()->json([
            'date' => $date,
            'lots' => $mappedLots
        ]);
    }
}
