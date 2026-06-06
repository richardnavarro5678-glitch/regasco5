<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['product', 'supplier', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        
        $products = Product::where('is_active', true)
            ->select('product_id', 'product_name', 'sku', 'supplier_id', 'stock_quantity', 'cost_price')
            ->with('supplier')
            ->orderBy('product_name')
            ->get();

        return view('admin.deliveries.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        DB::transaction(function () use ($validated, $product) {
            $delivery = Delivery::create([
                'supplier_id' => $validated['supplier_id'],
                'product_id' => $validated['product_id'],
                'user_id' => Auth::id(),
                'quantity' => $validated['quantity'],
                'unit_cost' => $validated['unit_cost'],
                'total_cost' => $validated['unit_cost'] * $validated['quantity'],
                'delivery_date' => $validated['delivery_date'],
                'notes' => $validated['notes'],
            ]);

            $stockBefore = $product->stock_quantity;
            $product->increment('stock_quantity', $validated['quantity']);

            if ($validated['unit_cost'] != $product->cost_price) {
                $product->update(['cost_price' => $validated['unit_cost']]);
            }

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'movement_type' => 'delivery',
                'quantity' => $validated['quantity'],
                'reference_type' => 'delivery',
                'reference_id' => $delivery->delivery_id,
                'stock_before' => $stockBefore,
                'stock_after' => $product->stock_quantity,
                'user_id' => Auth::id(),
                'remarks' => 'Delivery #' . $delivery->delivery_id . ' from ' . $delivery->supplier->supplier_name,
            ]);
        });

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery recorded successfully. Stock has been updated.');
    }

    public function show(Delivery $delivery)
    {
        return view('admin.deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)
            ->select('product_id', 'product_name', 'sku', 'supplier_id', 'stock_quantity', 'cost_price')
            ->with('supplier')
            ->orderBy('product_name')
            ->get();

        return view('admin.deliveries.edit', compact('delivery', 'suppliers', 'products'));
    }

    // FIX: Updated update method - NO product stock changes
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // FIX: Only update delivery record, no product stock changes
        $delivery->update([
            'supplier_id' => $validated['supplier_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'unit_cost' => $validated['unit_cost'],
            'total_cost' => $validated['unit_cost'] * $validated['quantity'],
            'delivery_date' => $validated['delivery_date'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery updated successfully. Product stock was not changed.');
    }

    // FIX: Updated destroy - NO product stock restoration
    public function destroy(Delivery $delivery)
    {
        // FIX: Just soft delete the delivery, no stock changes
        $delivery->delete();

        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery deleted successfully. Product stock was not changed.');
    }

    public function trashed()
    {
        $deliveries = Delivery::onlyTrashed()
            ->with(['product', 'supplier', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.deliveries.trashed', compact('deliveries'));
    }

    // FIX: Updated restore - NO product stock changes
    public function restore($delivery)
    {
        $delivery = Delivery::withTrashed()->findOrFail($delivery);
        
        // FIX: Just restore the delivery record, no stock changes
        $delivery->restore();

        return redirect()->route('admin.deliveries.trashed')
            ->with('success', 'Delivery restored successfully. Product stock was not changed.');
    }

    public function forceDelete($delivery)
    {
        Delivery::withTrashed()->findOrFail($delivery)->forceDelete();

        return redirect()->route('admin.deliveries.trashed')
            ->with('success', 'Delivery permanently deleted.');
    }
}