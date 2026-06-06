@extends('layouts.app')

@section('title', 'Login - REGASCO SIS')

@section('content')
<div class="min-h-screen gradient-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-fade-in">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-2xl mb-4">
                <i class="fas fa-gas-pump text-4xl text-primary-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">REGASCO</h1>
            <p class="text-primary-100 text-sm">Sales & Inventory System</p>
        </div>

        <!-- Login Card -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Welcome Back</h2>
            
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded alert-auto-hide">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-700 text-sm">{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" required 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white"
                            placeholder="Enter your username"
                            value="{{ old('username') }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white"
                            placeholder="Enter your password">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full btn-gradient text-white font-semibold py-3 rounded-xl shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Authorized personnel only</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-primary-100 text-xs">
            <p>&copy; 2024 REGASCO Sales & Inventory System</p>
            <p class="mt-1">Secure & Reliable</p>
        </div>
    </div>
</div>
@endsection