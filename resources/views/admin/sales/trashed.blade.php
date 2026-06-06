@extends('layouts.admin')

@section('title', 'Archived Sales - REGASCO SIS')
@section('page-title', 'Archived Sales')

@section('admin-content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Archived Sales</h2>
            <p class="text-gray-500">View and manage deleted sales transactions</p>
        </div>
        <a href="{{ route('admin.sales.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Sales</span>
        </a>
    </div>

    <!-- FIX: Scrollable table with max-height, same as index -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
            <table class="w-full" id="trashedTable">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <!-- FIX: Added # column -->
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cashier</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Deleted At</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="trashedTableBody">
                    @forelse($sales as $index => $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- FIX: Display row number -->
                            <td class="px-4 py-4 text-center text-sm font-semibold text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $sale->sale_date->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-[#3b82f6]"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $sale->product_name ?? $sale->product->product_name ?? 'Deleted Product' }}</p>
                                        <p class="text-xs text-gray-500">{{ $sale->product->sku ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-gray-500 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $sale->user->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-semibold">{{ $sale->quantity }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">₱{{ number_format($sale->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-800">₱{{ number_format($sale->total_price, 2) }}</td>
                            <td class="px-6 py-4 text-center text-xs text-gray-400">{{ $sale->deleted_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openRestoreModal({{ $sale->sale_id }}, '{{ $sale->product_name ?? $sale->product->product_name ?? 'Deleted Product' }}')" 
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-undo mr-1"></i> Restore
                                    </button>
                                    <button onclick="openForceDeleteModal({{ $sale->sale_id }}, '{{ $sale->product_name ?? $sale->product->product_name ?? 'Deleted Product' }}')" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                <p>No archived sales found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== RESTORE CONFIRMATION MODAL ==================== -->
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
                    <p class="text-sm text-orange-500 mt-2 font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        This will deduct stock from inventory.
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
                @method('PATCH')
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all font-medium text-sm shadow-lg">
                    <i class="fas fa-undo mr-2"></i>Restore Sale
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ==================== PERMANENT DELETE CONFIRMATION MODAL ==================== -->
<div id="forceDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="forceDeleteModalContent">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Permanently Delete Sale</h3>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-skull-crossbones text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">This action cannot be undone!</p>
                    <p class="text-sm text-gray-500">Product: <span id="forceDeleteProductName" class="font-semibold text-gray-700"></span></p>
                    <p class="text-sm text-red-600 mt-2 font-medium">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        This will permanently remove the sale record forever.
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
                    class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all font-medium text-sm shadow-lg">
                    <i class="fas fa-trash-alt mr-2"></i>Permanently Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // ==================== RESTORE MODAL ====================
    function openRestoreModal(saleId, productName) {
        document.getElementById('restoreProductName').textContent = productName;
        
        const form = document.getElementById('restoreForm');
        form.action = '{{ route("admin.sales.restore", ":id") }}'.replace(':id', saleId);
        
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

    // ==================== FORCE DELETE MODAL ====================
    function openForceDeleteModal(saleId, productName) {
        document.getElementById('forceDeleteProductName').textContent = productName;
        
        const form = document.getElementById('forceDeleteForm');
        form.action = '{{ route("admin.sales.force-delete", ":id") }}'.replace(':id', saleId);
        
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

    // Close on outside click
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