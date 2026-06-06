<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with('product');

        // FIX: Single date search (no end date)
        if ($request->filled('search_date')) {
            $query->whereDate('created_at', $request->search_date);
        }

        $movements = $query->latest()->paginate(50)->withQueryString();

        return view('admin.movements.index', compact('movements'));
    }
}