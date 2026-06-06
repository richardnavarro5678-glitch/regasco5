<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary stats
        $totalProducts = Product::where('is_active', true)->count();
        $totalInventoryValue = Product::sum(DB::raw('stock_quantity * cost_price'));
        // FIX: Added total retail/selling price value
        $totalSellingPriceValue = Product::sum(DB::raw('stock_quantity * selling_price'));

        // Recent sales - DAGDAG: limit to 4 only
        $recentSales = Sale::with(['user', 'product'])
            ->latest()
            ->limit(4)
            ->get();

        // Active cashiers count
        $activeCashiers = User::where('role', 'cashier')->where('is_active', true)->count();

        // Low stock products
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('is_active', true)
            ->with('supplier')
            ->get();

        // FIX: Today's sales - use whereDate with created_at for daily reset at 12:00 AM
        $todaySales = Sale::selectRaw('COUNT(*) as total_transactions, SUM(total_price) as total_amount')
            ->whereDate('created_at', today())
            ->first();

        // FIX: If no sales today, default to 0
        if (!$todaySales) {
            $todaySales = (object) [
                'total_transactions' => 0,
                'total_amount' => 0
            ];
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalInventoryValue',
            'totalSellingPriceValue',
            'recentSales',
            'activeCashiers',
            'lowStockProducts',
            'todaySales'
        ));
    }
}