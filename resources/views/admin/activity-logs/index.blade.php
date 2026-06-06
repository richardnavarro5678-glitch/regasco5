@extends('layouts.admin')

@section('title', 'Activity Logs - REGASCO SIS')
@section('page-title', 'Activity Logs')

@section('admin-content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-white font-bold flex items-center text-lg">
                <i class="fas fa-history mr-2"></i>
                Activity Logs
            </h3>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-1 text-teal-500"></i>
                    Search by Date
                </label>
                <input type="date" name="search_date" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    value="{{ request('search_date') }}">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-xl font-medium transition-all flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
                
                @if(request('search_date'))
                    <a href="{{ route('admin.activity-logs.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>

        @if(request('search_date'))
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-filter mr-1 text-teal-500"></i>
                    Showing results for: <span class="font-semibold text-teal-600">{{ \Carbon\Carbon::parse(request('search_date'))->format('F d, Y') }}</span>
                </p>
            </div>
        @endif
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Activity Records</h3>
            <span class="text-sm text-gray-500">{{ $movements->total() }} total records</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock Before</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock After</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $movement->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-gray-400 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $movement->product->product_name ?? 'Deleted Product' }}</p>
                                        <p class="text-xs text-gray-400">{{ $movement->product->sku ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $typeColors = [
                                        'sale' => 'bg-red-100 text-red-700',
                                        'delivery' => 'bg-green-100 text-green-700',
                                        'adjustment_in' => 'bg-blue-100 text-blue-700',
                                        'adjustment_out' => 'bg-orange-100 text-orange-700',
                                        'correction' => 'bg-purple-100 text-purple-700',
                                    ];
                                    $typeClass = $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium {{ $typeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $movement->stock_before }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-800">{{ $movement->stock_after }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="text-xs">{{ ucfirst($movement->reference_type) }} #{{ $movement->reference_id }}</span>
                                @if($movement->remarks)
                                    <p class="text-xs text-gray-400 mt-1">{{ $movement->remarks }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-gray-500 text-xs"></i>
                                    </div>
                                    {{ $movement->user->name ?? 'System' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-history text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium">No activity logs found</p>
                                <p class="text-sm text-gray-400">
                                    @if(request('search_date'))
                                        No records for {{ \Carbon\Carbon::parse(request('search_date'))->format('F d, Y') }}
                                    @else
                                        All stock activities will appear here
                                    @endif
                                </p>
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
</div>
@endsection