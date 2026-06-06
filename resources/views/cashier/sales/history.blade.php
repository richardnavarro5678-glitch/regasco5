@extends('layouts.cashier')

@section('title', 'My Sales - REGASCO SIS')
@section('page-title', 'My Sales History')

@section('cashier-content')
<div class="max-w-6xl mx-auto">
    <!-- Header with Filters -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#3b82f6] to-[#60a5fa] px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-white font-bold flex items-center text-lg">
                <i class="fas fa-history mr-2"></i>
                My Sales History
            </h3>
            
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Product Filter Button -->
                <button onclick="openProductFilterModal()" class="bg-white text-[#3b82f6] px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-all flex items-center space-x-2">
                    <i class="fas fa-filter mr-1"></i>
                    <span>Select Product</span>
                </button>
                
                <!-- Amount Search -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="number" id="amountSearch" placeholder="Search by amount..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent bg-white"
                        oninput="filterByAmount(this.value)">
                </div>
            </div>
        </div>
        
        <!-- Active Filter Display -->
        <div id="activeFilter" class="{{ request('product_id') ? '' : 'hidden' }} px-6 py-3 bg-blue-50 border-t border-blue-100 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Filtered by:</span>
                <span id="filterProductName" class="text-sm font-bold text-[#3b82f6] bg-white px-3 py-1 rounded-full border border-blue-200">
                    {{ request('product_name', '') }}
                </span>
            </div>
            <a href="{{ route('cashier.sales.history') }}" class="text-sm text-[#ef4444] hover:text-red-700 font-medium">
                <i class="fas fa-times mr-1"></i> Clear Filter
            </a>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-3">Product</th>
                            <th class="pb-3 text-center">Qty</th>
                            <th class="pb-3 text-right">Amount</th>
                            <th class="pb-3 text-right">Date</th>
                            <th class="pb-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="salesTableBody">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors sale-row" 
                                data-product-id="{{ $sale->product_id }}"
                                data-product-name="{{ $sale->product->product_name }}"
                                data-amount="{{ $sale->total_price }}">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-[#3b82f6]"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $sale->product->product_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $sale->product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-[#3b82f6]">
                                        {{ $sale->quantity }}
                                    </span>
                                </td>
                                <td class="py-4 text-right font-semibold text-gray-800">₱{{ number_format($sale->total_price, 2) }}</td>
                                <td class="py-4 text-right text-sm text-gray-400">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                <td class="py-4 text-center">
                                    <a href="{{ route('cashier.sales.show', $sale) }}" class="text-[#3b82f6] hover:text-blue-800 p-2" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="noSalesRow">
                                <td colspan="5" class="py-12 text-center text-gray-500">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-lg font-medium">No sales yet</p>
                                    <p class="text-sm text-gray-400">Start making sales to see your history here</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $sales->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Product Filter Modal -->
<div id="productFilterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto animate-fade-in">
        <div class="bg-gradient-to-r from-[#3b82f6] to-[#60a5fa] px-6 py-4 flex justify-between items-center sticky top-0">
            <h3 class="text-white font-bold flex items-center text-lg">
                <i class="fas fa-filter mr-2"></i>
                Select Product to Filter
            </h3>
            <button onclick="closeProductFilterModal()" class="text-white hover:text-blue-100 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="relative mb-6">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="productSearchInput" placeholder="Search products..." 
                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent"
                    oninput="searchProducts(this.value)">
            </div>

            <div class="space-y-2 max-h-96 overflow-y-auto" id="productsList">
                @php
                    $allProducts = \App\Models\Product::where('is_active', true)->orderBy('product_name')->get();
                @endphp
                @forelse($allProducts as $product)
                    <a href="{{ route('cashier.sales.history', ['product_id' => $product->product_id, 'product_name' => $product->product_name]) }}" 
                        class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-blue-50 rounded-xl border border-gray-100 hover:border-blue-200 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3 group-hover:from-blue-100 group-hover:to-blue-200">
                                <i class="fas fa-box text-[#3b82f6]"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-800">{{ $product->product_name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $product->sku }} | Stock: {{ $product->stock_quantity }}</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-[#3b82f6]"></i>
                    </a>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                        <p>No products found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="p-4 border-t border-gray-100 flex justify-end">
            <button onclick="closeProductFilterModal()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    function openProductFilterModal() {
        document.getElementById('productFilterModal').classList.remove('hidden');
        document.getElementById('productSearchInput').focus();
    }

    function closeProductFilterModal() {
        document.getElementById('productFilterModal').classList.add('hidden');
    }

    function searchProducts(searchTerm) {
        const buttons = document.querySelectorAll('#productsList a');
        const term = searchTerm.toLowerCase();
        
        buttons.forEach(button => {
            const productName = button.querySelector('p.font-semibold').textContent.toLowerCase();
            const sku = button.querySelector('p.text-xs').textContent.toLowerCase();
            
            if (productName.includes(term) || sku.includes(term)) {
                button.classList.remove('hidden');
            } else {
                button.classList.add('hidden');
            }
        });
    }

    function filterByAmount(amount) {
        const rows = document.querySelectorAll('.sale-row');
        const searchAmount = parseFloat(amount);
        
        rows.forEach(row => {
            const rowAmount = parseFloat(row.dataset.amount);
            
            if (!amount || rowAmount === searchAmount || rowAmount.toString().includes(amount)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    document.getElementById('productFilterModal').addEventListener('click', function(e) {
        if (e.target === this) closeProductFilterModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProductFilterModal();
        }
    });
</script>
@endsection