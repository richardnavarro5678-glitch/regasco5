@extends('layouts.admin')

@section('title', 'Stock Adjustments - REGASCO SIS')
@section('page-title', 'Stock Adjustments')

@section('admin-content')
<!-- FIX: Added flex container with button on right -->
<div class="mb-6 flex items-center justify-between">
    <p class="text-gray-500 text-sm">Select an adjustment type to view and manage records</p>
    <a href="{{ route('admin.adjustments.create') }}" 
       class="bg-[#f97316] hover:bg-[#ea580c] text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
        <i class="fas fa-plus text-sm"></i>
        <span class="font-medium">New Adjustment</span>
    </a>
</div>

<!-- FIX: Changed from 3 columns to 4 columns (2x2 on md) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Return In Card -->
    <a href="{{ route('admin.adjustments.type', 'return_in') }}" class="group block bg-white rounded-2xl shadow-md border border-blue-200 hover:shadow-xl hover:border-blue-400 transition-all duration-300 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-undo-alt text-blue-600 text-xl"></i>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                    {{ $typeStats['return_in']['count'] ?? 0 }} products
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Return In</h3>
            <p class="text-sm text-gray-500 mb-3">Customer returns pool (empty/damaged - no stock change)</p>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-sm font-semibold text-blue-600">
                    {{ $typeStats['return_in']['total_quantity'] ?? 0 }} units available
                </span>
                <span class="text-xs text-gray-400">
                    Pool system
                </span>
            </div>
        </div>
        <div class="bg-blue-50 px-6 py-3 flex items-center justify-between group-hover:bg-blue-100 transition-colors">
            <span class="text-sm font-medium text-blue-700">View Records</span>
            <i class="fas fa-arrow-right text-blue-600 group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    <!-- Damaged Card - FIX: Now shows "Stock deducted" -->
    <a href="{{ route('admin.adjustments.type', 'damage_out') }}" class="group block bg-white rounded-2xl shadow-md border border-red-200 hover:shadow-xl hover:border-red-400 transition-all duration-300 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition-colors">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                    {{ $typeStats['damage_out']['count'] ?? 0 }} records
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Damaged</h3>
            <p class="text-sm text-gray-500 mb-3">Broken or damaged stock - Stock deducted</p>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-sm font-semibold text-red-600">
                    {{ $typeStats['damage_out']['total_quantity'] ?? 0 }} items
                </span>
                <span class="text-xs text-gray-400">
                    @if($typeStats['damage_out']['last_adjustment'])
                        Last: {{ $typeStats['damage_out']['last_adjustment']->adjustment_date->format('M d') }}
                    @else
                        No records
                    @endif
                </span>
            </div>
        </div>
        <div class="bg-red-50 px-6 py-3 flex items-center justify-between group-hover:bg-red-100 transition-colors">
            <span class="text-sm font-medium text-red-700">View Records</span>
            <i class="fas fa-arrow-right text-red-600 group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    <!-- FIX: Customer Damaged Card (NEW) -->
    <a href="{{ route('admin.adjustments.type', 'customer_damaged') }}" class="group block bg-white rounded-2xl shadow-md border border-orange-200 hover:shadow-xl hover:border-orange-400 transition-all duration-300 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                    <i class="fas fa-user-times text-orange-600 text-xl"></i>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                    {{ $typeStats['customer_damaged']['count'] ?? 0 }} records
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Customer Damaged</h3>
            <p class="text-sm text-gray-500 mb-3">Customer returned damaged - No stock change</p>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-sm font-semibold text-orange-600">
                    {{ $typeStats['customer_damaged']['total_quantity'] ?? 0 }} items
                </span>
                <span class="text-xs text-gray-400">
                    @if($typeStats['customer_damaged']['last_adjustment'])
                        Last: {{ $typeStats['customer_damaged']['last_adjustment']->adjustment_date->format('M d') }}
                    @else
                        No records
                    @endif
                </span>
            </div>
        </div>
        <div class="bg-orange-50 px-6 py-3 flex items-center justify-between group-hover:bg-orange-100 transition-colors">
            <span class="text-sm font-medium text-orange-700">View Records</span>
            <i class="fas fa-arrow-right text-orange-600 group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    <!-- Lost Card -->
    <a href="{{ route('admin.adjustments.type', 'lost') }}" class="group block bg-white rounded-2xl shadow-md border border-gray-200 hover:shadow-xl hover:border-gray-400 transition-all duration-300 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                    <i class="fas fa-question-circle text-gray-600 text-xl"></i>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                    {{ $typeStats['lost']['count'] ?? 0 }} records
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Lost / Missing</h3>
            <p class="text-sm text-gray-500 mb-3">Inventory discrepancies & missing items</p>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-sm font-semibold text-gray-600">
                    {{ $typeStats['lost']['total_quantity'] ?? 0 }} items
                </span>
                <span class="text-xs text-gray-400">
                    @if($typeStats['lost']['last_adjustment'])
                        Last: {{ $typeStats['lost']['last_adjustment']->adjustment_date->format('M d') }}
                    @else
                        No records
                    @endif
                </span>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between group-hover:bg-gray-100 transition-colors">
            <span class="text-sm font-medium text-gray-700">View Records</span>
            <i class="fas fa-arrow-right text-gray-600 group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-800">Recent Adjustments</h3>
        <span class="text-sm text-gray-500">Last 5 records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reason</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentAdjustments as $adjustment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $adjustment->adjustment_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $adjustment->product->product_name }}</td>
                        <td class="px-6 py-3 text-center">
                            @php
                                $typeColors = [
                                    'return_in' => 'bg-blue-100 text-blue-700',
                                    'damage_out' => 'bg-red-100 text-red-700',
                                    'customer_damaged' => 'bg-orange-100 text-orange-700', // FIX: Added
                                    'lost' => 'bg-gray-100 text-gray-700',
                                ];
                                $typeLabels = [
                                    'return_in' => 'Return In',
                                    'damage_out' => 'Damaged',
                                    'customer_damaged' => 'Customer Damaged', // FIX: Added
                                    'lost' => 'Lost',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $typeColors[$adjustment->adjustment_type] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $typeLabels[$adjustment->adjustment_type] ?? $adjustment->adjustment_type }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-xs font-bold {{ $adjustment->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $adjustment->quantity > 0 ? '+' : '' }}{{ $adjustment->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $adjustment->reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <p>No recent adjustments</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection