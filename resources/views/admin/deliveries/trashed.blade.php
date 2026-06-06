@extends('layouts.admin')

@section('title', 'Archived Deliveries - REGASCO SIS')
@section('page-title', 'Archived Deliveries')

@section('admin-content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm">View and manage archived deliveries</p>
        </div>
        <a href="{{ route('admin.deliveries.index') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
            <i class="fas fa-arrow-left text-sm"></i>
            <span class="font-medium">Back to Deliveries</span>
        </a>
    </div>
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
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Recorded By</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deleted At</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($deliveries as $delivery)
                    <tr class="hover:bg-gray-50 transition-colors bg-red-50/30">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $delivery->delivery_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-truck text-blue-600 text-xs"></i>
                                </div>
                                {{ $delivery->supplier->supplier_name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-box text-green-600 text-xs"></i>
                                </div>
                                {{ $delivery->product->product_name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                +{{ $delivery->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $delivery->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-sm text-red-500">{{ $delivery->deleted_at->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openRestoreModal({{ $delivery->delivery_id }}, '{{ $delivery->product->product_name ?? 'Unknown' }}', {{ $delivery->quantity }})" 
                                    class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-xs font-medium transition-colors">
                                    <i class="fas fa-undo mr-1"></i>Restore
                                </button>
                                <button type="button" onclick="openForceDeleteModal({{ $delivery->delivery_id }}, '{{ $delivery->product->product_name ?? 'Unknown' }}', {{ $delivery->quantity }})" 
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-medium transition-colors">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-archive text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-medium">No archived deliveries</p>
                            <p class="text-sm text-gray-400">Deleted deliveries will appear here</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($deliveries->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $deliveries->links() }}
        </div>
    @endif
</div>

<!-- Custom Restore Confirmation Modal -->
<div id="restoreModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="restoreModalContent">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-undo text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Confirm Restore</h3>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box-open text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">Are you sure you want to restore this delivery?</p>
                    <p class="text-sm text-gray-500">
                        Product: <span id="restoreProductName" class="font-semibold text-gray-700"></span><br>
                        Quantity: <span id="restoreQuantity" class="font-semibold text-gray-700"></span>
                    </p>
                    <!-- FIX: Removed "This will re-add the stock to the product inventory" warning -->
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
                    <i class="fas fa-undo mr-2"></i>Yes, Restore
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Custom Force Delete Confirmation Modal -->
<div id="forceDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="forceDeleteModalContent">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Permanent Delete</h3>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-skull-crossbones text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">Are you sure you want to permanently delete this delivery?</p>
                    <p class="text-sm text-gray-500 mb-3">
                        Product: <span id="forceDeleteProductName" class="font-semibold text-gray-700"></span><br>
                        Quantity: <span id="forceDeleteQuantity" class="font-semibold text-gray-700"></span>
                    </p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-sm text-red-700 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            This action cannot be undone! The record will be permanently removed from the database.
                        </p>
                    </div>
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
                    <i class="fas fa-trash-alt mr-2"></i>Yes, Delete Forever
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openRestoreModal(deliveryId, productName, quantity) {
        document.getElementById('restoreProductName').textContent = productName;
        document.getElementById('restoreQuantity').textContent = quantity;
        
        const form = document.getElementById('restoreForm');
        form.action = '{{ url("admin/deliveries") }}/' + deliveryId + '/restore';
        
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

    function openForceDeleteModal(deliveryId, productName, quantity) {
        document.getElementById('forceDeleteProductName').textContent = productName;
        document.getElementById('forceDeleteQuantity').textContent = quantity;
        
        const form = document.getElementById('forceDeleteForm');
        form.action = '{{ url("admin/deliveries") }}/' + deliveryId + '/force-delete';
        
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

    document.getElementById('restoreModal').addEventListener('click', function(e) {
        if (e.target === this) closeRestoreModal();
    });

    document.getElementById('forceDeleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeForceDeleteModal();
    });

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