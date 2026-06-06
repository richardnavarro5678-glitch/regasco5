<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report - {{ now()->format('Y-m-d') }}</title>
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
        tr.low-stock {
            background: #fef2f2;
        }
        .status-ok {
            color: #059669;
            font-weight: bold;
        }
        .status-low {
            color: #dc2626;
            font-weight: bold;
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
        <p><strong>Inventory Valuation Report</strong></p>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <h3>{{ $summary['total_products'] }}</h3>
                <p>Total Products</p>
            </div>
            <div class="summary-item">
                <h3>{{ $summary['total_stock'] }}</h3>
                <p>Total Stock Units</p>
            </div>
            <div class="summary-item">
                <h3>₱{{ number_format($summary['total_value'], 2) }}</h3>
                <p>Inventory Value</p>
            </div>
            <div class="summary-item">
                <h3 style="color: {{ $summary['low_stock_count'] > 0 ? '#dc2626' : '#059669' }}">{{ $summary['low_stock_count'] }}</h3>
                <p>Low Stock Items</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th class="text-center">Stock</th>
                <th class="text-right">Cost Price</th>
                <th class="text-right">Selling Price</th>
                <th class="text-right">Stock Value</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr class="{{ $product->isLowStock() ? 'low-stock' : '' }}">
                    <td>
                        <strong>{{ $product->product_name }}</strong>
                    </td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $product->stock_quantity }}</td>
                    <td class="text-right">₱{{ number_format($product->cost_price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($product->price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($product->stock_quantity * $product->cost_price, 2) }}</td>
                    <td class="text-center">
                        @if($product->isLowStock())
                            <span class="status-low">LOW</span>
                        @else
                            <span class="status-ok">OK</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>REGASCO Sales & Inventory System | Confidential Report</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>