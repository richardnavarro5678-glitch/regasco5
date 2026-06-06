@extends('layouts.cashier')

@section('title', 'Archived Sales - REGASCO SIS')
@section('page-title', 'Archived Sales')

@section('cashier-content')
<div class="mb-6">
    <a href="{{ route('cashier.sales.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
        <i class="fas fa-arrow-left mr-1"></i> Back to My Sales
    </a>
    <p class="text-gray-500 text-sm">View and restore your archived sales</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sale ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Deleted</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50 transition-colors bg-red-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">#{{ $sale->sale_id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-gray-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $sale->product->product_name ?? 'Deleted Product' }}</p>
                                    <p class="text-xs text-gray-400">{{ $sale->product->sku ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">{{ $sale->quantity }}</td>
                        <td class="px-6 py-4 text-center text-sm font-medium">₱{{ number_format($sale->total_price, 2) }}</td>
                        <td class="px-6 py-4 text-center text-xs text-gray-500">{{ $sale->deleted_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Restore Button -->
                                <button onclick="openRestoreModal({{ $sale->sale_id }}, '{{ $sale->product->product_name ?? 'Deleted Product' }}')" 
                                    class="p-2 text-green-500 hover:text-green-700 transition-colors" title="Restore Sale">
                                    <i class="fas fa-undo"></i>
                                </button>

                                <!-- Permanent Delete Button -->
                                <button onclick="openForceDeleteModal({{ $sale->sale_id }}, '{{ $sale->product->product_name ?? 'Deleted Product' }}')" 
                                    class="p-2 text-red-500 hover:text-red-700 transition-colors" title="Delete Permanently">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-archive text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-medium">No archived sales</p>
                            <p class="text-sm text-gray-400">Your archived sales will appear here</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($sales->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $sales->links() }}
        </div>
    @endif
</div>

<!-- ==================== CUSTOM RESTORE MODAL ==================== -->
<div id="restoreModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="restoreModalContent">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-undo text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Restore Sale</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-question-circle text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">Are you sure you want to restore this sale?</p>
                    <p class="text-sm text-gray-500">Product: <span id="restoreProductName" class="font-semibold text-gray-700"></span></p>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        The stock quantity will be deducted from inventory.
                    </p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
            <button onclick="closeRestoreModal()" 
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition-all font-medium text-sm">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            <form id="restoreForm" method="POST" class="inline">
                @csrf
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all font-medium text-sm shadow-lg">
                    <i class="fas fa-undo mr-2"></i>Restore Sale
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ==================== CUSTOM PERMANENT DELETE MODAL ==================== -->
<div id="forceDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="forceDeleteModalContent">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-trash text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Delete Permanently</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">Are you sure you want to permanently delete this sale?</p>
                    <p class="text-sm text-gray-500">Product: <span id="forceDeleteProductName" class="font-semibold text-gray-700"></span></p>
                    <p class="text-sm text-red-500 mt-2 font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        This action cannot be undone!
                    </p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
            <button onclick="closeForceDeleteModal()" 
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition-all font-medium text-sm">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            <form id="forceDeleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all font-medium text-sm shadow-lg">
                    <i class="fas fa-trash mr-2"></i>Delete Permanently
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // ==================== RESTORE MODAL FUNCTIONS ====================
    function openRestoreModal(saleId, productName) {
        document.getElementById('restoreProductName').textContent = productName;
        const form = document.getElementById('restoreForm');
        form.action = '{{ url("cashier/sales") }}/' + saleId + '/restore';
        const modal = document.getElementById('restoreModal');
        const content = document.getElementById('restoreModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeRestoreModal() {
        const modal = document.getElementById('restoreModal');
        const content = document.getElementById('restoreModalContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // ==================== PERMANENT DELETE MODAL FUNCTIONS ====================
    function openForceDeleteModal(saleId, productName) {
        document.getElementById('forceDeleteProductName').textContent = productName;
        const form = document.getElementById('forceDeleteForm');
        form.action = '{{ url("cashier/sales") }}/' + saleId + '/force-delete';
        const modal = document.getElementById('forceDeleteModal');
        const content = document.getElementById('forceDeleteModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeForceDeleteModal() {
        const modal = document.getElementById('forceDeleteModal');
        const content = document.getElementById('forceDeleteModalContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Close modals on outside click
    document.getElementById('restoreModal').addEventListener('click', function(e) {
        if (e.target === this) closeRestoreModal();
    });
    document.getElementById('forceDeleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeForceDeleteModal();
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('restoreModal').classList.contains('hidden')) {
                closeRestoreModal();
            }
            if (!document.getElementById('forceDeleteModal').classList.contains('hidden')) {
                closeForceDeleteModal();
            }
        }
    });
</script>
@endsection