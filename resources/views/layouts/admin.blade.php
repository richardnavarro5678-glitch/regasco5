<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - REGASCO SIS')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar - Dark Blue -->
        <aside class="w-64 bg-[#1e3a8a] flex-shrink-0 overflow-y-auto hidden md:block z-20">
            <div class="p-6 border-b border-blue-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-gas-pump text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">REGASCO</h1>
                        <p class="text-xs text-blue-200">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="mt-4 pb-6">
                <!-- Main Section -->
                <div class="px-4 mb-2">
                    <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Main</p>
                </div>
                
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-2"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Sales History -->
                <a href="{{ route('admin.sales.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.sales.*') && !request()->routeIs('admin.sales-trends.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-receipt w-6 text-center mr-2"></i>
                    <span class="font-medium">Sales History</span>
                </a>

                <!-- DAGDAG - Sales Trends -->
                <a href="{{ route('admin.sales-trends.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.sales-trends.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-chart-line w-6 text-center mr-2"></i>
                    <span class="font-medium">Sales Trends</span>
                </a>

                <!-- Products -->
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-box w-6 text-center mr-2"></i>
                    <span class="font-medium">Products</span>
                </a>

                <!-- Suppliers -->
                <a href="{{ route('admin.suppliers.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.suppliers.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-truck w-6 text-center mr-2"></i>
                    <span class="font-medium">Suppliers</span>
                </a>

                <!-- Inventory Section -->
                <div class="px-4 mt-6 mb-2">
                    <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Inventory</p>
                </div>

                <!-- Deliveries -->
                <a href="{{ route('admin.deliveries.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.deliveries.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-dolly w-6 text-center mr-2"></i>
                    <span class="font-medium">Deliveries</span>
                </a>

                <!-- Stock Adjustments -->
                <a href="{{ route('admin.adjustments.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.adjustments.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-sliders-h w-6 text-center mr-2"></i>
                    <span class="font-medium">Adjustments</span>
                </a>

                <!-- Activity Logs -->
                <a href="{{ route('admin.movements.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.movements.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-exchange-alt w-6 text-center mr-2"></i>
                    <span class="font-medium">Activity Logs</span>
                </a>

                <!-- Supplier Returns -->
                <a href="{{ route('admin.supplier-returns.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.supplier-returns.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-undo w-6 text-center mr-2"></i>
                    <span class="font-medium">Supplier Returns</span>
                </a>

                <!-- Management Section -->
                <div class="px-4 mt-6 mb-2">
                    <p class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Management</p>
                </div>

                <!-- Cashiers -->
                <a href="{{ route('admin.cashiers.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.cashiers.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-users w-6 text-center mr-2"></i>
                    <span class="font-medium">Cashiers</span>
                </a>

                <!-- Reports -->
                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-3 mx-2 rounded-lg text-white transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600' : 'hover:bg-blue-800' }}">
                    <i class="fas fa-chart-pie w-6 text-center mr-2"></i>
                    <span class="font-medium">Reports</span>
                </a>
            </nav>

            <!-- User Info & Logout -->
            <div class="mt-auto p-4 border-t border-blue-800">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-blue-200">Administrator</p>
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
                            <p class="text-xs text-gray-600">System Administrator</p>
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

                @yield('admin-content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>