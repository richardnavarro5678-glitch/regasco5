@extends('layouts.admin')

@section('title', 'Edit Supplier - REGASCO SIS')
@section('page-title', 'Edit Supplier')

@section('admin-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Supplier
            </h3>
        </div>
        
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" name="supplier_name" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ old('supplier_name', $supplier->supplier_name) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_person" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ old('contact_person', $supplier->contact_person) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Info <span class="text-red-500">*</span></label>
                    <!-- FIX: Changed name from 'contact_info' to 'phone' to match database column -->
                    <input type="text" name="phone" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ old('phone', $supplier->phone) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address', $supplier->address) }}</textarea>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $supplier->is_active ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active Supplier</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.suppliers.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-3 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
                    <i class="fas fa-save"></i>
                    <span>Update Supplier</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection