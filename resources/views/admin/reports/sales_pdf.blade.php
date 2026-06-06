<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        .header h1 {
            color: #1e3a8a;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            background: #f0f9ff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .summary-grid {
            display: flex;
            justify-content: space-between;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item h3 {
            color: #2563eb;
            font-size: 18px;
            margin: 0;
        }
        .summary-item p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .date-header {
            background: #e5e7eb;
            font-weight: bold;
            color: #374151;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REGASCO Sales & Inventory System</h1>
        <p><strong>Sales Report</strong></p>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <h3>₱{{ number_format($summary['total_sales'], 2) }}</h3>
                <p>Total Sales</p>
            </div>
            <div class="summary-item">
                <h3>{{ $summary['total_transactions'] }}</h3>
                <p>Transactions</p>
            </div>
            <div class="summary-item">
                <h3>{{ $summary['total_items'] }}</h3>
                <p>Items Sold</p>
            </div>
        </div>
    </div>

    <!-- FIX: Grouped by Date + Product Table -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th class="text-center">Transactions</th>
                <th class="text-center">Total Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Daily Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentDate = null;
            @endphp
            
            @forelse($groupedSales as $dailySale)
                {{-- FIX: Show date separator when date changes --}}
                @if($currentDate != $dailySale->date->format('Y-m-d'))
                    @php $currentDate = $dailySale->date->format('Y-m-d'); @endphp
                    <tr class="date-header">
                        <td colspan="6" style="padding: 8px 10px; font-weight: bold; color: #374151;">
                            {{ $dailySale->date->format('F d, Y (l)') }}
                        </td>
                    </tr>
                @endif
                
                <tr>
                    <td></td> {{-- Empty date cell since date is in header --}}
                    <td>{{ $dailySale->product_name }}</td>
                    <td class="text-center">{{ $dailySale->transaction_count }}</td>
                    <td class="text-center">{{ $dailySale->quantity }}</td>
                    <td class="text-right">₱{{ number_format($dailySale->unit_price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($dailySale->total_price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No sales found for this period</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #e5e7eb; font-weight: bold;">
                <td colspan="5" class="text-right">GRAND TOTAL:</td>
                <td class="text-right">₱{{ number_format($summary['total_sales'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>REGASCO Sales & Inventory System | Confidential Report</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>