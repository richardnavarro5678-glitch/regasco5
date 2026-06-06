@extends('layouts.admin')

@section('title', 'Record Stock Adjustment - REGASCO SIS')
@section('page-title', 'Record Stock Adjustment')

@section('admin-content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Record Stock Adjustment</h2>
        <p class="text-gray-500">Record damaged, return in, or lost items</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-exchange-alt mr-2"></i>
                New Stock Adjustment
            </h3>
        </div>

        <form method="POST" action="{{ route('admin.adjustments.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Adjustment Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Type <span class="text-red-500">*</span></label>
                <select name="adjustment_type" id="adjustmentType" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white"
                    onchange="updateAdjustmentInfo(this.value)">
                    <option value="">Select Type...</option>
                    <option value="return_in">Return In (Customer Return - No Stock Change)</option>
                    <!-- FIX: damage_out now deducts stock -->
                    <option value="damage_out">Damaged (Warehouse Damage - Stock Deducted)</option>
                    <!-- FIX: New customer_damaged option -->
                    <option value="customer_damaged">Customer Damaged Return (No Stock Change)</option>
                    <option value="lost">Lost / Missing (Record Only - No Stock Change)</option>
                </select>
                @error('adjustment_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                <select name="product_id" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white">
                    <option value="">Select Product...</option>
                    @foreach($products as $product)
                        <!-- FIX: Added SKU in parenthesis format -->
                        <option value="{{ $product->product_id }}">
                            {{ $product->product_name }} ({{ $product->sku ?? 'N/A' }})
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
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Enter quantity">
                <p class="text-xs text-orange-500 mt-1" id="adjustmentInfo">
                    <i class="fas fa-info-circle mr-1"></i>
                    Select an adjustment type to see the effect
                </p>
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason <span class="text-red-500">*</span></label>
                <input type="text" name="reason" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Explain the reason for this adjustment">
                @error('reason')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Adjustment Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Date <span class="text-red-500">*</span></label>
                <input type="date" name="adjustment_date" required 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    value="{{ old('adjustment_date', now()->format('Y-m-d')) }}">
                @error('adjustment_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                    placeholder="Optional additional details..."></textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-3">
                <a href="{{ route('admin.adjustments.index') }}" 
                    class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-200 transition-all text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Cancel
                </a>
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-medium hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>Record Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateAdjustmentInfo(type) {
        const infoText = document.getElementById('adjustmentInfo');
        
        if (type === 'return_in') {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> <strong>No stock change</strong> - Item will be added to Return In pool only';
        } else if (type === 'damage_out') {
            // FIX: Updated info for damage_out (now deducts stock)
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> <strong>Stock deducted</strong> - Item will be recorded as damaged AND deducted from product stock';
        } else if (type === 'customer_damaged') {
            // FIX: New info for customer_damaged
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> <strong>No stock change</strong> - Customer returned damaged item - recorded only (no stock deduction)';
        } else if (type === 'lost') {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> <strong>No stock change</strong> - Item will be recorded as lost/missing only (no stock deduction)';
        } else {
            infoText.innerHTML = '<i class="fas fa-info-circle mr-1"></i> Select an adjustment type to see the effect';
        }
    }
</script>
@endsection