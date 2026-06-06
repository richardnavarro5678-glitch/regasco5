<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'supplier'])
            ->latest()
            ->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'category_id' => 'nullable|exists:categories,category_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = true;
        $validated['category_id'] = $validated['category_id'] ?? null;

        // FIX: Create product first
        $product = Product::create($validated);

        // FIX: Auto-create delivery record for initial stock (matches your Delivery model schema)
        Delivery::create([
            'product_id' => $product->product_id,
            'supplier_id' => $product->supplier_id,
            'user_id' => Auth::id(),
            'quantity' => $product->stock_quantity,
            'delivery_date' => now(),
            'notes' => 'Initial stock upon product creation',
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product added successfully with initial delivery record.');
    }

    public function show(Product $product)
    {
        $sales = $product->sales()
            ->with(['user', 'product' => function($query) {
                $query->withTrashed();
            }])
            ->latest()
            ->paginate(10);

        $deliveries = $product->deliveries()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.products.show', compact('product', 'sales', 'deliveries'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->product_id . ',product_id',
            'category_id' => 'nullable|exists:categories,category_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        unset($validated['is_active']);
        $validated['category_id'] = $validated['category_id'] ?? null;

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function trashed()
    {
        $products = Product::onlyTrashed()
            ->with(['category', 'supplier'])
            ->latest()
            ->paginate(20);
        return view('admin.products.trashed', compact('products'));
    }

    public function restore($product)
    {
        $product = Product::withTrashed()->findOrFail($product);
        $product->restore();
        $product->update(['is_active' => true]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product restored successfully.');
    }

    public function forceDelete($product)
    {
        Product::withTrashed()->findOrFail($product)->forceDelete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product permanently deleted.');
    }
}