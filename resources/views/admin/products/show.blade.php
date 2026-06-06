@extends('layouts.admin')

@section('title', 'View Product - REGASCO SIS')
@section('page-title', 'View Product')

@section('admin-content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Products
    </a>
</div>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Product Details Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg">
                <i class="fas fa-box mr-2"></i>
                Product Details
            </h3>
        </div>
        
        <div class="p-6">
            <!-- Product Header -->
            <div class="flex items-center mb-6 pb-6 border-b border-gray-100">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-box text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $product->product_name }}</h2>
                    <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Product Name</label>
                        <p class="text-base font-semibold text-gray-800">{{ $product->product_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">SKU</label>
                        <p class="text-base font-semibold text-gray-800">{{ $product->sku }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                        <p class="text-base font-semibold text-gray-800">
                            {{ $product->category->category_name ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Supplier</label>
                        <p class="text-base font-semibold text-gray-800">
                            {{ $product->supplier->supplier_name ?? $product->supplier->name ?? 'N/A' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                        <p class="text-base text-gray-800">{{ $product->description ?? 'No description' }}</p>
                    </div>
                </div>

                <!-- Pricing & Stock -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Pricing & Stock</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Cost Price</label>
                        <p class="text-base font-semibold text-gray-800">₱{{ number_format($product->cost_price, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Selling Price</label>
                        <p class="text-base font-semibold text-gray-800">₱{{ number_format($product->selling_price, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Stock Quantity</label>
                        <p class="text-base font-semibold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $product->stock_quantity }}
                            @if($product->isLowStock())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 ml-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Low Stock
                                </span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Low Stock Threshold</label>
                        <p class="text-base font-semibold text-gray-800">{{ $product->low_stock_threshold }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Profit Margin</label>
                        <p class="text-base font-semibold text-gray-800">
                            ₱{{ number_format($product->selling_price - $product->cost_price, 2) }}
                            ({{ number_format((($product->selling_price - $product->cost_price) / $product->cost_price) * 100, 1) }}%)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-500">
                    <div>
                        <label class="block font-medium mb-1">Created At</label>
                        <p>{{ $product->created_at ? $product->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Updated At</label>
                        <p>{{ $product->updated_at ? $product->updated_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Product ID</label>
                        <p>#{{ $product->product_id }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    Back
                </a>
                <a href="{{ url('/admin/products/' . $product->product_id . '/edit') }}" 
                    class="px-6 py-3 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>

    <!-- FIX: Sales History Section with N/A logic -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg flex items-center">
                <i class="fas fa-receipt mr-2"></i>
                Sales History
            </h3>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Unit Price</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cashier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $sales = $product->sales()
                                ->with(['user'])
                                ->latest()
                                ->limit(20)
                                ->get();
                        @endphp
                        
                        @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $sale->sale_date ? $sale->sale_date->format('M d, Y') : $sale->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    <!-- FIX: Show N/A if product is deleted, else show product name -->
                                    @if($sale->isProductDeleted())
                                        <span class="text-red-500">N/A</span>
                                    @else
                                        <span class="text-gray-800">{{ $sale->getProductName() }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm">{{ $sale->quantity }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">₱{{ number_format($sale->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-800">₱{{ number_format($sale->total_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->user->name ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                    <p>No sales history found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delivery History Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg flex items-center">
                <i class="fas fa-truck mr-2"></i>
                Delivery History
            </h3>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cost</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Received By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $deliveries = $product->deliveries()
                                ->with(['user'])
                                ->latest()
                                ->limit(20)
                                ->get();
                        @endphp
                        
                        @forelse($deliveries as $delivery)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y') : $delivery->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $product->product_name }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm">{{ $delivery->quantity }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">₱{{ number_format($delivery->cost_price ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $delivery->user->name ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                    <p>No delivery history found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection