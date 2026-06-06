@extends('layouts.admin')

@section('title', $supplier->supplier_name . ' Returns - REGASCO SIS')
@section('page-title', $supplier->supplier_name . ' - Return History')

@section('admin-content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.supplier-returns.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Returns Dashboard
            </a>
            <p class="text-gray-500 text-sm">Return history for this supplier</p>
        </div>
        <a href="{{ route('admin.supplier-returns.create') }}" 
           class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
            <i class="fas fa-plus"></i>
            <span>New Return</span>
        </a>
    </div>
</div>

<!-- Status Summary -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Pending</p>
                <p class="text-xl font-bold text-yellow-600">{{ $statusCounts['pending'] }}</p>
            </div>
            <i class="fas fa-clock text-yellow-400 text-xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Shipped</p>
                <p class="text-xl font-bold text-blue-600">{{ $statusCounts['shipped'] }}</p>
            </div>
            <i class="fas fa-truck text-blue-400 text-xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Received</p>
                <p class="text-xl font-bold text-purple-600">{{ $statusCounts['received'] }}</p>
            </div>
            <i class="fas fa-check-circle text-purple-400 text-xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Completed</p>
                <p class="text-xl font-bold text-green-600">{{ $statusCounts['completed'] }}</p>
            </div>
            <i class="fas fa-check-double text-green-400 text-xl"></i>
        </div>
    </div>
</div>

<!-- Returns Table -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">All Returns</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Reason</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($returns as $return)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $return->return_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $return->product->product_name }}</td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">{{ $return->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $return->reason }}</td>
                        <td class="px-6 py-4 text-center">
                            @php $badge = $return->status_badge; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium {{ $badge[0] }}">
                                <i class="fas {{ $badge[1] }} mr-1"></i>
                                {{ ucfirst($return->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.supplier-returns.show', $return) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <p class="text-lg font-medium">No returns for this supplier</p>
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