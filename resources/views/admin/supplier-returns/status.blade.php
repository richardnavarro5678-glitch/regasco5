@extends('layouts.admin')

@section('title', $statusLabel . ' Returns - REGASCO SIS')
@section('page-title', $statusLabel . ' Returns')

@section('admin-content')
<div class="max-w-7xl mx-auto">
    <!-- Header with Back Button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-500">All {{ $statusLabel }} supplier returns</p>
        </div>
        <a href="{{ route('admin.supplier-returns.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Returns</span>
        </a>
    </div>

    <!-- Status Card Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if($status === 'completed')
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
                    {{ $returns->total() }} records
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-green-600 font-semibold">{{ $returns->sum('quantity') }} items</span>
                <span class="text-green-600 text-sm font-medium">Viewing All</span>
            </div>
        </div>
        @else
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
                    {{ $returns->total() }} records
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-red-600 font-semibold">{{ $returns->sum('quantity') }} items</span>
                <span class="text-red-600 text-sm font-medium">Viewing All</span>
            </div>
        </div>
        @endif
    </div>

    <!-- All Returns Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">All {{ $statusLabel }} Returns</h3>
            <span class="text-sm text-gray-500">{{ $returns->total() }} total records</span>
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
                    @forelse($returns as $return)
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
                                <p>No {{ $statusLabel }} returns found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection