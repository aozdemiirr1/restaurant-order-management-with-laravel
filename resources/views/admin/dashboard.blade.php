@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <!-- Günlük Ciro Card -->
    <div class="stats-card bg-white rounded-lg shadow-sm hover:shadow p-4 border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Günlük Ciro</p>
                <h3 class="text-xl font-semibold text-gray-800">₺{{ number_format($daily_revenue, 2) }}</h3>
                @if(isset($debug))
                <div class="text-xs text-gray-400 mt-2">
                    <p>Tarih: {{ $debug['today_date'] }}</p>
                    <p>Sipariş: {{ $debug['orders_count'] }}</p>
                    <p>Ham Ciro: {{ $debug['raw_daily_revenue'] }}</p>
                </div>
                @endif
                <p class="text-xs {{ $revenue_change >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-2">
                    <i class="fas fa-arrow-{{ $revenue_change >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($revenue_change) }}% geçen güne göre
                </p>
            </div>
            <div class="p-2 rounded-lg bg-gray-50">
                <i class="fas fa-wallet text-lg icon-gradient"></i>
            </div>
        </div>
    </div>

    <!-- Günlük Satışlar Card -->
    <div class="stats-card bg-white rounded-lg shadow-sm hover:shadow p-4 border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Günlük Satışlar</p>
                <h3 class="text-xl font-semibold text-gray-800">{{ $daily_orders_count }}</h3>
                <div class="text-xs text-gray-400 mt-2">
                    @foreach($daily_popular_items as $item)
                        <p>{{ $item->name }}: {{ $item->count }} adet</p>
                    @endforeach
                </div>
            </div>
            <div class="p-2 rounded-lg bg-gray-50">
                <i class="fas fa-shopping-cart text-lg icon-gradient"></i>
            </div>
        </div>
    </div>

    <!-- Toplam Satış Card -->
    <div class="stats-card bg-white rounded-lg shadow-sm hover:shadow p-4 border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Toplam Satış</p>
                <h3 class="text-xl font-semibold text-gray-800">{{ $monthly_orders_count }}</h3>
                <p class="text-xs text-gray-400 mt-2">
                    Bu ay
                </p>
            </div>
            <div class="p-2 rounded-lg bg-gray-50">
                <i class="fas fa-chart-bar text-lg icon-gradient"></i>
            </div>
        </div>
    </div>

    <!-- Ortalama Satış Card -->
    <div class="stats-card bg-white rounded-lg shadow-sm hover:shadow p-4 border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Ortalama Günlük Satış</p>
                <h3 class="text-xl font-semibold text-gray-800">{{ number_format($average_daily_orders, 1) }}</h3>
                <p class="text-xs {{ $orders_change >= 0 ? 'text-emerald-500' : 'text-rose-500' }} mt-2">
                    <i class="fas fa-arrow-{{ $orders_change >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($orders_change) }}% geçen haftaya göre
                </p>
            </div>
            <div class="p-2 rounded-lg bg-gray-50">
                <i class="fas fa-chart-line text-lg icon-gradient"></i>
            </div>
        </div>
    </div>
</div>

<!-- Satış Grafiği -->
<div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
    <h3 class="text-sm font-medium text-gray-800 mb-4">Satış Grafiği</h3>
    <div class="h-72">
        <div class="w-full h-full bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
            <p class="text-sm">Grafik yakında eklenecek</p>
        </div>
    </div>
</div>
@endsection
