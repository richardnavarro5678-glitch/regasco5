@extends('layouts.admin')

@section('title', 'Cashiers - REGASCO SIS')
@section('page-title', 'Cashier Management')

@section('admin-content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm">Manage cashier accounts and permissions</p>
        </div>
        <a href="{{ route('admin.cashiers.create') }}" 
           class="bg-[#f97316] hover:bg-[#ea580c] text-white px-5 py-2.5 rounded-xl shadow-lg flex items-center space-x-2 transition-all">
            <i class="fas fa-plus text-sm"></i>
            <span class="font-medium">Create Cashier Account</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($cashiers as $cashier)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover-lift">
            <!-- Profile Header -->
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center shadow-md flex-shrink-0">
                    <i class="fas fa-user text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-gray-800 truncate">{{ $cashier->name }}</h3>
                    <p class="text-sm text-gray-500">@ {{ $cashier->username ?? explode('@', $cashier->email)[0] }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2 {{ $cashier->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $cashier->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <!-- Info -->
            <div class="space-y-2 mb-6">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-phone w-5 text-gray-400"></i>
                    <!-- FIX: Changed $cashier->phone to $cashier->phone_number to match database field -->
                    <span>{{ $cashier->phone_number ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-shopping-cart w-5 text-gray-400"></i>
                    <span>{{ $cashier->sales()->count() }} sales transactions</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-calendar w-5 text-gray-400"></i>
                    <span>Created {{ $cashier->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-3">
                <form method="POST" action="{{ route('admin.cashiers.toggle-status', $cashier) }}" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 {{ $cashier->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} rounded-xl text-sm font-medium transition-colors">
                        <i class="fas {{ $cashier->is_active ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                        {{ $cashier->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                
                <button onclick="openResetModal({{ $cashier->user_id ?? $cashier->id }}, '{{ $cashier->name }}')" 
                    class="flex-1 flex items-center justify-center px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl text-sm font-medium transition-colors">
                    <i class="fas fa-key mr-2"></i>
                    Reset Password
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <p class="text-lg font-medium text-gray-600">No cashiers found</p>
                <p class="text-sm text-gray-400 mb-6">Add your first cashier account</p>
                <a href="{{ route('admin.cashiers.create') }}" class="inline-flex items-center px-6 py-3 bg-[#f97316] hover:bg-[#ea580c] text-white rounded-xl shadow-lg transition-all">
                    <i class="fas fa-plus mr-2"></i>Create Cashier Account
                </a>
            </div>
        </div>
    @endforelse
</div>

<!-- ==================== RESET PASSWORD MODAL ==================== -->
<div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300" id="resetModalContent">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-key text-white text-lg"></i>
                </div>
                <h3 class="text-white font-bold text-lg">Reset Password</h3>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-lock text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-800 font-medium mb-1">Set new password for:</p>
                    <p class="text-sm text-gray-500 mb-3">Cashier: <span id="resetCashierName" class="font-semibold text-gray-700"></span></p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="newPassword" required minlength="6"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter new password (min 6 characters)">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="confirmPassword" required minlength="6"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Confirm new password">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
            <button onclick="closeResetModal()" 
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition-all font-medium text-sm">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            
            <form id="resetPasswordForm" method="POST" class="inline" onsubmit="return validatePasswords()">
                @csrf
                @method('PATCH')
                <input type="hidden" name="password" id="formPassword">
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all font-medium text-sm shadow-lg">
                    <i class="fas fa-save mr-2"></i>Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openResetModal(userId, cashierName) {
        document.getElementById('resetCashierName').textContent = cashierName;
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
        document.getElementById('formPassword').value = '';
        
        const form = document.getElementById('resetPasswordForm');
        form.action = '{{ url("admin/cashiers") }}/' + userId + '/reset-password';
        
        const modal = document.getElementById('resetPasswordModal');
        const content = document.getElementById('resetModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeResetModal() {
        const modal = document.getElementById('resetPasswordModal');
        const content = document.getElementById('resetModalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function validatePasswords() {
        const newPass = document.getElementById('newPassword').value;
        const confirmPass = document.getElementById('confirmPassword').value;
        
        if (newPass.length < 6) {
            alert('Password must be at least 6 characters long.');
            return false;
        }
        
        if (newPass !== confirmPass) {
            alert('Passwords do not match. Please try again.');
            return false;
        }
        
        document.getElementById('formPassword').value = newPass;
        
        return true;
    }

    document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
        if (e.target === this) closeResetModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('resetPasswordModal').classList.contains('hidden')) {
                closeResetModal();
            }
        }
    });
</script>
@endsection