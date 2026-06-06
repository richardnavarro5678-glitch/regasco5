@extends('layouts.admin')

@section('title', 'Edit Stock Adjustment - REGASCO SIS')
@section('page-title', 'Edit Stock Adjustment')

@section('admin-content')
<div class="mb-6">
    <a href="{{ route('admin.adjustments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Stock Adjustments
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
                Edit Stock Adjustment #{{ $adjustment->adjustment_id }}
            </h3>
        </div>
        
        <form method="POST" action="{{ url('/admin/adjustments/' . $adjustment->adjustment_id) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Product -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Product <span class="text-red-500">*</span>
                </label>
                <select name="product_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $adjustment->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }} (Stock: {{ $product->stock_quantity }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Type <span class="text-red-500">*</span>
                </label>
                <select name="type" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="return_in" {{ $adjustment->type == 'return_in' ? 'selected' : '' }}>Return In</option>
                    <option value="damage" {{ $adjustment->type == 'damage' ? 'selected' : '' }}>Damage</option>
                    <option value="lost" {{ $adjustment->type == 'lost' ? 'selected' : '' }}>Lost / Missing</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity" min="1" required
                    value="{{ $adjustment->quantity }}"
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
                    value="{{ $adjustment->reason }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter reason for adjustment">
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
                    placeholder="Additional notes...">{{ $adjustment->notes }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.adjustments.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Adjustment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection