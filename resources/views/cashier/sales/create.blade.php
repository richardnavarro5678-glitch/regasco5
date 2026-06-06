@extends('layouts.cashier')

@section('title', 'New Sale - REGASCO SIS')
@section('page-title', 'New Sale')

@section('cashier-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-cash-register mr-2"></i>
                Process New Sale
            </h3>
        </div>
        
        <form method="POST" action="{{ route('cashier.sales.store') }}" class="p-8" id="saleForm">
            @csrf
            
            <!-- Product Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Product <span class="text-red-500">*</span></label>
                <select name="product_id" id="productSelect" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white"
                    onchange="updateProductInfo()">
                    <option value="">Choose a product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->product_id }}" 
                            data-price="{{ $product->selling_price ?? 0 }}"
                            data-stock="{{ $product->stock_quantity ?? 0 }}">
                            {{ $product->product_name }} (₱{{ number_format($product->selling_price ?? 0, 2) }}) - Stock: {{ $product->stock_quantity ?? 0 }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity with +/- buttons -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="decrementQuantity()" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-minus text-gray-600"></i>
                    </button>
                    <input type="number" name="quantity" id="quantity" required min="1" value="1"
                        class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-center font-semibold"
                        oninput="calculateTotal()">
                    <button type="button" onclick="incrementQuantity()" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-plus text-gray-600"></i>
                    </button>
                </div>
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes (Optional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                    placeholder="Additional notes...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Summary -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Unit Price:</span>
                    <span class="font-semibold" id="unitPriceDisplay">₱0.00</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-600">Quantity:</span>
                    <span class="font-semibold" id="quantityDisplay">1</span>
                </div>
                <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">Total Amount:</span>
                    <span class="text-2xl font-bold text-green-600" id="totalAmount">₱0.00</span>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('cashier.sales.index') }}" class="text-gray-600 hover:text-gray-800 font-medium flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Cancel
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-xl shadow-lg flex items-center space-x-2 transition-colors">
                    <i class="fas fa-check-circle"></i>
                    <span>Complete Sale</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let unitPrice = 0;
    let availableStock = 0;

    function updateProductInfo() {
        const select = document.getElementById('productSelect');
        const option = select.options[select.selectedIndex];
        
        if (select.value) {
            // FIX: Parse price properly with fallback
            const rawPrice = option.getAttribute('data-price');
            unitPrice = parseFloat(rawPrice) || 0;
            
            const rawStock = option.getAttribute('data-stock');
            availableStock = parseInt(rawStock) || 0;
            
            document.getElementById('unitPriceDisplay').textContent = '₱' + unitPrice.toFixed(2);
            
            // Update quantity max
            document.getElementById('quantity').max = availableStock;
        } else {
            unitPrice = 0;
            availableStock = 0;
            document.getElementById('unitPriceDisplay').textContent = '₱0.00';
        }
        
        calculateTotal();
    }

    function incrementQuantity() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value) || 0;
        if (currentValue < availableStock) {
            input.value = currentValue + 1;
            calculateTotal();
        }
    }

    function decrementQuantity() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value) || 0;
        if (currentValue > 1) {
            input.value = currentValue - 1;
            calculateTotal();
        }
    }

    function calculateTotal() {
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const total = unitPrice * quantity;
        
        document.getElementById('quantityDisplay').textContent = quantity;
        document.getElementById('totalAmount').textContent = '₱' + total.toFixed(2);
    }

    // Form validation
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        const select = document.getElementById('productSelect');
        const quantity = parseInt(document.getElementById('quantity').value) || 0;

        if (!select.value) {
            e.preventDefault();
            alert('Please select a product!');
            return false;
        }

        if (quantity > availableStock) {
            e.preventDefault();
            alert('Quantity exceeds available stock! Only ' + availableStock + ' available.');
            return false;
        }

        if (quantity < 1) {
            e.preventDefault();
            alert('Quantity must be at least 1!');
            return false;
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateProductInfo();
    });
</script>
@endsection