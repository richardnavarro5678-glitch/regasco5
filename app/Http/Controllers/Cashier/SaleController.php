<?php

namespace App\Http\Controllers\Cashier;

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
    // FIX: Changed from paginate(20) to get() for scroll view
    public function index()
    {
        $sales = Sale::with(['product', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('cashier.sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->with('supplier')
            ->get();

        return view('cashier.sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock_quantity < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock. Only ' . $product->stock_quantity . ' available.');
        }

        $validated['unit_price'] = $product->selling_price;
        $validated['total_price'] = $product->selling_price * $validated['quantity'];
        $validated['user_id'] = Auth::id();
        
        // FIX: Use Carbon::now() with explicit Asia/Manila timezone
        $validated['sale_date'] = Carbon::now('Asia/Manila');
        
        $validated['product_name'] = $product->product_name;

        DB::transaction(function () use ($validated, $product) {
            $sale = Sale::create($validated);

            $stockBefore = $product->stock_quantity;
            $product->decrement('stock_quantity', $validated['quantity']);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'movement_type' => 'sale',
                'quantity' => -$validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $product->stock_quantity,
                'reference_id' => $sale->sale_id,
                'reference_type' => 'sale',
                'user_id' => Auth::id(),
                'notes' => 'Sale #' . $sale->sale_id,
            ]);
        });

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Sale completed successfully.');
    }

    public function show(Sale $sale)
    {
        return view('cashier.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        if ($sale->user_id !== Auth::id()) {
            return redirect()->route('cashier.sales.index')
                ->with('error', 'You are not authorized to edit this sale.');
        }

        $products = Product::where('is_active', true)
            ->with('supplier')
            ->get();

        return view('cashier.sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        if ($sale->user_id !== Auth::id()) {
            return redirect()->route('cashier.sales.index')
                ->with('error', 'You are not authorized to update this sale.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $oldQuantity = $sale->quantity;
        $oldTotalPrice = $sale->total_price;
        $quantityDiff = $validated['quantity'] - $oldQuantity;

        if ($quantityDiff > 0 && $product->stock_quantity < $quantityDiff) {
            return back()->with('error', 'Insufficient stock. Only ' . $product->stock_quantity . ' available.');
        }

        $validated['unit_price'] = $product->selling_price;
        $validated['total_price'] = $product->selling_price * $validated['quantity'];
        $validated['product_name'] = $product->product_name;

        DB::transaction(function () use ($validated, $sale, $product, $oldQuantity, $oldTotalPrice, $quantityDiff) {
            $stockBefore = $product->stock_quantity;
            $product->increment('stock_quantity', $oldQuantity);
            $product->decrement('stock_quantity', $validated['quantity']);

            $sale->update($validated);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'movement_type' => 'adjustment',
                'quantity' => -$quantityDiff,
                'stock_before' => $stockBefore,
                'stock_after' => $product->stock_quantity,
                'reference_id' => $sale->sale_id,
                'reference_type' => 'sale_update',
                'user_id' => Auth::id(),
                'notes' => 'Sale #' . $sale->sale_id . ' updated from ' . $oldQuantity . ' to ' . $validated['quantity'] . 
                          ' (Total: ₱' . number_format($oldTotalPrice, 2) . ' → ₱' . number_format($validated['total_price'], 2) . ')',
            ]);
        });

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Sale updated successfully. Today\'s Sales recalculated.');
    }

    // FIX: Changed from paginate(20) to get() for scroll view
    public function history(Request $request)
    {
        $query = Sale::with(['product'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $sales = $query->get();

        return view('cashier.sales.history', compact('sales'));
    }

    // FIX: Updated destroy - NO stock restoration, just soft delete
    public function destroy(Sale $sale)
    {
        if ($sale->user_id !== Auth::id()) {
            return redirect()->route('cashier.sales.index')
                ->with('error', 'You are not authorized to delete this sale.');
        }

        // FIX: Just soft delete the sale, NO stock changes
        $sale->delete();

        return redirect()->route('cashier.sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    // FIX: Added archived method - shows soft-deleted sales
    public function archived()
    {
        $sales = Sale::onlyTrashed()
            ->with(['product'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('cashier.sales.archived', compact('sales'));
    }

    // FIX: Updated restore - NO stock deduction when restoring
    public function restore($saleId)
    {
        $sale = Sale::withTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($saleId);

        // FIX: Just restore the sale record, NO stock changes
        $sale->restore();

        return redirect()->route('cashier.sales.index')
            ->with('success', 'Sale restored successfully.');
    }
}