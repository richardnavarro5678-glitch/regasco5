@extends('layouts.cashier')

@section('title', 'Sale Details - REGASCO SIS')
@section('page-title', 'Sale Details')

@section('cashier-content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-primary-400 px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-receipt mr-2"></i>
                Transaction Details
            </h3>
            <a href="{{ route('cashier.sales.history') }}" class="text-white text-sm hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Back to History
            </a>
        </div>
        
        <div class="p-8">
            <!-- Sale Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500 mb-1">Transaction ID</p>
                    <p class="text-lg font-bold text-gray-800">#{{ $sale->sale_id }}</p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500 mb-1">Date & Time</p>
                    <p class="text-lg font-bold text-gray-800">{{ $sale->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            <!-- Product Details -->
            <div class="border-t border-gray-100 pt-6 mb-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4">Product Information</h4>
                
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-box text-primary-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-800">{{ $sale->product->product_name }}</p>
                        <p class="text-sm text-gray-500">SKU: {{ $sale->product->sku }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500 mb-1">Quantity</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $sale->quantity }}</p>
                    </div>
                    
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500 mb-1">Unit Price</p>
                        <p class="text-2xl font-bold text-green-600">₱{{ number_format($sale->unit_price, 2) }}</p>
                    </div>
                    
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                        <p class="text-2xl font-bold text-purple-600">₱{{ number_format($sale->total_price, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="border-t border-gray-100 pt-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4">Payment Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                        <p class="text-lg font-bold text-gray-800 capitalize">{{ $sale->payment_method ?? 'Cash' }}</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500 mb-1">Cashier</p>
                        <p class="text-lg font-bold text-gray-800">{{ $sale->user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <div class="mt-8 flex justify-center">
                <button onclick="window.print()" class="btn-gradient text-white px-8 py-3 rounded-xl shadow-lg flex items-center space-x-2">
                    <i class="fas fa-print"></i>
                    <span>Print Receipt</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection