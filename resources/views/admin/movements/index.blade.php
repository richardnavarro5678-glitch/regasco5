@extends('layouts.admin')

@section('title', 'Activity Logs - REGASCO SIS')
@section('page-title', 'Activity Logs')

@section('admin-content')
<div class="mb-6">
    <p class="text-gray-500 text-sm">Complete audit trail of all system activities</p>
</div>

<!-- FIX: Single Date Search Bar (no end date) -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
    <form method="GET" action="{{ route('admin.movements.index') }}" class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[300px]">
            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent bg-white">
                <div class="pl-4 pr-2 text-gray-400">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <input type="date" name="search_date" 
                    class="flex-1 px-2 py-3 border-0 focus:ring-0 text-sm"
                    value="{{ request('search_date') }}"
                    placeholder="Search by date">
            </div>
        </div>
        <button type="submit" 
            class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors flex items-center shadow-md font-medium">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        <a href="{{ route('admin.movements.index') }}" 
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors flex items-center font-medium">
            <i class="fas fa-undo mr-2"></i>Reset
        </a>
    </form>
</div>

<!-- Movements Table -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Change</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Before</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">After</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $movement->created_at->format('M d, Y') }}
                            <span class="text-xs text-gray-400 block">{{ $movement->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-box text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $movement->product->product_name ?? 'Deleted Product' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $typeColors = [
                                    'sale' => 'bg-red-100 text-red-700',
                                    'delivery' => 'bg-green-100 text-green-700',
                                    'adjustment' => 'bg-orange-100 text-orange-700',
                                ];
                                $typeIcons = [
                                    'sale' => 'fa-shopping-cart',
                                    'delivery' => 'fa-truck',
                                    'adjustment' => 'fa-exchange-alt',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium {{ $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-700' }}">
                                <i class="fas {{ $typeIcons[$movement->movement_type] ?? 'fa-circle' }} mr-1"></i>
                                {{ ucfirst($movement->movement_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $movement->quantity > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $movement->stock_before }}</td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-800">{{ $movement->stock_after }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $movement->remarks ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-history text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-medium">No activities recorded</p>
                            <p class="text-sm text-gray-400">System activities will appear here</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($movements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection