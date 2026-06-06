@extends('layouts.admin')

@section('title', 'Record New Delivery - REGASCO SIS')
@section('page-title', 'Record New Delivery')

@section('admin-content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Record New Delivery</h2>
        <p class="text-gray-500">Record a new product delivery from supplier</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-truck-loading mr-2"></i>
                New Delivery Record
            </h3>
        </div>

        <form method="POST" action="{{ route('admin.deliveries.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Supplier -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                <select name="supplier_id" id="supplierSelect" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white"
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
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white">
                    <option value="">Select Product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->product_id }}" data-supplier-id="{{ $product->supplier_id }}">
                            {{ $product->product_name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" required min="1" value="1"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Enter quantity">
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unit Cost -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Cost (₱) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="fas fa-peso-sign"></i>
                    </span>
                    <input type="number" name="unit_cost" required min="0" step="0.01"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="0.00">
                </div>
                @error('unit_cost')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Delivery Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date <span class="text-red-500">*</span></label>
                <input type="date" name="delivery_date" required value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                @error('delivery_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                    placeholder="Additional notes..."></textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Preview -->
            <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                <div class="flex justify-between items-center">
                    <span class="text-purple-700 font-medium">Total Cost Preview:</span>
                    <span class="text-2xl font-bold text-purple-600" id="totalPreview">₱0.00</span>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex space-x-3">
                <a href="{{ route('admin.deliveries.index') }}" 
                    class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-200 transition-all text-center">
                    Cancel
                </a>
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 text-white py-3 rounded-xl font-medium hover:from-purple-600 hover:to-purple-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>Record Delivery
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
        
        // Reset product selection
        productSelect.value = '';
        
        options.forEach(option => {
            if (option.value === '') {
                // Keep the default "Select Product..." option
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

    // Calculate total preview
    document.querySelector('input[name="quantity"]').addEventListener('input', updateTotal);
    document.querySelector('input[name="unit_cost"]').addEventListener('input', updateTotal);

    function updateTotal() {
        const quantity = parseFloat(document.querySelector('input[name="quantity"]').value) || 0;
        const unitCost = parseFloat(document.querySelector('input[name="unit_cost"]').value) || 0;
        const total = quantity * unitCost;
        
        document.getElementById('totalPreview').textContent = '₱' + total.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
</script>
@endsection