<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    // FIX: Changed from paginate(20) to get() for scrollable table
    public function index(Request $request)
    {
        $query = Sale::with(['product', 'user'])
            ->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // FIX: Use get() instead of paginate() for scrollable view
        $sales = $query->get();

        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $oldQuantity = $sale->quantity;
        $newQuantity = $validated['quantity'];

        $unitPrice = $product->selling_price;
        $totalPrice = $unitPrice * $newQuantity;

        try {
            DB::transaction(function () use ($sale, $product, $oldQuantity, $newQuantity, $unitPrice, $totalPrice) {
                $product->increment('stock_quantity', $oldQuantity);
                
                if ($product->stock_quantity < $newQuantity) {
                    throw new \Exception('Insufficient stock. Available: ' . $product->stock_quantity);
                }
                
                $product->decrement('stock_quantity', $newQuantity);
                
                $sale->update([
                    'product_id' => $product->product_id,
                    'quantity' => $newQuantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'sale_date' => Carbon::now('Asia/Manila'),
                ]);
            });

            return redirect()->route('admin.sales.index')
                ->with('success', 'Sale updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Update failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    public function trashed()
    {
        $sales = Sale::onlyTrashed()
            ->with(['product', 'user'])
            ->latest()
            ->get();

        return view('admin.sales.trashed', compact('sales'));
    }

    public function restore($saleId)
    {
        $sale = Sale::withTrashed()->findOrFail($saleId);

        $sale->restore();

        return redirect()->route('admin.sales.trashed')
            ->with('success', 'Sale restored successfully.');
    }

    public function forceDelete($saleId)
    {
        $sale = Sale::withTrashed()->findOrFail($saleId);
        $sale->forceDelete();

        return redirect()->route('admin.sales.trashed')
            ->with('success', 'Sale permanently deleted.');
    }
}