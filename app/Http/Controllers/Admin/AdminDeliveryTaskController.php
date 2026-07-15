<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDeliveryTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryTask::with(['booking.lots', 'staff']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('task_date', $request->date);
        }

        $tasks = $query->orderBy('task_date', 'desc')->paginate(20)->withQueryString();
        $staffMembers = User::where('role', 'staff')->get();

        return view('admin.tasks.index', compact('tasks', 'staffMembers'));
    }

    public function show(DeliveryTask $task)
    {
        $task->load(['booking.lots', 'staff', 'photos.uploadedBy']);
        
        $logs = \App\Models\StatusLog::where('loggable_type', DeliveryTask::class)
            ->where('loggable_id', $task->id)
            ->with('changedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.tasks.show', compact('task', 'logs'));
    }
}
