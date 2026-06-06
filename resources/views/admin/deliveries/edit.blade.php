@extends('layouts.admin')

@section('title', 'Edit Delivery - REGASCO SIS')
@section('page-title', 'Edit Delivery')

@section('admin-content')
<div class="mb-6">
    <a href="{{ route('admin.deliveries.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Deliveries
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Delivery
            </h3>
        </div>
        
        <form method="POST" action="{{ route('admin.deliveries.update', $delivery) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Select Supplier...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id', $delivery->supplier_id) == $supplier->supplier_id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
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
                        onchange="updateUnitCost()">
                        <option value="">Select Product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->product_id }}" 
                                data-cost-price="{{ $product->cost_price }}"
                                {{ old('product_id', $delivery->product_id) == $product->product_id ? 'selected' : '' }}>
                                {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
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
                    <input type="number" name="quantity" required min="1"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ old('quantity', $delivery->quantity) }}">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- FIX: Unit Cost - Uses current product's cost_price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Cost (₱) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-500">₱</span>
                        <!-- FIX: value uses $delivery->product->cost_price to show current product cost -->
                        <input type="number" name="unit_cost" id="unitCostInput" required min="0" step="0.01"
                            class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('unit_cost', $delivery->product->cost_price ?? $delivery->unit_cost) }}">
                    </div>
                    @error('unit_cost')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date <span class="text-red-500">*</span></label>
                    <input type="date" name="delivery_date" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ old('delivery_date', $delivery->delivery_date->format('Y-m-d')) }}">
                    @error('delivery_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Delivery notes...">{{ old('notes', $delivery->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.deliveries.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Update Delivery
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // FIX: Update unit cost when product changes - uses selected product's cost_price
    function updateUnitCost() {
        const productSelect = document.getElementById('productSelect');
        const selectedOption = productSelect.selectedOptions[0];
        const unitCostInput = document.getElementById('unitCostInput');
        
        if (selectedOption && selectedOption.value !== '') {
            const costPrice = selectedOption.getAttribute('data-cost-price');
            if (costPrice) {
                unitCostInput.value = parseFloat(costPrice).toFixed(2);
            }
        }
    }
</script>
@endsection