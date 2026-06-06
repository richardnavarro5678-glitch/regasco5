@extends('layouts.admin')

@section('title', ucfirst($status) . ' Returns - REGASCO SIS')
@section('page-title', ucfirst($status) . ' Supplier Returns')

@section('admin-content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.supplier-returns.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Returns Dashboard
            </a>
            <p class="text-gray-500 text-sm">
                @if($status === 'completed')
                    Successfully processed returns
                @elseif($status === 'rejected')
                    Returns rejected by supplier
                @else
                    {{ ucfirst($status) }} returns
                @endif
            </p>
        </div>
        <a href="{{ route('admin.supplier-returns.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-all">
            <i class="fas fa-plus mr-2"></i> New Return
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
        <p class="text-gray-500 text-sm">Total Records</p>
        <h4 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_count']) }}</h4>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
        <p class="text-gray-500 text-sm">Total Items</p>
        <h4 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_quantity']) }}</h4>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
        <p class="text-gray-500 text-sm">This Month</p>
        <h4 class="text-2xl font-bold text-gray-800">{{ number_format($stats['this_month']) }}</h4>
    </div>
</div>

<!-- Returns Table -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">{{ ucfirst($status) }} Returns</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reason</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($returns as $return)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $return->return_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $return->supplier->supplier_name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $return->product->product_name }}</td>
                        <td class="px-6 py-3 text-center text-sm font-semibold">{{ $return->quantity }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $return->reason }}</td>
                        <td class="px-6 py-3 text-center">
                            <a href="{{ route('admin.supplier-returns.show', $return) }}" class="text-blue-600 hover:text-blue-800 text-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <p>No {{ $status }} returns found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($returns->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $returns->links() }}
        </div>
    @endif
</div>
@endsection