@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Günlük Ciro Card -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
        <div class="px-5 py-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 rounded-lg bg-amber-50">
                    <i class="fas fa-wallet text-amber-500"></i>
                </div>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $revenue_change >= 0 ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }}">
                    <i class="fas fa-arrow-{{ $revenue_change >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($revenue_change) }}%
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">₺{{ number_format($daily_revenue, 2) }}</h3>
            <p class="text-sm text-gray-500">Günlük Ciro</p>
            @if(isset($debug))
            <div class="mt-3 pt-3 border-t border-gray-50 text-xs text-gray-400">
                <p>Tarih: {{ $debug['today_date'] }}</p>
                <p>Sipariş: {{ $debug['orders_count'] }}</p>
                <p>Ham Ciro: {{ $debug['raw_daily_revenue'] }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Günlük Satışlar Card -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
        <div class="px-5 py-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 rounded-lg bg-blue-50">
                    <i class="fas fa-shopping-cart text-blue-500"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $daily_orders_count }}</h3>
            <p class="text-sm text-gray-500">Günlük Satışlar</p>
            @if($daily_cancelled_orders > 0)
            <div class="mt-3 pt-3 border-t border-gray-50">
                <span class="text-xs px-2 py-1 rounded bg-rose-50 text-rose-500">
                    {{ $daily_cancelled_orders }} iptal edildi
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Toplam Satış Card -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
        <div class="px-5 py-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 rounded-lg bg-violet-50">
                    <i class="fas fa-chart-bar text-violet-500"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $monthly_orders_count }}</h3>
            <p class="text-sm text-gray-500">Aylık Toplam Satış</p>
            @if($monthly_cancelled_orders > 0)
            <div class="mt-3 pt-3 border-t border-gray-50">
                <span class="text-xs px-2 py-1 rounded bg-rose-50 text-rose-500">
                    {{ $monthly_cancelled_orders }} iptal edildi
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Ortalama Satış Card -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
        <div class="px-5 py-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 rounded-lg bg-emerald-50">
                    <i class="fas fa-chart-line text-emerald-500"></i>
                </div>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $orders_change >= 0 ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }}">
                    <i class="fas fa-arrow-{{ $orders_change >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($orders_change) }}%
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ number_format($average_daily_orders, 1) }}</h3>
            <p class="text-sm text-gray-500">Ortalama Günlük Satış</p>
        </div>
    </div>
</div>

<!-- Alt Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <!-- Satış Grafiği -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-semibold text-gray-800">Satış Grafiği</h3>
            <div class="flex gap-2">
                <button class="text-xs px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100">Haftalık</button>
                <button class="text-xs px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100">Aylık</button>
            </div>
        </div>
        <div class="h-80">
            <div id="salesChart"></div>
        </div>
    </div>

    <!-- Popüler Ürünler -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-base font-semibold text-gray-800 mb-6">Günün Popüler Ürünleri</h3>
        <div class="h-[calc(100vh-40rem)] overflow-y-auto pr-2 space-y-6">
            @foreach($daily_popular_items as $category => $items)
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-3">{{ $category }}</h4>
                <div class="space-y-4">
                    @foreach($items as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                                <i class="fas fa-utensils text-gray-400"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->count }} adet</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-emerald-500 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                                <i class="fas fa-arrow-up mr-1"></i>
                                {{ $item->count }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @if(!$loop->last)
                <hr class="border-gray-100">
            @endif
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const salesData = @json($sales_data);

    const options = {
        series: [{
            name: 'Günlük Satış',
            data: salesData.map(data => data.total)
        }],
        chart: {
            type: 'area',
            height: 320,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        colors: ['#f59e0b'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [50, 100]
            }
        },
        xaxis: {
            categories: salesData.map(data => data.date),
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: '#64748b',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#64748b',
                    fontSize: '12px'
                },
                formatter: function(value) {
                    return '₺' + value.toFixed(2)
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return '₺' + value.toFixed(2)
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#salesChart"), options);
    chart.render();
});
</script>
@endpush

@endsection
