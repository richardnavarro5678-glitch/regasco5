@extends('layouts.admin')

@section('title', 'View Stock Adjustment - REGASCO SIS')
@section('page-title', 'View Stock Adjustment')

@section('admin-content')
<div class="mb-6">
    <a href="{{ route('admin.adjustments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
        <i class="fas fa-arrow-left mr-1"></i> Back to Stock Adjustments
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg">
                <i class="fas fa-eye mr-2"></i>
                Stock Adjustment #{{ $adjustment->adjustment_id }}
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Adjustment ID -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Adjustment ID</label>
                    <p class="text-lg font-semibold text-gray-800">#{{ $adjustment->adjustment_id }}</p>
                </div>

                <!-- Product -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Product</label>
                    <p class="text-lg font-semibold text-gray-800">
                        @if($adjustment->product)
                            {{ $adjustment->product->product_name }}
                        @else
                            <span class="text-red-500">Product not found</span>
                        @endif
                    </p>
                </div>

                <!-- Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                    <p class="text-lg font-semibold text-gray-800">
                        @php
                            $typeLabels = [
                                'return_in' => 'Return In',
                                'damage' => 'Damage',
                                'lost' => 'Lost / Missing',
                            ];
                            $typeColors = [
                                'return_in' => 'text-blue-600',
                                'damage' => 'text-red-600',
                                'lost' => 'text-orange-600',
                            ];
                        @endphp
                        <span class="{{ $typeColors[$adjustment->type] ?? 'text-gray-600' }}">
                            {{ $typeLabels[$adjustment->type] ?? ucfirst($adjustment->type) }}
                        </span>
                    </p>
                </div>

                <!-- Quantity -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Quantity</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $adjustment->quantity }}</p>
                </div>

                <!-- Reason -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Reason</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $adjustment->reason }}</p>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Notes</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $adjustment->notes ?? 'No notes' }}</p>
                </div>

                <!-- Adjusted By -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Adjusted By</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $adjustment->user->name ?? 'Unknown' }}</p>
                </div>

                <!-- Adjustment Date -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Adjustment Date</label>
                    <p class="text-lg font-semibold text-gray-800">
                        {{ $adjustment->adjustment_date ? \Carbon\Carbon::parse($adjustment->adjustment_date)->format('M d, Y') : $adjustment->created_at->format('M d, Y') }}
                    </p>
                </div>

                <!-- Created At -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $adjustment->created_at->format('M d, Y H:i') }}</p>
                </div>

                <!-- Updated At -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Updated At</label>
                    <p class="text-lg font-semibold text-gray-800">{{ $adjustment->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.adjustments.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    Back
                </a>
                <a href="{{ url('/admin/adjustments/' . $adjustment->adjustment_id . '/edit') }}" 
                    class="px-6 py-3 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection