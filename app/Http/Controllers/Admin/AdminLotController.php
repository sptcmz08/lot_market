<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Zone;
use Illuminate\Http\Request;

class AdminLotController extends Controller
{
    public function index(Request $request)
    {
        $zones = Zone::withCount('lots')->orderBy('sort_order')->get();
        
        $query = Lot::with('zone');

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('search')) {
            $query->where('lot_code', 'like', "%{$request->search}%")
                  ->orWhere('display_name', 'like', "%{$request->search}%");
        }

        $lots = $query->orderBy('lot_code')->paginate(30)->withQueryString();

        return view('admin.lots.index', compact('lots', 'zones'));
    }

    public function create()
    {
        $zones = Zone::all();
        return view('admin.lots.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => 'required|integer|exists:zones,id',
            'lot_code' => 'required|string|max:50|unique:lots,lot_code',
            'display_name' => 'nullable|string|max:100',
            'svg_element_id' => 'nullable|string|max:100',
            'position_x' => 'nullable|numeric',
            'position_y' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'is_active' => 'required|boolean',
            'note' => 'nullable|string',
        ]);

        Lot::create($validated);

        return redirect()->route('admin.lots.index')->with('success', 'สร้างล็อคเรียบร้อยแล้ว');
    }

    public function edit(Lot $lot)
    {
        $zones = Zone::all();
        return view('admin.lots.edit', compact('lot', 'zones'));
    }

    public function update(Request $request, Lot $lot)
    {
        $validated = $request->validate([
            'zone_id' => 'required|integer|exists:zones,id',
            'lot_code' => 'required|string|max:50|unique:lots,lot_code,' . $lot->id,
            'display_name' => 'nullable|string|max:100',
            'svg_element_id' => 'nullable|string|max:100',
            'position_x' => 'nullable|numeric',
            'position_y' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'is_active' => 'required|boolean',
            'note' => 'nullable|string',
        ]);

        $lot->update($validated);

        return redirect()->route('admin.lots.index')->with('success', 'อัปเดตข้อมูลล็อคเรียบร้อยแล้ว');
    }

    public function destroy(Lot $lot)
    {
        $lot->delete();
        return redirect()->route('admin.lots.index')->with('success', 'ลบล็อคเรียบร้อยแล้ว');
    }
}
