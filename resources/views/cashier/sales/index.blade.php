@extends('layouts.cashier')

@section('title', 'My Sales History - REGASCO SIS')
@section('page-title', 'My Sales History')

@section('cashier-content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-500">View and manage your sales transactions</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('cashier.sales.archived') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-archive"></i>
                <span>View Archived</span>
            </a>
            <a href="{{ route('cashier.sales.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-plus"></i>
                <span>New Sale</span>
            </a>
        </div>
    </div>

    <!-- Product Filter and Amount Search -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Filter Sales</h3>
        </div>
        
        <div id="activeFilter" class="hidden px-6 py-3 bg-blue-50 border-b border-blue-100 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Filtered by:</span>
                <span id="filterProductName" class="text-sm font-bold text-blue-600 bg-white px-3 py-1 rounded-full border border-blue-200"></span>
            </div>
            <button onclick="clearProductFilter()" class="text-sm text-red-500 hover:text-red-700 font-medium">
                <i class="fas fa-times mr-1"></i> Clear Filter
            </button>
        </div>

        <div class="p-6">
            <div class="flex flex-wrap gap-4">
                <button onclick="openProductFilterModal()" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition-all flex items-center">
                    <i class="fas fa-filter mr-2"></i> Select Product
                </button>
                
                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="number" id="amountSearch" placeholder="Search by amount (e.g. 1500)" 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        oninput="filterByAmount(this.value)">
                </div>
            </div>
        </div>
    </div>

    <!-- FIX: Scrollable table with max-height -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
            <table class="w-full" id="salesTable">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <!-- FIX: Added # column -->
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="salesTableBody">
                    @forelse($sales as $index => $sale)
                        <tr class="hover:bg-gray-50 transition-colors sale-row" 
                            data-product-id="{{ $sale->product_id }}"
                            data-product-name="{{ $sale->product_name ?? $sale->product->product_name ?? 'Deleted Product' }}"
                            data-amount="{{ $sale->total_price }}">
                            <!-- FIX: Display row number -->
                            <td class="px-4 py-4 text-center text-sm font-semibold text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $sale->sale_date->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $sale->product_name ?? $sale->product->product_name ?? 'Deleted Product' }}
                                @if(!$sale->product)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 ml-2">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Deleted
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-semibold">{{ $sale->quantity }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">₱{{ number_format($sale->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-800">₱{{ number_format($sale->total_price, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('cashier.sales.edit', $sale->sale_id) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="openDeleteModal({{ $sale->sale_id }}, '{{ $sale->product_name ?? $sale->product->product_name ?? 'Deleted Product' }}', {{ $sale->quantity }})" 
                                        class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="noSalesRow">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                <p>No sales found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Filter Modal -->
<div id="productFilterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto animate-fade-in">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex justify-between items-center sticky top-0">
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
                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    oninput="searchProducts(this.value)">
            </div>

            <div class="space-y-2 max-h-96 overflow-y-auto" id="productsList">
                @php
                    $allProducts = \App\Models\Product::where('is_active', true)->orderBy('product_name')->get();
                @endphp
                @forelse($allProducts as $product)
                    <button onclick="filterByProduct({{ $product->product_id }}, '{{ $product->product_name }}')" 
                        class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-blue-50 rounded-xl border border-gray-100 hover:border-blue-200 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-800">{{ $product->product_name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $product->sku }} | Stock: {{ $product->stock_quantity }}</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                    </button>
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

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="deleteModalContent">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-trash text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Delete Sale</h3>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">Are you sure you want to delete this sale?</p>
                    <p class="text-sm text-gray-500">Product: <span id="deleteProductName" class="font-semibold text-gray-700"></span></p>
                    <p class="text-sm text-gray-500">Quantity: <span id="deleteQuantity" class="font-semibold text-gray-700"></span></p>
                    <p class="text-sm text-red-500 mt-2 font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        This will restore stock and deduct from Today's Sales.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" 
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition-all font-medium text-sm">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all font-medium text-sm shadow-lg">
                    <i class="fas fa-trash mr-2"></i>Delete Sale
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let currentProductId = null;

    function openProductFilterModal() {
        document.getElementById('productFilterModal').classList.remove('hidden');
        document.getElementById('productSearchInput').focus();
    }

    function closeProductFilterModal() {
        document.getElementById('productFilterModal').classList.add('hidden');
    }

    function searchProducts(searchTerm) {
        const buttons = document.querySelectorAll('#productsList button');
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

    function filterByProduct(productId, productName) {
        currentProductId = productId;
        
        document.getElementById('activeFilter').classList.remove('hidden');
        document.getElementById('filterProductName').textContent = productName;
        
        const rows = document.querySelectorAll('.sale-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (parseInt(row.dataset.productId) === productId) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) noResultsRow.remove();
        
        if (visibleCount === 0) {
            const tbody = document.getElementById('salesTableBody');
            tbody.insertAdjacentHTML('beforeend', `
                <tr id="noResultsRow">
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                        <p>No sales found for this product</p>
                    </td>
                </tr>
            `);
        }
        
        closeProductFilterModal();
        
        const amountValue = document.getElementById('amountSearch').value;
        if (amountValue) {
            filterByAmount(amountValue);
        }
    }

    function clearProductFilter() {
        currentProductId = null;
        
        document.getElementById('activeFilter').classList.add('hidden');
        
        const rows = document.querySelectorAll('.sale-row');
        rows.forEach(row => row.classList.remove('hidden'));
        
        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) noResultsRow.remove();
        
        const amountValue = document.getElementById('amountSearch').value;
        if (amountValue) {
            filterByAmount(amountValue);
        }
    }

    function filterByAmount(amount) {
        const rows = document.querySelectorAll('.sale-row');
        const searchAmount = parseFloat(amount);
        
        rows.forEach(row => {
            if (currentProductId && parseInt(row.dataset.productId) !== currentProductId) {
                return;
            }
            
            const rowAmount = parseFloat(row.dataset.amount);
            
            if (!amount || rowAmount === searchAmount || rowAmount.toString().includes(amount)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
        
        updateNoResultsMessage();
    }

    function updateNoResultsMessage() {
        const visibleRows = document.querySelectorAll('.sale-row:not(.hidden)');
        const noResultsRow = document.getElementById('noResultsRow');
        const noSalesRow = document.getElementById('noSalesRow');
        
        if (visibleRows.length === 0 && !noResultsRow && !noSalesRow) {
            const tbody = document.getElementById('salesTableBody');
            tbody.insertAdjacentHTML('beforeend', `
                <tr id="noResultsRow">
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                        <p>No sales match your criteria</p>
                    </td>
                </tr>
            `);
        } else if (visibleRows.length > 0 && noResultsRow) {
            noResultsRow.remove();
        }
    }

    function openDeleteModal(saleId, productName, quantity) {
        document.getElementById('deleteProductName').textContent = productName;
        document.getElementById('deleteQuantity').textContent = quantity;
        
        const form = document.getElementById('deleteForm');
        form.action = '{{ route("cashier.sales.index") }}/' + saleId;
        
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteModalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('productFilterModal').addEventListener('click', function(e) {
        if (e.target === this) closeProductFilterModal();
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProductFilterModal();
            if (!document.getElementById('deleteModal').classList.contains('hidden')) {
                closeDeleteModal();
            }
        }
    });
</script>
@endsection