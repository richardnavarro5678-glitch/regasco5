@extends('layouts.admin')

@section('title', 'Create Cashier - REGASCO SIS')
@section('page-title', 'Create Cashier Account')

@section('admin-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-600 to-yellow-400 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-user-plus mr-2"></i>
                New Cashier Account
            </h3>
        </div>
        
        <form method="POST" action="{{ route('admin.cashiers.store') }}" class="p-8">
            @csrf
            
            <div class="space-y-6">
                <!-- Username -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-at"></i>
                        </span>
                        <input type="text" name="username" required 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                            placeholder="Enter unique username"
                            value="{{ old('username') }}">
                    </div>
                    @error('username')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="name" required 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                            placeholder="Enter full name"
                            value="{{ old('name') }}">
                    </div>
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input type="text" name="phone_number" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                            placeholder="Enter phone number (optional)"
                            value="{{ old('phone_number') }}">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required minlength="6"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                            placeholder="Set password (min 6 characters)">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Cashier can change this later in their profile</p>
                </div>
            </div>

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.cashiers.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
                <button type="submit" class="btn-gradient text-black px-8 py-3 rounded-xl shadow-lg flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Create Account</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection