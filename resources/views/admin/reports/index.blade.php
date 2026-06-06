@extends('layouts.admin')

@section('title', 'Reports - REGASCO SIS')
@section('page-title', 'Reports & Analytics')

@section('admin-content')
<!-- Sales Report - Full Width -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover-lift mb-8">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
        <h3 class="text-white font-bold flex items-center">
            <i class="fas fa-chart-line mr-2"></i>
            Sales Report
        </h3>
    </div>
    <div class="p-6">
        <p class="text-gray-500 mb-6">Generate sales reports with date range filtering. Includes printable letterhead format with signatories.</p>
        
        <form action="{{ route('admin.reports.sales') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" required 
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ date('Y-m-01') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" required 
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ date('Y-m-d') }}">
                </div>
            </div>
            
            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-blue-50 text-blue-600 py-3 rounded-lg font-medium hover:bg-blue-100 transition-all">
                    <i class="fas fa-eye mr-2"></i> View Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Today's Sales</p>
                <h4 class="text-2xl font-bold">₱{{ number_format(\App\Models\Sale::whereDate('sale_date', today())->sum('total_price'), 2) }}</h4>
            </div>
            <i class="fas fa-peso-sign text-3xl text-white/30"></i>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Total Products</p>
                <h4 class="text-2xl font-bold">{{ \App\Models\Product::where('is_active', true)->count() }}</h4>
            </div>
            <i class="fas fa-box text-3xl text-white/30"></i>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Low Stock Items</p>
                <h4 class="text-2xl font-bold">{{ \App\Models\Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('is_active', true)->count() }}</h4>
            </div>
            <i class="fas fa-exclamation-triangle text-3xl text-white/30"></i>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Inventory Value</p>
                <h4 class="text-2xl font-bold">₱{{ number_format(\App\Models\Product::sum(\DB::raw('stock_quantity * cost_price')), 0) }}</h4>
            </div>
            <i class="fas fa-chart-pie text-3xl text-white/30"></i>
        </div>
    </div>
</div>
@endsection