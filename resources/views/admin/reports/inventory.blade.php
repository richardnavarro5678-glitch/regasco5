@extends('layouts.admin')

@section('title', 'Inventory Report - REGASCO SIS')
@section('page-title', 'Inventory Report')

@section('admin-content')
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden print:shadow-none print:border-none" id="report-content">
    
    <!-- PRINT LETTERHEAD -->
    <div class="hidden print:block print-letterhead">
        <div class="text-center pb-6 mb-6 border-b-2 border-gray-800">
            <!-- Logo Placeholder -->
            <div class="mb-4">
                <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-gas-pump text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-wide">REGASCO</h1>
                <p class="text-sm text-gray-600 mt-1">Retailer of Gas and Convenience Store Operations</p>
            </div>
            
            <div class="text-sm text-gray-600 mt-4">
                <p class="font-semibold">Resultay Family Complex, Magsaysay Avenue, Brgy. Poblacion, Basista, Pangasinan, Philippines</p>
                <p>Contact: (02) 8123-4567 | Email: info@regasco.com</p>
                <p class="italic mt-1">"Fuel Your Journey, Power Your Life"</p>
            </div>
        </div>
        
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wider border-b border-gray-400 inline-block pb-1">Inventory Valuation Report</h2>
            <p class="text-sm text-gray-600 mt-2">
                As of: <strong>{{ now()->format('F d, Y') }}</strong>
            </p>
            <p class="text-sm text-gray-600">Report Generated: {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>

    <!-- Screen Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 flex justify-between items-center print:hidden">
        <h3 class="text-white font-bold flex items-center">
            <i class="fas fa-warehouse mr-2"></i>
            Inventory Valuation Report
        </h3>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-green-50 transition-all">
                <i class="fas fa-print mr-2"></i> Print / Save PDF
            </button>
            <a href="{{ route('admin.reports.index') }}" class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 p-6 bg-gray-50 print:bg-white print-summary">
        <div class="bg-white rounded-xl p-4 shadow-sm print:shadow-none print:border print:border-gray-300">
            <p class="text-gray-500 text-sm print:text-gray-600">Total Products</p>
            <h4 class="text-2xl font-bold text-gray-800 print:text-xl">{{ number_format($summary['total_products']) }}</h4>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm print:shadow-none print:border print:border-gray-300">
            <p class="text-gray-500 text-sm print:text-gray-600">Total Stock Units</p>
            <h4 class="text-2xl font-bold text-gray-800 print:text-xl">{{ number_format($summary['total_stock']) }}</h4>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm print:shadow-none print:border print:border-gray-300">
            <p class="text-gray-500 text-sm print:text-gray-600">Inventory Value</p>
            <h4 class="text-2xl font-bold text-green-600 print:text-xl">₱{{ number_format($summary['total_value'], 2) }}</h4>
        </div>
        <!-- DAGDAG - Total Selling Price -->
        <div class="bg-white rounded-xl p-4 shadow-sm print:shadow-none print:border print:border-gray-300">
            <p class="text-gray-500 text-sm print:text-gray-600">Total Selling Price</p>
            <h4 class="text-2xl font-bold text-blue-600 print:text-xl">₱{{ number_format($summary['total_selling_price'], 2) }}</h4>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm print:shadow-none print:border print:border-gray-300">
            <p class="text-gray-500 text-sm print:text-gray-600">Low Stock Items</p>
            <h4 class="text-2xl font-bold {{ $summary['low_stock_count'] > 0 ? 'text-red-600' : 'text-gray-800' }} print:text-xl">{{ number_format($summary['low_stock_count']) }}</h4>
        </div>
    </div>

    <!-- Products Table -->
    <div class="overflow-x-auto p-6 print:p-0">
        <table class="w-full print:text-sm print-table">
            <thead class="bg-gray-50 print:bg-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">SKU</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Stock</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Cost Price</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Selling Price</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Stock Value</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 print:divide-gray-400">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 print:hover:bg-white {{ $product->isLowStock() ? 'bg-red-50 print:bg-red-50' : '' }}">
                        <td class="px-4 py-3 print:border print:border-gray-400">
                            <div class="font-medium text-gray-800">{{ $product->product_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 print:border print:border-gray-400">{{ $product->sku }}</td>
                        <td class="px-4 py-3 text-center font-semibold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-800' }} print:border print:border-gray-400">{{ $product->stock_quantity }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600 print:border print:border-gray-400">₱{{ number_format($product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600 print:border print:border-gray-400">₱{{ number_format($product->selling_price ?? $product->price ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800 print:border print:border-gray-400">₱{{ number_format($product->stock_quantity * $product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-center print:border print:border-gray-400">
                            @if($product->isLowStock())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 print:bg-transparent print:text-red-600 print:font-bold">LOW STOCK</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 print:bg-transparent print:text-green-600">NORMAL</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 print:border print:border-gray-400">No products found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 print:bg-gray-200 font-bold">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-gray-700 print:border print:border-gray-400">TOTAL INVENTORY VALUE:</td>
                    <td class="px-4 py-3 text-right text-gray-800 print:border print:border-gray-400">₱{{ number_format($summary['total_value'], 2) }}</td>
                    <td class="print:border print:border-gray-400"></td>
                </tr>
                <!-- DAGDAG - Total Selling Price -->
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-gray-700 print:border print:border-gray-400">TOTAL SELLING PRICE:</td>
                    <td class="px-4 py-3 text-right text-blue-600 print:border print:border-gray-400">₱{{ number_format($summary['total_selling_price'], 2) }}</td>
                    <td class="print:border print:border-gray-400"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- PRINT SIGNATORIES -->
    <div class="hidden print:block print-signatories mt-12">
        <div class="border-t-2 border-gray-800 pt-8">
            <p class="text-sm text-gray-600 mb-8 text-center italic">This is to certify that the above inventory records are true and correct as of the date specified.</p>
            
            <div class="grid grid-cols-3 gap-12 mt-16">
                <!-- Prepared By -->
                <div class="text-center">
                    <div class="border-b border-gray-800 pb-2 mb-2">
                        <p class="font-bold text-gray-800">{{ Auth::user()->name }}</p>
                    </div>
                    <p class="text-sm text-gray-600">Prepared by</p>
                    <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->role === 'admin' ? 'System Administrator' : 'Inventory Staff' }}</p>
                    <p class="text-xs text-gray-500">Date: _______________</p>
                </div>
                
                <!-- Verified By -->
                <div class="text-center">
                    <div class="border-b border-gray-800 pb-2 mb-2">
                        <p class="font-bold text-gray-800">&nbsp;</p>
                    </div>
                    <p class="text-sm text-gray-600">Verified by</p>
                    <p class="text-xs text-gray-500 mt-1">Warehouse Supervisor</p>
                    <p class="text-xs text-gray-500">Date: _______________</p>
                </div>
                
                <!-- Approved By -->
                <div class="text-center">
                    <div class="border-b border-gray-800 pb-2 mb-2">
                        <p class="font-bold text-gray-800">&nbsp;</p>
                    </div>
                    <p class="text-sm text-gray-600">Approved by</p>
                    <p class="text-xs text-gray-500 mt-1">General Manager / Owner</p>
                    <p class="text-xs text-gray-500">Date: _______________</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12 pt-4 border-t border-gray-300 text-xs text-gray-500">
            <p>REGASCO Sales & Inventory System | Confidential Document</p>
            <p>Page 1 of 1</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: letter;
            margin: 0.5in;
        }
        
        body { 
            background: white !important; 
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        aside, nav, .print\\:hidden { 
            display: none !important; 
        }
        
        main { 
            margin-left: 0 !important; 
            padding: 0 !important;
        }
        
        .print\\:block { 
            display: block !important; 
        }
        
        .print-letterhead {
            margin-bottom: 20px;
        }
        
        .print-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .print-table th,
        .print-table td {
            padding: 8px;
            font-size: 11px;
        }
        
        .print-table thead {
            background-color: #e5e7eb !important;
        }
        
        .print-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .print-summary > div {
            border: 1px solid #d1d5db !important;
            padding: 12px !important;
            margin-bottom: 8px;
        }
        
        .print-signatories {
            page-break-inside: avoid;
        }
        
        .print\\:shadow-none { 
            box-shadow: none !important; 
        }
        
        .print\\:bg-white { 
            background-color: white !important; 
        }
        
        .print\\:bg-gray-200 { 
            background-color: #e5e7eb !important; 
        }
        
        .print\\:bg-red-50 { 
            background-color: #fef2f2 !important; 
        }
        
        .print\\:border { 
            border-width: 1px !important; 
        }
        
        .print\\:border-gray-300 { 
            border-color: #d1d5db !important; 
        }
        
        .print\\:border-gray-400 { 
            border-color: #9ca3af !important; 
        }
        
        .print\\:border-gray-800 { 
            border-color: #1f2937 !important; 
        }
        
        .print\\:text-sm { 
            font-size: 0.875rem !important; 
        }
        
        .print\\:text-xl { 
            font-size: 1.25rem !important; 
        }
        
        .print\\:text-gray-600 { 
            color: #4b5563 !important; 
        }
        
        .print\\:text-gray-700 { 
            color: #374151 !important; 
        }
        
        .print\\:text-red-600 { 
            color: #dc2626 !important; 
        }
        
        .print\\:text-green-600 { 
            color: #059669 !important; 
        }
        
        .print\\:hover\\:bg-white:hover { 
            background-color: white !important; 
        }
        
        .print\\:bg-transparent { 
            background-color: transparent !important; 
        }
        
        .print\\:font-bold { 
            font-weight: 700 !important; 
        }
    }
</style>
@endsection