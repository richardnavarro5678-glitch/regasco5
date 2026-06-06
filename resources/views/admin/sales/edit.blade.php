@extends('layouts.admin')

@section('title', 'Edit Sale - REGASCO SIS')
@section('page-title', 'Edit Sale')

@section('admin-content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Sale
            </h3>
        </div>
        
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-4">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.sales.update', $sale) }}" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product <span class="text-red-500">*</span></label>
                    <select name="product_id" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Select product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->product_id }}" {{ old('product_id', $sale->product_id) == $product->product_id ? 'selected' : '' }}>
                                {{ $product->product_name }} (Stock: {{ $product->stock_quantity }}) - ₱{{ number_format($product->selling_price, 2) }}
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
                        placeholder="0"
                        value="{{ old('quantity', $sale->quantity) }}">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Sale Info -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Sale Info</label>
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <p class="text-sm text-gray-600">Unit Price: ₱{{ number_format($sale->unit_price, 2) }}</p>
                        <p class="text-sm text-gray-600">Total Price: ₱{{ number_format($sale->total_price, 2) }}</p>
                        <p class="text-sm text-gray-600">Cashier: {{ $sale->user->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-600">Date: {{ $sale->sale_date->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Sale notes">{{ old('notes', $sale->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.sales.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-8 py-3 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
                    <i class="fas fa-save"></i>
                    <span>Update Sale</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection