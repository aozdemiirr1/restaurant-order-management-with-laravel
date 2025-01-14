<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saklı Saray - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-[#2A2F4E] text-white">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-xl font-bold">Saklı Saray</h2>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 bg-[#1E2238] text-white">
                    <i class="fas fa-home mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.menus.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-utensils mr-3"></i>
                    Menüler
                </a>
                <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-users mr-3"></i>
                    Müşteriler
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-shopping-bag mr-3"></i>
                    Siparişler
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-8 py-4">
                    <h1 class="text-xl font-semibold">Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Admin</span>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="p-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Günlük Ciro Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Günlük Ciro</p>
                                <h3 class="text-2xl font-bold">₺{{ number_format($daily_revenue, 2) }}</h3>
                                @if(isset($debug))
                                <div class="text-xs text-gray-500 mt-2">
                                    <p>Tarih: {{ $debug['today_date'] }}</p>
                                    <p>Sipariş Sayısı: {{ $debug['orders_count'] }}</p>
                                    <p>Ham Ciro: {{ $debug['raw_daily_revenue'] }}</p>
                                </div>
                                @endif
                                <p class="text-sm {{ $revenue_change >= 0 ? 'text-green-500' : 'text-red-500' }} mt-2">
                                    <i class="fas fa-arrow-{{ $revenue_change >= 0 ? 'up' : 'down' }} mr-1"></i>
                                    {{ abs($revenue_change) }}% geçen güne göre
                                </p>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <i class="fas fa-wallet text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Günlük Satışlar Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Günlük Satışlar</p>
                                <h3 class="text-2xl font-bold">{{ $daily_orders_count }} Sipariş</h3>
                                <div class="text-xs text-gray-500 mt-2">
                                    @foreach($daily_popular_items as $item)
                                        <p>{{ $item->name }}: {{ $item->count }} adet</p>
                                    @endforeach
                                </div>
                            </div>
                            <div class="p-3 bg-green-50 rounded-lg">
                                <i class="fas fa-shopping-cart text-green-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Toplam Satış Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Toplam Satış</p>
                                <h3 class="text-2xl font-bold">{{ $monthly_orders_count }}</h3>
                                <p class="text-sm text-gray-500 mt-2">
                                    Bu ay
                                </p>
                            </div>
                            <div class="p-3 bg-purple-50 rounded-lg">
                                <i class="fas fa-chart-bar text-purple-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ortalama Satış Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Ortalama Günlük Satış</p>
                                <h3 class="text-2xl font-bold">{{ number_format($average_daily_orders, 1) }}</h3>
                                <p class="text-sm {{ $orders_change >= 0 ? 'text-green-500' : 'text-red-500' }} mt-2">
                                    <i class="fas fa-arrow-{{ $orders_change >= 0 ? 'up' : 'down' }} mr-1"></i>
                                    {{ abs($orders_change) }}% geçen haftaya göre
                                </p>
                            </div>
                            <div class="p-3 bg-orange-50 rounded-lg">
                                <i class="fas fa-chart-line text-orange-500"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Satış Grafiği -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Satış Grafiği</h3>
                    <div class="h-80">
                        <!-- Buraya grafik eklenecek -->
                        <div class="w-full h-full bg-gray-50 rounded flex items-center justify-center text-gray-400">
                            Grafik yakında eklenecek
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
