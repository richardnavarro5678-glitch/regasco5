@extends('layouts.admin')

@section('title', 'Sale Details - REGASCO SIS')
@section('page-title', 'Sale Details')

@section('admin-content')
<div class="mb-6">
    <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Main Sale Info -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Sale Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-bold text-lg">Sale #{{ $sale->sale_id }}</h3>
                        <p class="text-blue-100 text-sm">{{ $sale->sale_date->format('F d, Y') }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <p class="text-white font-bold text-xl">₱{{ number_format($sale->total_price, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Product</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $sale->product->product_name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $sale->product->sku ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Quantity</p>
                        <p class="text-lg font-bold text-gray-800">{{ $sale->quantity }} units</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Unit Price</p>
                        <p class="text-lg font-bold text-gray-800">₱{{ number_format($sale->unit_price, 2) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total Price</p>
                        <p class="text-lg font-bold text-green-600">₱{{ number_format($sale->total_price, 2) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Cashier</p>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-gas-pump text-white text-xs"></i>
                            </div>
                            <p class="font-medium text-gray-800">{{ $sale->user->name }}</p>
                        </div>
                    </div>
                    
                    <!-- FIX: Removed Payment Method section -->
                </div>
            </div>
        </div>

        <!-- FIX: Removed Product Details section -->
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        
        <!-- FIX: Removed Sale Summary section -->

        <!-- Timeline -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Activity</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-shopping-cart text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Sale Completed</p>
                            <p class="text-xs text-gray-500">{{ $sale->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6">
                <a href="{{ route('admin.sales.index') }}" class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    View All Sales
                </a>
            </div>
        </div>
    </div>
</div>
@endsection