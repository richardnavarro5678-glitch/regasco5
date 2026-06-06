<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $sales = Sale::with(['product', 'user'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->latest()
            ->get();

        // FIX: Group sales by DATE + PRODUCT (not just date)
        // Example: May 14 → LPG tank (combined) + Nova (combined)
        $groupedSales = $sales->groupBy(function ($sale) {
            // Group by date + product_id para magkahiwalay per product per day
            return $sale->sale_date->format('Y-m-d') . '_' . $sale->product_id;
        })->map(function ($productSales) {
            $firstSale = $productSales->first();
            return (object) [
                'date' => $firstSale->sale_date,
                'product_id' => $firstSale->product_id,
                'product_name' => $firstSale->product->product_name,
                'quantity' => $productSales->sum('quantity'),
                'total_price' => $productSales->sum('total_price'),
                'unit_price' => $firstSale->unit_price,
                'transaction_count' => $productSales->count(),
            ];
        })->sortBy('date')->values(); // Sort by date then reset keys

        $totalRevenue = $sales->sum('total_price');
        $totalTransactions = $sales->count();
        $itemsSold = $sales->sum('quantity');

        $summary = [
            'total_sales' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'total_items' => $itemsSold,
        ];

        return view('admin.reports.sales', compact(
            'sales',
            'groupedSales',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalTransactions',
            'itemsSold',
            'summary'
        ));
    }

    public function inventoryReport()
    {
        $products = Product::with('supplier')
            ->where('is_active', true)
            ->get();

        $totalValue = $products->sum(function ($product) {
            return $product->stock_quantity * $product->cost_price;
        });

        $totalRetail = $products->sum(function ($product) {
            return $product->stock_quantity * $product->price;
        });

        $lowStock = $products->filter(function ($product) {
            return $product->stock_quantity <= $product->low_stock_threshold;
        });

        return view('admin.reports.inventory', compact(
            'products',
            'totalValue',
            'totalRetail',
            'lowStock'
        ));
    }
}