@extends('layouts.admin')

@section('title', ($typeLabel ?? $typeName ?? 'Type') . ' Adjustments - REGASCO SIS')
@section('page-title', ($typeLabel ?? $typeName ?? 'Type') . ' Adjustments')

@section('admin-content')
<div class="mb-6">
    <a href="{{ route('admin.adjustments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
        <i class="fas fa-arrow-left mr-1"></i> Back to Adjustments
    </a>
    <p class="text-gray-500 text-sm">Viewing {{ strtolower($typeLabel ?? $typeName ?? 'type') }} adjustment records</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-white font-bold flex items-center text-lg">
                <i class="fas fa-sliders-h mr-2"></i>
                {{ $typeLabel ?? $typeName ?? 'Type' }} Adjustments
            </h3>
            
            <a href="{{ route('admin.adjustments.create') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-all flex items-center space-x-2">
                <i class="fas fa-plus mr-1"></i>
                <span>New Adjustment</span>
            </a>
        </div>
    </div>

    <!-- Adjustments Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($adjustments as $adjustment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $adjustment->adjustment_date ? \Carbon\Carbon::parse($adjustment->adjustment_date)->format('M d, Y') : $adjustment->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $adjustment->product->product_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium">
                                {{ $adjustment->quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $adjustment->reason ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ url('/admin/adjustments/' . $adjustment->id) }}" class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <a href="{{ url('/admin/adjustments/' . $adjustment->id . '/edit') }}" class="px-3 py-1 rounded-lg text-xs font-medium bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ url('/admin/adjustments/' . $adjustment->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this adjustment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium">No {{ strtolower($typeLabel ?? $typeName ?? 'type') }} records</p>
                                <p class="text-sm text-gray-400">No adjustments found for this type</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($adjustments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection