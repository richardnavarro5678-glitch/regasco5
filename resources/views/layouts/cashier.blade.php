<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier - REGASCO SIS')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-link {
            transition: all 0.3s ease;
        }
        .sidebar-link:hover {
            transform: translateX(4px);
        }
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar - EXACT ADMIN COLORS -->
        <aside class="w-64 bg-[#1e3a8a] flex-shrink-0 overflow-y-auto hidden md:block z-20">
            <div class="p-6 border-b border-blue-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-gas-pump text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">REGASCO</h1>
                        <p class="text-xs text-blue-200">Cashier Panel</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="mt-4 pb-6">
                <!-- Main Section -->
                <div class="px-4 mb-2">
                    <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Main</p>
                </div>
                
                <a href="{{ route('cashier.dashboard') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('cashier.dashboard') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-2"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('cashier.sales.create') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('cashier.sales.create') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-plus-circle w-6 text-center mr-2"></i>
                    <span class="font-medium">New Sale</span>
                </a>

                <a href="{{ route('cashier.sales.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('cashier.sales.index') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-receipt w-6 text-center mr-2"></i>
                    <span class="font-medium">Sales History</span>
                </a>

                <!-- DAGDAG: Sales Trend Analytics -->
                <a href="{{ route('cashier.sales-trend.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('cashier.sales-trend.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-chart-line w-6 text-center mr-2"></i>
                    <span class="font-medium">Sales Trend</span>
                </a>

                <!-- Account Section -->
                <div class="px-4 mt-6 mb-2">
                    <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Account</p>
                </div>

                <!-- FIX: Profile Link -->
                <a href="{{ route('cashier.profile.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('cashier.profile.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-user-cog w-6 text-center mr-2"></i>
                    <span class="font-medium">Profile</span>
                </a>
            </nav>

            <!-- FIX: User Info & Logout -->
            <div class="mt-auto p-4 border-t border-blue-800">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-blue-200">Cashier</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 flex-shrink-0 z-10">
                <div class="flex items-center">
                    <button class="md:hidden mr-4 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-600">@yield('page-subtitle', '')</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="text-gray-600 hover:text-gray-800 relative">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-600">Cashier Account</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('cashier-content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>