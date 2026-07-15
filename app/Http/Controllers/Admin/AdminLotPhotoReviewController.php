<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPhoto;
use App\Models\DeliveryTask;
use App\Services\StatusLogService;
use Illuminate\Http\Request;

class AdminLotPhotoReviewController extends Controller
{
    public function index()
    {
        $photos = DeliveryPhoto::where('photo_type', 'lot_number')
            ->where('ocr_status', 'pending_review')
            ->with(['deliveryTask.booking.lots', 'deliveryTask.staff', 'uploadedBy'])
            ->latest()
            ->paginate(20);

        return view('admin.lot-photo-reviews.index', compact('photos'));
    }

    public function status()
    {
        return response()->json([
            'pending_count' => DeliveryPhoto::where('photo_type', 'lot_number')
                ->where('ocr_status', 'pending_review')
                ->count(),
        ]);
    }

    public function approve(DeliveryPhoto $photo)
    {
        $this->ensureLotPhoto($photo);

        $photo->update([
            'ocr_status' => 'approved',
            'ocr_text' => trim(($photo->ocr_text ? $photo->ocr_text . "\n" : '') . 'ยืนยันโดยแอดมิน: ' . auth()->user()->name),
            'ocr_confidence' => 100,
        ]);

        if ($photo->deliveryTask) {
            StatusLogService::log(
                DeliveryTask::class,
                $photo->deliveryTask->id,
                $photo->deliveryTask->status,
                $photo->deliveryTask->status,
                auth()->id(),
                'แอดมินยืนยันรูปเลขล็อตแล้ว'
            );
        }

        return back()->with('success', 'ยืนยันรูปเลขล็อตเรียบร้อยแล้ว');
    }

    public function reject(Request $request, DeliveryPhoto $photo)
    {
        $this->ensureLotPhoto($photo);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:250',
        ]);

        $reason = $validated['reason'] ?? 'รูปเลขล็อตไม่ตรงหรือไม่ชัดเจน';

        $photo->update([
            'ocr_status' => 'rejected',
            'ocr_text' => trim(($photo->ocr_text ? $photo->ocr_text . "\n" : '') . 'ตีกลับโดยแอดมิน: ' . $reason),
            'ocr_confidence' => 0,
        ]);

        if ($photo->deliveryTask) {
            StatusLogService::log(
                DeliveryTask::class,
                $photo->deliveryTask->id,
                $photo->deliveryTask->status,
                $photo->deliveryTask->status,
                auth()->id(),
                'แอดมินตีกลับรูปเลขล็อต: ' . $reason
            );
        }

        return back()->with('success', 'ตีกลับรูปเลขล็อตแล้ว');
    }

    private function ensureLotPhoto(DeliveryPhoto $photo): void
    {
        abort_unless($photo->photo_type === 'lot_number', 404);
    }
}
