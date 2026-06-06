@extends('layouts.admin')

@section('title', $typeConfig['label'] . ' Adjustments - REGASCO SIS')
@section('page-title', $typeConfig['label'] . ' Adjustments')

@section('admin-content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.adjustments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Adjustment Types
            </a>
            <p class="text-gray-500 text-sm">{{ $typeConfig['description'] }}</p>
        </div>
        <a href="{{ route('admin.adjustments.create', ['type' => $type]) }}" 
           class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
            <i class="fas fa-plus"></i>
            <span>New {{ $typeConfig['label'] }} Adjustment</span>
        </a>
    </div>
</div>

@if($type === 'return_in')
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
    <div class="flex items-center">
        <i class="fas fa-info-circle text-blue-600 mr-3 text-lg"></i>
        <div>
            <p class="text-sm font-medium text-blue-800">Return In Pool System</p>
            <p class="text-xs text-blue-600">This is a running total of all customer returns per product. When you return to supplier, it deducts from this pool.</p>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Records</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_count'] }}</p>
            </div>
            <div class="w-12 h-12 {{ $typeConfig['bg_color'] }} rounded-xl flex items-center justify-center">
                <i class="fas {{ $typeConfig['icon'] }} {{ $typeConfig['icon_color'] }} text-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Quantity</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_quantity'] }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-boxes text-orange-600 text-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">This Month</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['this_month'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar text-blue-600 text-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reason</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $adjustment->adjustment_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 {{ $typeConfig['bg_color'] }} rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-box {{ $typeConfig['icon_color'] }} text-xs"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $adjustment->product->product_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $adjustment->quantity > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $adjustment->quantity > 0 ? '+' : '' }}{{ $adjustment->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $adjustment->reason }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $adjustment->notes ?? '-' }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $adjustment->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas {{ $typeConfig['icon'] }} text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-medium">No {{ $typeConfig['label'] }} adjustments</p>
                            <p class="text-sm text-gray-400 mb-4">Record your first {{ strtolower($typeConfig['label']) }} adjustment</p>
                            <a href="{{ route('admin.adjustments.create', ['type' => $type]) }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                <i class="fas fa-plus mr-2"></i> Add New
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($adjustments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $adjustments->links() }}
        </div>
    @endif
</div>
@endsection