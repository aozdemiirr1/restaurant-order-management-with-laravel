<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
        }
        .stats-card {
            transition: all 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .icon-gradient {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="min-h-screen flex bg-gray-50">
        <!-- Sidebar -->
        <div class="w-64 bg-red-900 text-white">
            <div class="p-5 border-b border-white-700/30">
                <h2 class="text-md font-semibold text-gray-100">Restaurant Management</h2>
            </div>
            <nav class="mt-5 px-3" x-data="{ adminOpen: true }">
                <!-- Admin Section -->
                <div class="mb-4">
                    <button @click="adminOpen = !adminOpen"
                        class="w-full flex items-center justify-between px-4 py-3 text-white hover:bg-white/5 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-user-shield mr-3 text-sm"></i>
                            <span class="text-sm font-medium">Admin Panel</span>
                        </div>
                        <i class="fas" :class="adminOpen ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                    </button>

                    <div x-show="adminOpen" x-collapse>
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                            <i class="fas fa-home mr-3 text-sm"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- Revenue -->
                        <a href="{{ route('admin.revenue.index') }}"
                            class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.revenue.*') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                            <i class="fas fa-chart-line mr-3 text-sm"></i>
                            <span>Ciro Raporu</span>
                        </a>

                        <!-- Expenses -->
                        <a href="{{ route('admin.expenses.index') }}"
                            class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.expenses.*') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                            <i class="fas fa-money-bill-wave mr-3 text-sm"></i>
                            <span>Giderler</span>
                        </a>

                        <!-- Menus Section -->
                        <div class="mt-4">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase">Menü Yönetimi</p>
                            <a href="{{ route('admin.menus.index') }}"
                                class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.menus.*') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                                <i class="fas fa-utensils mr-3 text-sm"></i>
                                <span>Menüler</span>
                            </a>
                        </div>

                        <!-- Customers Section -->
                        <div class="mt-4">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase">Müşteriler</p>
                            <a href="{{ route('admin.customers.index') }}"
                                class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.customers.*') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                                <i class="fas fa-users mr-3 text-sm"></i>
                                <span>Müşteriler</span>
                            </a>
                        </div>

                        <!-- Orders Section -->
                        <div class="mt-4">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase">Siparişler</p>
                            <a href="{{ route('admin.orders.index') }}"
                                class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.archive*') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                                <i class="fas fa-shopping-cart mr-3 text-sm"></i>
                                <span>Siparişler</span>
                            </a>
                            <a href="{{ route('admin.orders.archive') }}"
                                class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.orders.archive*') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                                <i class="fas fa-archive mr-3 text-sm"></i>
                                <span>Arşiv</span>
                            </a>
                        </div>

                        <!-- Settings Section -->
                        <div class="mt-4">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase">Ayarlar</p>
                            <a href="{{ route('admin.settings.index') }}"
                                class="flex items-center px-4 py-2 mt-2 {{ request()->routeIs('admin.settings.*') ? 'bg-white/10' : 'hover:bg-white/5' }} rounded-lg text-white text-sm">
                                <i class="fas fa-cog mr-3 text-sm"></i>
                                <span>Sistem Ayarları</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm backdrop-blur-xl bg-white/90 sticky top-0 z-10">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-lg font-medium text-gray-800">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-red-500">{{ Auth::guard('admin')->user()->name }}</span>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open" class="text-red-500 hover:text-red-700 transition-colors">
                                    <i class="fas fa-user"></i>
                                </button>
                                <div x-show="open"
                                     @click.away="open = false"
                                     class="absolute right-0 top-full mt-1 bg-red-500 text-white text-xs py-2 px-4 rounded shadow-lg">
                                    <button type="submit" class="whitespace-nowrap hover:text-gray-300 transition-colors">
                                        Çıkış Yap
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
