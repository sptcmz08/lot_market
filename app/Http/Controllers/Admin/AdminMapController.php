<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminMapController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $zones = \App\Models\Zone::with(['lots' => function ($q) {
            $q->orderBy('lot_code');
        }])->orderBy('sort_order')->get();
        
        return view('admin.map.index', compact('date', 'zones'));
    }
}
