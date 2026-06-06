@extends('layouts.admin')

@section('title', 'Edit Supplier Return - REGASCO SIS')
@section('page-title', 'Edit Supplier Return')

@section('admin-content')
<div class="mb-6">
    <a href="{{ route('admin.supplier-returns.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Supplier Returns
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg">
                <i class="fas fa-edit mr-2"></i>
                Edit Supplier Return #{{ $supplierReturn->return_id }}
            </h3>
        </div>
        
        <form method="POST" action="{{ url('/admin/supplier-returns/' . $supplierReturn->return_id) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Supplier -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Supplier <span class="text-red-500">*</span>
                </label>
                <select name="supplier_id" id="supplier_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $supplierReturn->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name ?? $supplier->name ?? 'Supplier #' . $supplier->id }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Product <span class="text-red-500">*</span>
                </label>
                <select name="product_id" id="product_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    <option value="">-- Select Product --</option>
                    @foreach($allProducts as $product)
                        <option value="{{ $product->product_id }}" 
                            data-supplier="{{ $product->supplier_id }}"
                            {{ $supplierReturn->product_id == $product->product_id ? 'selected' : '' }}
                            class="product-option {{ $supplierReturn->supplier_id != $product->supplier_id ? 'hidden' : '' }}">
                            {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p id="product-message" class="text-yellow-600 text-sm mt-1 hidden">
                    <i class="fas fa-exclamation-triangle mr-1"></i> No products for this supplier
                </p>
            </div>

            <!-- Return In Record -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Return In Record (Optional)
                </label>
                <select name="adjustment_id" id="adjustment_id"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- No Return In Link --</option>
                    @foreach($availableAdjustments as $adjustment)
                        <option value="{{ $adjustment->adjustment_id }}" 
                            {{ $supplierReturn->adjustment_id == $adjustment->adjustment_id ? 'selected' : '' }}
                            data-product="{{ $adjustment->product_id }}" 
                            data-qty="{{ $adjustment->quantity }}">
                            #{{ $adjustment->adjustment_id }} - {{ $adjustment->product->product_name ?? 'Unknown Product' }}
                            (Qty: {{ $adjustment->quantity }}) - {{ $adjustment->created_at->format('M d, Y') }}
                        </option>
                    @endforeach
                </select>
                @error('adjustment_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity" min="1" required
                    value="{{ $supplierReturn->quantity }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter quantity">
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reason -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Reason <span class="text-red-500">*</span>
                </label>
                <input type="text" name="reason" required
                    value="{{ $supplierReturn->reason }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter reason for return">
                @error('reason')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Notes (Optional)
                </label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Additional notes...">{{ $supplierReturn->notes }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="pending" {{ $supplierReturn->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="shipped" {{ $supplierReturn->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="received" {{ $supplierReturn->status == 'received' ? 'selected' : '' }}>Received</option>
                    <option value="completed" {{ $supplierReturn->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ $supplierReturn->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tracking Number -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tracking Number (Optional)
                </label>
                <input type="text" name="tracking_number"
                    value="{{ $supplierReturn->tracking_number }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter tracking number">
                @error('tracking_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Shipped Date -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Shipped Date (Optional)
                </label>
                <input type="date" name="shipped_date"
                    value="{{ $supplierReturn->shipped_date ? $supplierReturn->shipped_date->format('Y-m-d') : '' }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('shipped_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Received Date -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Received Date (Optional)
                </label>
                <input type="date" name="received_date"
                    value="{{ $supplierReturn->received_date ? $supplierReturn->received_date->format('Y-m-d') : '' }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('received_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Refund Status -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Refund Status (Optional)
                </label>
                <select name="refund_status"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Select Refund Status --</option>
                    <option value="pending" {{ $supplierReturn->refund_status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ $supplierReturn->refund_status == 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="completed" {{ $supplierReturn->refund_status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ $supplierReturn->refund_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('refund_status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Refund Amount -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Refund Amount (Optional)
                </label>
                <input type="number" name="refund_amount" step="0.01" min="0"
                    value="{{ $supplierReturn->refund_amount }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter refund amount">
                @error('refund_amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.supplier-returns.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Supplier Return
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // SIMPLIFIED: No AJAX - filter products via JavaScript
    document.getElementById('supplier_id').addEventListener('change', function() {
        const supplierId = this.value;
        const productSelect = document.getElementById('product_id');
        const productMessage = document.getElementById('product-message');
        const allOptions = productSelect.querySelectorAll('.product-option');
        
        // Reset
        productSelect.innerHTML = '<option value="">-- Select Product --</option>';
        productMessage.classList.add('hidden');
        
        if (!supplierId) {
            productSelect.innerHTML = '<option value="">-- Select Supplier First --</option>';
            productSelect.classList.add('bg-gray-100');
            productSelect.disabled = true;
            return;
        }
        
        let hasProducts = false;
        
        // Filter products by supplier
        allOptions.forEach(option => {
            if (option.getAttribute('data-supplier') === supplierId) {
                const newOption = document.createElement('option');
                newOption.value = option.value;
                newOption.textContent = option.textContent;
                productSelect.appendChild(newOption);
                hasProducts = true;
            }
        });
        
        if (hasProducts) {
            productSelect.disabled = false;
            productSelect.classList.remove('bg-gray-100');
            productSelect.classList.add('bg-white');
            productSelect.focus();
        } else {
            productSelect.innerHTML = '<option value="">-- No products for this supplier --</option>';
            productSelect.disabled = true;
            productSelect.classList.add('bg-gray-100');
            productMessage.classList.remove('hidden');
        }
    });

    // Auto-fill product and quantity when Return In is selected
    document.getElementById('adjustment_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const productId = selectedOption.getAttribute('data-product');
            const qty = selectedOption.getAttribute('data-qty');
            
            if (productId) {
                document.getElementById('product_id').value = productId;
            }
            if (qty) {
                document.getElementById('quantity').value = qty;
            }
        }
    });
</script>
@endsection