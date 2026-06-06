@extends('layouts.cashier')

@section('title', 'My Profile - REGASCO SIS')
@section('page-title', 'Profile')

@section('cashier-content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Profile Information -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- FIX: Blue header instead of primary -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-user-circle mr-2"></i>
                Profile Information
            </h3>
        </div>
        
        <form method="POST" action="{{ route('cashier.profile.update') }}" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="flex items-center mb-6">
                <!-- FIX: Blue profile picture instead of primary -->
                <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="ml-4">
                    <h4 class="font-bold text-gray-800 text-lg">{{ Auth::user()->name }}</h4>
                    <p class="text-gray-500 text-sm">@ {{ Auth::user()->username }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                        Active Cashier
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" disabled 
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed"
                        value="{{ Auth::user()->username }}">
                    <p class="text-xs text-gray-500 mt-1">Username cannot be changed</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <!-- FIX: Blue focus ring instead of primary -->
                    <input type="text" name="name" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ Auth::user()->name }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <!-- FIX: Blue focus ring instead of primary -->
                    <input type="text" name="phone_number" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ Auth::user()->phone_number }}">
                </div>
            </div>

            <!-- FIX: Blue button instead of btn-gradient -->
            <button type="submit" class="w-full mt-6 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-3 rounded-xl shadow-lg flex items-center justify-center space-x-2 transition-all">
                <i class="fas fa-save"></i>
                <span>Update Profile</span>
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-key mr-2"></i>
                Change Password
            </h3>
        </div>
        
        <form method="POST" action="{{ route('cashier.profile.password') }}" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" required 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Enter current password">
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Minimum 6 characters">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Confirm new password">
                </div>
            </div>

            <button type="submit" class="w-full mt-6 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-3 rounded-xl shadow-lg flex items-center justify-center space-x-2 transition-all">
                <i class="fas fa-lock"></i>
                <span>Change Password</span>
            </button>
        </form>
    </div>
</div>
@endsection