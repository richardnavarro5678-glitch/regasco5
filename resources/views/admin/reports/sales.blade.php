@extends('layouts.admin')

@section('title', 'Sales Report - REGASCO SIS')
@section('page-title', 'Sales Report')

@section('admin-content')
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden print:shadow-none print:border-none" id="report-content">
    
    <!-- PRINT LETTERHEAD -->
    <div class="hidden print:block print-letterhead">
        <div class="text-center pb-6 mb-6 border-b-2 border-gray-800">
            <!-- Logo Placeholder -->
            <div class="mb-4">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-gas-pump text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-wide">REGASCO</h1>
                <p class="text-sm text-gray-600 mt-1">Retailer of Gas and Convenience Store Operations</p>
            </div>
            
            <div class="text-sm text-gray-600 mt-4">
                <p class="font-semibold">Resultay Family Complex, Magsaysay Avenue, Brgy. Poblacion, Basista, Pangasinan, Philippines</p>
                <p>Contact: (02) 8123-4567 | Email: info@regasco.com</p>
                <p class="italic mt-1">"Basta REGASCO sigurado"</p>
            </div>
        </div>
        
        <div class="text-center pb-4 mb-4 border-b-2 border-gray-800">
            <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-wider">Sales Report</h2>
            <p class="text-sm text-gray-600 mt-2">
                Period Covered: <strong>{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</strong>
            </p>
            <p class="text-sm text-gray-600">Report Generated: {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>

    <!-- Screen Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex justify-between items-center print:hidden">
        <h3 class="text-white font-bold flex items-center">
            <i class="fas fa-chart-line mr-2"></i>
            Sales Report: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
        </h3>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-all">
                <i class="fas fa-print mr-2"></i> Print / Save PDF
            </button>
            <a href="{{ route('admin.reports.index') }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>
    
    @php
        $totalSales = isset($summary) ? $summary['total_sales'] : $sales->sum('total_price');
        $totalItems = isset($summary) ? $summary['total_items'] : $sales->sum('quantity');
    @endphp
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50 print:hidden">
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <p class="text-gray-500 text-sm">Total Sales Revenue</p>
            <h4 class="text-2xl font-bold text-gray-800">₱{{ number_format($totalSales, 2) }}</h4>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <p class="text-gray-500 text-sm">Items Sold</p>
            <h4 class="text-2xl font-bold text-gray-800">{{ number_format($totalItems) }}</h4>
        </div>
    </div>

    <!-- SCREEN VIEW: Individual Records Table -->
    <div class="overflow-x-auto p-6 print:hidden">
        <h4 class="text-lg font-bold text-gray-800 mb-4">Detailed Transactions</h4>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cashier</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Unit Price</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->sale_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $sale->product->product_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->user->name }}</td>
                        <td class="px-4 py-3 text-center text-sm">{{ $sale->quantity }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">₱{{ number_format($sale->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">₱{{ number_format($sale->total_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No sales found for this period</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PRINT VIEW: Grouped by Date + Product Table -->
    <div class="hidden print:block overflow-x-auto p-6 print:p-0">
        <h4 class="text-lg font-bold text-gray-800 mb-4 print:text-base">Daily Summary (Per Product)</h4>
        <table class="w-full print:text-sm print-table">
            <thead class="bg-gray-50 print:bg-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Product</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Transactions</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Total Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Unit Price</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase print:text-gray-700 print:border print:border-gray-400">Daily Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 print:divide-gray-400">
                @php $currentDate = null; @endphp
                
                @forelse($groupedSales as $dailySale)
                    @if($currentDate != $dailySale->date->format('Y-m-d'))
                        @php $currentDate = $dailySale->date->format('Y-m-d'); @endphp
                        <tr style="background: #f3f4f6;">
                            <td colspan="6" class="px-4 py-2 text-sm font-bold text-gray-700 print:border print:border-gray-400">
                                {{ $dailySale->date->format('F d, Y (l)') }}
                            </td>
                        </tr>
                    @endif
                    
                    <tr class="hover:bg-gray-50 print:hover:bg-white">
                        <td class="px-4 py-3 text-sm text-gray-600 print:border print:border-gray-400"></td>
                        <td class="px-4 py-3 font-medium text-gray-800 print:border print:border-gray-400">{{ $dailySale->product_name }}</td>
                        <td class="px-4 py-3 text-center text-sm print:border print:border-gray-400">{{ $dailySale->transaction_count }}</td>
                        <td class="px-4 py-3 text-center text-sm print:border print:border-gray-400">{{ $dailySale->quantity }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600 print:border print:border-gray-400">₱{{ number_format($dailySale->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800 print:border print:border-gray-400">₱{{ number_format($dailySale->total_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 print:border print:border-gray-400">No sales found for this period</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 print:bg-gray-200 font-bold">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-gray-700 print:border print:border-gray-400">GRAND TOTAL:</td>
                    <td class="px-4 py-3 text-right text-gray-800 print:border print:border-gray-400">₱{{ number_format($totalSales, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- PRINT SIGNATORIES -->
    <div class="hidden print:block print-signatories">
        <div class="border-t-2 border-gray-800 pt-6 mt-6">
            <p class="text-sm text-gray-600 mb-6 text-center italic">This is to certify that the above information is true and correct based on the records of REGASCO.</p>
            
            <div class="grid grid-cols-3 gap-8 mt-12">
                <div class="text-center">
                    <div class="border-b border-gray-800 pb-2 mb-2">
                        <p class="font-bold text-gray-800">{{ Auth::user()->name }}</p>
                    </div>
                    <p class="text-sm text-gray-600">Prepared by</p>
                    <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->role === 'admin' ? 'System Administrator' : 'Cashier' }}</p>
                    <p class="text-xs text-gray-500">Date: _______________</p>
                </div>
                <div class="text-center">
                    <div class="border-b border-gray-800 pb-2 mb-2"><p class="font-bold text-gray-800">&nbsp;</p></div>
                    <p class="text-sm text-gray-600">Checked by</p>
                    <p class="text-xs text-gray-500 mt-1">Cashier</p>
                    <p class="text-xs text-gray-500">Date: _______________</p>
                </div>
                <div class="text-center">
                    <div class="border-b border-gray-800 pb-2 mb-2"><p class="font-bold text-gray-800">&nbsp;</p></div>
                    <p class="text-sm text-gray-600">Approved by</p>
                    <p class="text-xs text-gray-500 mt-1">General Manager / Owner</p>
                    <p class="text-xs text-gray-500">Date: _______________</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-8 pt-4 border-t border-gray-300 text-xs text-gray-500">
            <p>REGASCO Sales & Inventory System | Confidential Document</p>
            <p>Page 1 of 1</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: letter; margin: 0.5in; }
        body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        aside, nav, .print\:hidden, 
        .sidebar, .admin-header, .top-bar, 
        header, .navbar, .nav-header,
        [class*="sidebar"], [class*="header"]:not(.print-letterhead):not(.print-signatories),
        [id*="sidebar"], [id*="header"]:not(#report-content) {
            display: none !important;
        }
        main { margin-left: 0 !important; padding: 0 !important; }
        .print\:block { display: block !important; }
        .print-letterhead { margin-bottom: 15px; }
        .print-table { width: 100%; border-collapse: collapse; }
        .print-table th, .print-table td { padding: 8px; font-size: 11px; }
        .print-table thead { background-color: #e5e7eb !important; }
        .print-signatories { page-break-inside: avoid; margin-top: 30px !important; }
        .print\:shadow-none { box-shadow: none !important; }
        .print\:bg-white { background-color: white !important; }
        .print\:bg-gray-200 { background-color: #e5e7eb !important; }
        .print\:border { border-width: 1px !important; }
        .print\:border-gray-400 { border-color: #9ca3af !important; }
        .print\:border-gray-800 { border-color: #1f2937 !important; }
    }
</style>
@endsection