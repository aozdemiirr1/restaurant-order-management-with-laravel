@extends('layouts.admin')

@section('title', 'Ciro Raporu')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Ciro Raporu</h2>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.revenue.index') }}" method="GET" class="flex items-center gap-4">
                    <select name="period" class="text-sm border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-500">
                        <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Günlük</option>
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Haftalık</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Aylık</option>
                    </select>
                    <input type="date" name="date" value="{{ $date }}"
                           class="text-sm border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-500">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm">Filtrele</button>
                </form>
            </div>
        </div>
    </div>

    <div class="p-4">
        @if($period === 'daily')
            @if($revenue)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Toplam Sipariş ve Ciro -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 stats-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Toplam Sipariş</p>
                            <h3 class="text-2xl font-bold text-blue-900 mt-2">{{ $revenue->total_orders }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-xl text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <p class="text-sm font-medium text-blue-600">Toplam Ciro</p>
                        <h4 class="text-xl font-bold text-blue-900 mt-1">₺{{ number_format($revenue->total_revenue, 2, '.', ',') }}</h4>
                    </div>
                </div>

                <!-- İptal Edilen Siparişler -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 stats-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600">İptal Edilen</p>
                            <h3 class="text-2xl font-bold text-red-900 mt-2">{{ $revenue->cancelled_orders }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-xl text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-red-200">
                        <p class="text-sm font-medium text-red-600">İptal Edilen Tutar</p>
                        <h4 class="text-xl font-bold text-red-900 mt-1">₺{{ number_format($revenue->cancelled_amount, 2, '.', ',') }}</h4>
                    </div>
                </div>

                <!-- Arşivlenen Siparişler -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 stats-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Arşivlenen</p>
                            <h3 class="text-2xl font-bold text-green-900 mt-2">{{ $revenue->archived_orders }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-archive text-xl text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-green-200">
                        <p class="text-sm font-medium text-green-600">Arşivlenen Ciro</p>
                        <h4 class="text-xl font-bold text-green-900 mt-1">₺{{ number_format($revenue->archived_revenue, 2, '.', ',') }}</h4>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center text-gray-500 py-8">
                <p>Seçilen tarih için veri bulunamadı.</p>
            </div>
            @endif
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tarih</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Toplam Sipariş</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Toplam Ciro</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İptal Edilen</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İptal Edilen Tutar</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Arşivlenen</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Arşivlenen Ciro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($revenue as $day)
                        <tr class="hover:bg-gray-50/40">
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm text-gray-900">{{ $day->total_orders }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm text-gray-900">₺{{ number_format($day->total_revenue, 2, '.', ',') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm text-red-600">{{ $day->cancelled_orders }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm text-red-600">₺{{ number_format($day->cancelled_amount, 2, '.', ',') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm text-gray-900">{{ $day->archived_orders }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm text-gray-900">₺{{ number_format($day->archived_revenue, 2, '.', ',') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Seçilen tarih aralığı için veri bulunamadı.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
