@extends('layouts.admin')

@section('title', 'Supplier Returns - REGASCO SIS')
@section('page-title', 'Supplier Returns')

@section('admin-content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-500">Monitor and manage product returns to suppliers</p>
        </div>
        <a href="{{ route('admin.supplier-returns.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-plus"></i>
            <span>New Return</span>
        </a>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Completed -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Completed</h3>
                        <p class="text-sm text-gray-500">Successfully processed returns</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                    {{ $completedCount }} records
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-green-600 font-semibold">{{ $completedQty }} items</span>
                <a href="{{ route('admin.supplier-returns.status', 'completed') }}" class="text-green-600 hover:text-green-800 text-sm font-medium flex items-center">
                    View Records <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Rejected</h3>
                        <p class="text-sm text-gray-500">Returns rejected by supplier</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">
                    {{ $rejectedCount }} records
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-red-600 font-semibold">{{ $rejectedQty }} items</span>
                <a href="{{ route('admin.supplier-returns.status', 'rejected') }}" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
                    View Records <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Returns Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Recent Returns</h3>
            <span class="text-sm text-gray-500">Last 10 records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentReturns->take(10) as $return)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $return->return_date->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $return->supplier->supplier_name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $return->product->product_name }}</td>
                            <td class="px-6 py-3 text-center text-sm font-semibold">{{ $return->quantity }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($return->status === 'completed')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-lg">Completed</span>
                                @elseif($return->status === 'rejected')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-lg">Rejected</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg">{{ ucfirst($return->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('admin.supplier-returns.show', $return->return_id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                <p>No supplier returns yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Supplier Summary -->
    <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Supplier Summary</h3>
            <p class="text-sm text-gray-500">Returns per supplier</p>
        </div>
        <div class="p-6">
            @if($supplierSummary->count() > 0)
                @foreach($supplierSummary as $supplier)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-3">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-truck text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ $supplier->supplier_name }}</p>
                                <p class="text-xs text-gray-500">{{ $supplier->returns_count }} total returns</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-600">{{ $supplier->total_quantity }} items</span>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-truck text-gray-300 text-3xl mb-2"></i>
                    <p>No supplier returns yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection