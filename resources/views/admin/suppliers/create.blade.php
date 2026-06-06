@extends('layouts.admin')

@section('title', 'Add Supplier - REGASCO SIS')
@section('page-title', 'Add New Supplier')

@section('admin-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-truck mr-2"></i>
                New Supplier
            </h3>
        </div>
        
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="p-8">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" name="supplier_name" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Enter company name"
                        value="{{ old('supplier_name') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_person" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Enter contact person name"
                        value="{{ old('contact_person') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Enter phone number"
                        value="{{ old('phone') }}">
                </div>

                <!-- FIX: Removed email field -->

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Enter complete address">{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.suppliers.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-3 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
                    <i class="fas fa-save"></i>
                    <span>Save Supplier</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection