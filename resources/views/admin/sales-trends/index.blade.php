@extends('layouts.admin')

@section('title', 'Sales Trends - REGASCO SIS')
@section('page-title', 'Sales Trends')

@section('admin-content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Sales Trends</h2>
        <p class="text-gray-500">View overall sales performance over time</p>
    </div>

    <!-- Period Toggle Buttons -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.sales-trends.index', ['period' => 'daily']) }}" 
               class="px-6 py-2.5 rounded-xl font-medium transition-all {{ $period === 'daily' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-calendar-day mr-2"></i>Daily
            </a>
            <a href="{{ route('admin.sales-trends.index', ['period' => 'weekly']) }}" 
               class="px-6 py-2.5 rounded-xl font-medium transition-all {{ $period === 'weekly' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-calendar-week mr-2"></i>Weekly
            </a>
            <a href="{{ route('admin.sales-trends.index', ['period' => 'monthly']) }}" 
               class="px-6 py-2.5 rounded-xl font-medium transition-all {{ $period === 'monthly' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-calendar-alt mr-2"></i>Monthly
            </a>
            <a href="{{ route('admin.sales-trends.index', ['period' => 'yearly']) }}" 
               class="px-6 py-2.5 rounded-xl font-medium transition-all {{ $period === 'yearly' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                <i class="fas fa-calendar mr-2"></i>Yearly
            </a>
        </div>
    </div>

    <!-- Chart Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold text-lg">{{ $chartData['title'] }}</h3>
            <p class="text-blue-100 text-sm">{{ $chartData['subtitle'] }}</p>
        </div>
        <div class="p-6">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-400 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Sales</p>
                    <h3 class="text-2xl font-bold">₱{{ number_format(array_sum($chartData['revenue']), 2) }}</h3>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-peso-sign text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-400 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Transactions</p>
                    <h3 class="text-2xl font-bold">{{ array_sum($chartData['transactions']) }}</h3>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Sales (₱)',
                    data: @json($chartData['revenue']),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '₱' + context.parsed.y.toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sales (₱)',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-PH');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection