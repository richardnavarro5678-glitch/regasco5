@extends('layouts.admin')

@section('title', 'Admin Dashboard - REGASCO SIS')
@section('page-title', 'Dashboard Overview')

@section('admin-content')
<!-- Stats Grid - 4 cards with COLORS from screenshot -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- 1. Today's Sales - CYAN (HIGHLIGHTED) -->
    <div class="bg-white rounded-2xl p-6 shadow-lg shadow-cyan-200/50 hover-lift border-2 border-cyan-400 ring-2 ring-cyan-100 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-300 via-cyan-500 to-cyan-300"></div>
        
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-cyan-600 mb-1 flex items-center">
                    <i class="fas fa-star mr-1 text-xs"></i> Today's Sales
                </p>
                <h3 class="text-3xl font-bold text-gray-800">₱{{ number_format($todaySales->total_amount ?? 0, 2) }}</h3>
            </div>
            <div class="w-14 h-14 bg-[#06b6d4] rounded-2xl flex items-center justify-center shadow-lg shadow-cyan-200">
                <i class="fas fa-peso-sign text-white text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-gray-500">
            <span>{{ $todaySales->total_transactions ?? 0 }} transactions today</span>
        </div>
    </div>

    <!-- 2. Total Products - BLUE -->
    <div class="bg-white rounded-2xl p-6 shadow-lg hover-lift border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Products</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</h3>
            </div>
            <div class="w-14 h-14 bg-[#3b82f6] rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-box text-white text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-green-600">
            <i class="fas fa-arrow-up mr-1"></i>
            <span>Active inventory</span>
        </div>
    </div>

    <!-- 3. Selling Price Value Card - PURPLE -->
    <div class="bg-white rounded-2xl p-6 shadow-lg hover-lift border border-gray-100 cursor-pointer" onclick="openSellingPriceModal()">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Selling Price Value</p>
                <h3 class="text-3xl font-bold text-gray-800">₱{{ number_format($totalSellingPriceValue, 2) }}</h3>
            </div>
            <div class="w-14 h-14 bg-[#a855f7] rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-tags text-white text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-[#a855f7] hover:text-purple-700 transition-colors">
            <i class="fas fa-eye mr-1"></i>
            <span class="underline">Click to view selling price valuation</span>
        </div>
    </div>

    <!-- 4. Inventory Value - INDIGO -->
    <div class="bg-white rounded-2xl p-6 shadow-lg hover-lift border border-gray-100 cursor-pointer" onclick="openInventoryModal()">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Inventory Value</p>
                <h3 class="text-3xl font-bold text-gray-800">₱{{ number_format($totalInventoryValue, 2) }}</h3>
            </div>
            <div class="w-14 h-14 bg-[#6366f1] rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-warehouse text-white text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-[#3b82f6] hover:text-blue-700 transition-colors">
            <i class="fas fa-eye mr-1"></i>
            <span class="underline">Click to view cost price valuation</span>
        </div>
    </div>
</div>

<!-- FIX: Swapped positions - Recent Sales (LEFT) + Low Stock Alerts (RIGHT) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Sales - BLUE (NOW LEFT) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-[#3b82f6] to-[#60a5fa] px-6 py-4 flex justify-between items-center">
                <h3 class="text-white font-bold flex items-center">
                    <i class="fas fa-receipt mr-2"></i>
                    Recent Sales
                </h3>
                <a href="{{ route('admin.sales.index') }}" class="text-white text-sm hover:underline">View All</a>
            </div>
            
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full" id="salesTable">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-3">Product</th>
                                <th class="pb-3">Cashier</th>
                                <th class="pb-3">Qty</th>
                                <th class="pb-3 text-right">Amount</th>
                                <th class="pb-3 text-right">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="salesTableBody">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-gray-50 transition-colors sale-row" 
                                    data-product-id="{{ $sale->product_id ?? '' }}"
                                    data-product-name="{{ $sale->product->product_name ?? 'Deleted Product' }}"
                                    data-amount="{{ $sale->total_price }}">
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 {{ $sale->product ? 'bg-blue-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas {{ $sale->product ? 'fa-box text-[#3b82f6]' : 'fa-trash text-gray-400' }} text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 text-sm">{{ $sale->product->product_name ?? 'Deleted Product' }}</p>
                                                <p class="text-xs text-gray-400">{{ $sale->product->sku ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600">{{ $sale->user->name ?? 'Unknown' }}</td>
                                    <td class="py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-[#3b82f6]">
                                            {{ $sale->quantity }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right font-semibold text-gray-800">₱{{ number_format($sale->total_price, 2) }}</td>
                                    <td class="py-4 text-right text-xs text-gray-400">{{ $sale->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr id="noSalesRow">
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                        <p>No recent sales</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts - ORANGE/RED (NOW RIGHT) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-[#f97316] to-[#ea580c] px-6 py-4">
                <h3 class="text-white font-bold flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Low Stock Alerts
                </h3>
            </div>
            <div class="p-6">
                @if($lowStockProducts->count() > 0)
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($lowStockProducts as $product)
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl border-l-4 border-[#ef4444]">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $product->product_name }}</p>
                                    <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                                    <p class="text-xs text-gray-400">{{ $product->supplier->supplier_name ?? 'No Supplier' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-[#ef4444]">
                                        {{ $product->stock_quantity }} left
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">Min: {{ $product->low_stock_threshold }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check text-[#22c55e] text-2xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">All stocks are healthy!</p>
                        <p class="text-sm text-gray-400">No low stock alerts</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions - COLORED ICONS -->
<div class="mt-8">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.products.create') }}" class="group p-6 bg-white rounded-2xl shadow-md hover:shadow-xl transition-all border border-gray-100 hover:border-blue-200 text-center">
            <div class="w-12 h-12 bg-[#3b82f6] rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus text-white"></i>
            </div>
            <p class="font-semibold text-gray-700 text-sm">Add Product</p>
        </a>
        
        <a href="{{ route('admin.deliveries.create') }}" class="group p-6 bg-white rounded-2xl shadow-md hover:shadow-xl transition-all border border-gray-100 hover:border-green-200 text-center">
            <div class="w-12 h-12 bg-[#22c55e] rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-dolly text-white"></i>
            </div>
            <p class="font-semibold text-gray-700 text-sm">Record Delivery</p>
        </a>
        
        <a href="{{ route('admin.cashiers.create') }}" class="group p-6 bg-white rounded-2xl shadow-md hover:shadow-xl transition-all border border-gray-100 hover:border-purple-200 text-center">
            <div class="w-12 h-12 bg-[#a855f7] rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-user-plus text-white"></i>
            </div>
            <p class="font-semibold text-gray-700 text-sm">Add Cashier</p>
        </a>
        
        <a href="{{ route('admin.reports.index') }}" class="group p-6 bg-white rounded-2xl shadow-md hover:shadow-xl transition-all border border-gray-100 hover:border-orange-200 text-center">
            <div class="w-12 h-12 bg-[#f97316] rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-pie text-white"></i>
            </div>
            <p class="font-semibold text-gray-700 text-sm">View Reports</p>
        </a>
    </div>
</div>

<!-- Inventory Value Modal -->
<div id="inventoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-y-auto animate-fade-in">
        <div class="bg-gradient-to-r from-[#6366f1] to-[#818cf8] px-6 py-4 flex justify-between items-center sticky top-0">
            <h3 class="text-white font-bold flex items-center text-lg">
                <i class="fas fa-warehouse mr-2"></i>
                Cost Price Valuation Breakdown
            </h3>
            <button onclick="closeInventoryModal()" class="text-white hover:text-indigo-100 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="bg-indigo-50 rounded-xl p-4 mb-6 border border-indigo-100">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">Total Inventory Value:</span>
                    <span class="text-2xl font-bold text-[#6366f1]">₱{{ number_format($totalInventoryValue, 2) }}</span>
                </div>
            </div>

            <table class="w-full">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SKU</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cost Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $products = \App\Models\Product::with('supplier')->where('is_active', true)->get();
                    @endphp
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $product->product_name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->supplier->supplier_name ?? 'No Supplier' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $product->sku }}</td>
                            <td class="px-4 py-3 text-center text-sm">{{ $product->stock_quantity }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-600">₱{{ number_format($product->cost_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">₱{{ number_format($product->stock_quantity * $product->cost_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-gray-700">TOTAL:</td>
                        <td class="px-4 py-3 text-right text-[#6366f1] text-lg">₱{{ number_format($totalInventoryValue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 flex justify-end">
            <button onclick="closeInventoryModal()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Selling Price Value Modal -->
<div id="sellingPriceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-y-auto animate-fade-in">
        <div class="bg-gradient-to-r from-[#a855f7] to-[#c084fc] px-6 py-4 flex justify-between items-center sticky top-0">
            <h3 class="text-white font-bold flex items-center text-lg">
                <i class="fas fa-tags mr-2"></i>
                Selling Price Valuation Breakdown
            </h3>
            <button onclick="closeSellingPriceModal()" class="text-white hover:text-purple-100 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="bg-purple-50 rounded-xl p-4 mb-6 border border-purple-100">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">Total Selling Price Value:</span>
                    <span class="text-2xl font-bold text-[#a855f7]">₱{{ number_format($totalSellingPriceValue, 2) }}</span>
                </div>
            </div>

            <table class="w-full">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SKU</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Selling Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $products = \App\Models\Product::with('supplier')->where('is_active', true)->get();
                    @endphp
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $product->product_name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->supplier->supplier_name ?? 'No Supplier' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $product->sku }}</td>
                            <td class="px-4 py-3 text-center text-sm">{{ $product->stock_quantity }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-600">₱{{ number_format($product->selling_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">₱{{ number_format($product->stock_quantity * $product->selling_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-gray-700">TOTAL:</td>
                        <td class="px-4 py-3 text-right text-[#a855f7] text-lg">₱{{ number_format($totalSellingPriceValue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 flex justify-end">
            <button onclick="closeSellingPriceModal()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function openInventoryModal() {
        document.getElementById('inventoryModal').classList.remove('hidden');
    }

    function closeInventoryModal() {
        document.getElementById('inventoryModal').classList.add('hidden');
    }

    function openSellingPriceModal() {
        document.getElementById('sellingPriceModal').classList.remove('hidden');
    }

    function closeSellingPriceModal() {
        document.getElementById('sellingPriceModal').classList.add('hidden');
    }

    document.getElementById('inventoryModal').addEventListener('click', function(e) {
        if (e.target === this) closeInventoryModal();
    });

    document.getElementById('sellingPriceModal').addEventListener('click', function(e) {
        if (e.target === this) closeSellingPriceModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeInventoryModal();
            closeSellingPriceModal();
        }
    });
</script>
@endsection