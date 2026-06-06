@extends('layouts.admin')

@section('title', 'Deliveries - REGASCO SIS')
@section('page-title', 'Deliveries')

@section('admin-content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm">Manage supplier deliveries and stock intake</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.deliveries.trashed') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
                <i class="fas fa-archive text-sm"></i>
                <span class="font-medium">View Archived</span>
            </a>
            <a href="{{ route('admin.deliveries.create') }}" 
               class="bg-[#f97316] hover:bg-[#ea580c] text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
                <i class="fas fa-plus text-sm"></i>
                <span class="font-medium">New Delivery</span>
            </a>
        </div>
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
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Recorded By</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($deliveries as $delivery)
                    <tr class="hover:bg-gray-50 transition-colors">
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
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                            {{ $delivery->product->sku ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                +{{ $delivery->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $delivery->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.deliveries.edit', $delivery) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <button type="button" onclick="openDeleteModal({{ $delivery->delivery_id }}, '{{ $delivery->product->product_name ?? 'Unknown' }}', {{ $delivery->quantity }})" 
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
                                <i class="fas fa-truck text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-medium">No deliveries found</p>
                            <p class="text-sm text-gray-400">Record your first delivery</p>
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

<!-- Custom Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="deleteModalContent">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Confirm Delete</h3>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-trash-alt text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">Are you sure you want to delete this delivery?</p>
                    <p class="text-sm text-gray-500">
                        Product: <span id="deleteProductName" class="font-semibold text-gray-700"></span><br>
                        Quantity: <span id="deleteQuantity" class="font-semibold text-gray-700"></span>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" 
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition-all font-medium text-sm">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all font-medium text-sm shadow-lg">
                    <i class="fas fa-trash-alt mr-2"></i>Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(deliveryId, productName, quantity) {
        document.getElementById('deleteProductName').textContent = productName;
        document.getElementById('deleteQuantity').textContent = quantity;
        
        const form = document.getElementById('deleteForm');
        form.action = '{{ url("admin/deliveries") }}/' + deliveryId;
        
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteModalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('deleteModal').classList.contains('hidden')) {
                closeDeleteModal();
            }
        }
    });
</script>
@endsection