<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // FIX: $todaySales ay integer (count), hindi collection
        $todaySales = Sale::where('user_id', Auth::id())
            ->whereDate('sale_date', today())
            ->count();

        // FIX: $todayRevenue ay float (sum)
        $todayRevenue = Sale::where('user_id', Auth::id())
            ->whereDate('sale_date', today())
            ->sum('total_price');

        // FIX: $todayItems ay integer (sum ng quantity)
        $todayItems = Sale::where('user_id', Auth::id())
            ->whereDate('sale_date', today())
            ->sum('quantity');

        $totalSales = Sale::where('user_id', Auth::id())->count();

        // FIX: Add with('product') para maiwasan yung null
        $recentSales = Sale::with(['product'])
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->limit(5)
            ->get();

        return view('cashier.dashboard', compact(
            'todaySales',
            'todayRevenue',
            'todayItems',
            'totalSales',
            'recentSales',
            'lowStockProducts'
        ));
    }
}