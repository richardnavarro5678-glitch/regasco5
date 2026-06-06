@extends('layouts.cashier')

@section('title', 'Cashier Dashboard - REGASCO SIS')
@section('page-title', 'Dashboard')

@section('cashier-content')
<!-- Simple Welcome Text -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Welcome back, {{ Auth::user()->name }}!</h2>
    <p class="text-gray-500">Here's your sales summary for today</p>
</div>

<!-- 3 Cards Only - Exact Style -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Today's Sales - Blue -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-400 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-blue-100">Today's Sales</p>
                <h3 class="text-3xl font-bold">₱{{ number_format($todayRevenue, 2) }}</h3>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-peso-sign text-white text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-blue-100">{{ $todaySales }} transactions</p>
    </div>

    <!-- Items Sold - Green -->
    <div class="bg-gradient-to-r from-green-500 to-green-400 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-green-100">Items Sold</p>
                <h3 class="text-3xl font-bold">{{ $todayItems }}</h3>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-cart text-white text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-green-100">Total units today</p>
    </div>

    <!-- Quick Action - New Sale - Purple (CLICKABLE) -->
    <a href="{{ route('cashier.sales.create') }}" class="bg-gradient-to-r from-purple-500 to-purple-400 rounded-2xl shadow-lg p-6 text-white hover:shadow-xl transition-all cursor-pointer block">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-purple-100">Quick Action</p>
                <h3 class="text-2xl font-bold">New Sale</h3>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-plus text-white text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-purple-100 flex items-center">
            Process Sale <i class="fas fa-arrow-right ml-1"></i>
        </p>
    </a>
</div>

<!-- FIX: Removed Product Filter, Amount Search, and Active Filter -->
<!-- Recent Sales - Simple Table Only -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-blue-400 px-6 py-4 flex justify-between items-center">
        <h3 class="text-white font-bold flex items-center">
            <i class="fas fa-receipt mr-2"></i>
            Recent Sales
        </h3>
        <a href="{{ route('cashier.sales.index') }}" class="text-white text-sm hover:underline">View All</a>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full" id="salesTable">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3">Product</th>
                        <th class="pb-3">Qty</th>
                        <th class="pb-3 text-right">Amount</th>
                        <th class="pb-3 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="salesTableBody">
                    @forelse($recentSales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $sale->product ? $sale->product->product_name : 'Deleted Product' }}</p>
                                        <p class="text-xs text-gray-400">{{ $sale->product ? $sale->product->sku : 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $sale->quantity }}
                                </span>
                            </td>
                            <td class="py-4 text-right font-semibold text-gray-800">₱{{ number_format($sale->total_price, 2) }}</td>
                            <td class="py-4 text-right text-xs text-gray-400">{{ $sale->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr id="noSalesRow">
                            <td colspan="4" class="py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                <p>No recent sales</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection