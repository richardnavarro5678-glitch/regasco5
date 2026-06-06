@extends('layouts.admin')

@section('title', 'Record Supplier Return - REGASCO SIS')
@section('page-title', 'Record Supplier Return')

@section('admin-content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Record Supplier Return</h2>
        <p class="text-gray-500">Return products to supplier</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-undo-alt mr-2"></i>
                New Supplier Return
            </h3>
        </div>

        <form method="POST" action="{{ route('admin.supplier-returns.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Supplier -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                <select name="supplier_id" id="supplierSelect" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    onchange="filterProductsBySupplier(this.value)">
                    <option value="">Select Supplier...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                <select name="product_id" id="productSelect" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    onchange="updatePoolInfo()">
                    <option value="">Select Product...</option>
                    @foreach($products as $product)
                        @php
                            // FIX: Calculate pool balances
                            $returnInPool = $product->stockAdjustments->where('adjustment_type', 'return_in')->sum('total');
                            $damagedPool = $product->stockAdjustments->where('adjustment_type', 'damage_out')->sum('total');
                            $customerDamagedPool = $product->stockAdjustments->where('adjustment_type', 'customer_damaged')->sum('total');
                        @endphp
                        <option value="{{ $product->product_id }}" 
                            data-supplier-id="{{ $product->supplier_id }}"
                            data-return-in="{{ $returnInPool }}"
                            data-damaged="{{ $damagedPool }}"
                            data-customer-damaged="{{ $customerDamagedPool }}"
                            data-stock="{{ $product->stock_quantity }}">
                            {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Product must belong to selected supplier</p>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- FIX: Pool Balance Display -->
            <div id="poolBalanceDisplay" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">Available Pools for Selected Product:</h4>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-lg p-3 text-center border border-blue-100">
                        <p class="text-xs text-gray-500">Return In</p>
                        <p class="text-lg font-bold text-blue-600" id="returnInBalance">0</p>
                    </div>
                    <div class="bg-white rounded-lg p-3 text-center border border-red-100">
                        <p class="text-xs text-gray-500">Damaged</p>
                        <p class="text-lg font-bold text-red-600" id="damagedBalance">0</p>
                    </div>
                    <div class="bg-white rounded-lg p-3 text-center border border-orange-100">
                        <p class="text-xs text-gray-500">Customer Damaged</p>
                        <p class="text-lg font-bold text-orange-600" id="customerDamagedBalance">0</p>
                    </div>
                </div>
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" id="quantityInput" required min="1" value="1"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter quantity">
                <p class="text-xs text-orange-500 mt-1" id="quantityInfo">
                    <i class="fas fa-info-circle mr-1"></i>
                    Select a reason first to see which pool will be deducted
                </p>
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason <span class="text-red-500">*</span></label>
                <select name="reason" id="reasonSelect" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    onchange="updateQuantityInfo(this.value)">
                    <option value="">Select Reason...</option>
                    <option value="empty">Empty / No Content</option>
                    <option value="defective">Defective / Damaged</option>
                    <option value="customer_damaged">Customer Damaged Return</option>
                    <option value="other">Other</option>
                </select>
                @error('reason')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    placeholder="Additional notes..."></textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <a href="{{ route('admin.supplier-returns.index') }}" 
                    class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-200 transition-all text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Cancel
                </a>
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>Record Return
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Filter products based on selected supplier
    function filterProductsBySupplier(supplierId) {
        const productSelect = document.getElementById('productSelect');
        const options = productSelect.querySelectorAll('option');
        
        productSelect.value = '';
        document.getElementById('poolBalanceDisplay').classList.add('hidden');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const productSupplierId = option.getAttribute('data-supplier-id');
                
                if (supplierId === '' || productSupplierId === supplierId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        });
    }

    // FIX: Update pool balance display when product is selected
    function updatePoolInfo() {
        const selectedOption = document.getElementById('productSelect').selectedOptions[0];
        const poolDisplay = document.getElementById('poolBalanceDisplay');
        
        if (selectedOption && selectedOption.value !== '') {
            document.getElementById('returnInBalance').textContent = selectedOption.getAttribute('data-return-in') || '0';
            document.getElementById('damagedBalance').textContent = selectedOption.getAttribute('data-damaged') || '0';
            document.getElementById('customerDamagedBalance').textContent = selectedOption.getAttribute('data-customer-damaged') || '0';
            poolDisplay.classList.remove('hidden');
        } else {
            poolDisplay.classList.add('hidden');
        }
        
        updateQuantityInfo(document.getElementById('reasonSelect').value);
    }

    // Update info text based on selected reason
    function updateQuantityInfo(reason) {
        const infoText = document.getElementById('quantityInfo');
        const selectedOption = document.getElementById('productSelect').selectedOptions[0];
        
        let availableBalance = 0;
        let poolName = '';
        
        if (selectedOption && selectedOption.value !== '') {
            if (reason === 'empty') {
                availableBalance = parseInt(selectedOption.getAttribute('data-return-in')) || 0;
                poolName = 'Return In';
            } else if (reason === 'defective') {
                availableBalance = parseInt(selectedOption.getAttribute('data-damaged')) || 0;
                poolName = 'Damaged';
            } else if (reason === 'customer_damaged') {
                availableBalance = parseInt(selectedOption.getAttribute('data-customer-damaged')) || 0;
                poolName = 'Customer Damaged';
            } else if (reason === 'other') {
                availableBalance = parseInt(selectedOption.getAttribute('data-stock')) || 0;
                poolName = 'Product Stock';
            }
        }

        if (reason === 'empty') {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> This will deduct from the <strong>Return In</strong> pool. Available: <strong>' + availableBalance + '</strong>';
        } else if (reason === 'defective') {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> This will deduct from the <strong>Damaged</strong> pool. Available: <strong>' + availableBalance + '</strong>';
        } else if (reason === 'customer_damaged') {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> This will deduct from the <strong>Customer Damaged</strong> pool. Available: <strong>' + availableBalance + '</strong>';
        } else if (reason === 'other') {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> This will deduct from <strong>Product Stock</strong> directly. Available: <strong>' + availableBalance + '</strong>';
        } else {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> Select a reason first to see which pool will be deducted';
        }
    }

    // Validate quantity against selected pool
    document.getElementById('quantityInput').addEventListener('input', function() {
        const reason = document.getElementById('reasonSelect').value;
        const selectedOption = document.getElementById('productSelect').selectedOptions[0];
        
        if (!reason || !selectedOption || selectedOption.value === '') return;
        
        let maxQty = 0;
        if (reason === 'empty') {
            maxQty = parseInt(selectedOption.getAttribute('data-return-in')) || 0;
        } else if (reason === 'defective') {
            maxQty = parseInt(selectedOption.getAttribute('data-damaged')) || 0;
        } else if (reason === 'customer_damaged') {
            maxQty = parseInt(selectedOption.getAttribute('data-customer-damaged')) || 0;
        } else if (reason === 'other') {
            maxQty = parseInt(selectedOption.getAttribute('data-stock')) || 0;
        }
        
        if (parseInt(this.value) > maxQty) {
            this.setCustomValidity('Quantity cannot exceed available ' + (reason === 'other' ? 'stock' : 'pool') + ' (' + maxQty + ')');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endsection