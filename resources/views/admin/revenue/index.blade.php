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

                <!-- Toplam Gider -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 stats-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-600">Toplam Gider</p>
                            <h3 class="text-2xl font-bold text-yellow-900 mt-2">{{ $expenses?->total_expenses ?? 0 }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-xl text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-yellow-200">
                        <p class="text-sm font-medium text-yellow-600">Toplam Gider Tutarı</p>
                        <h4 class="text-xl font-bold text-yellow-900 mt-1">₺{{ number_format($expenses?->total_expense_amount ?? 0, 2, '.', ',') }}</h4>
                    </div>
                </div>

                <!-- Net Kazanç -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 stats-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Net Kazanç</p>
                            <h3 class="text-2xl font-bold text-green-900 mt-2">
                                ₺{{ number_format(($revenue->total_revenue ?? 0) - ($expenses?->total_expense_amount ?? 0), 2, '.', ',') }}
                            </h3>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-xl text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if($expenses)
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Gider Detayları</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Malzeme Giderleri -->
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-sm text-gray-600">Malzeme Giderleri</p>
                        <p class="text-lg font-semibold mt-1">₺{{ number_format($expenses->material_expenses ?? 0, 2, '.', ',') }}</p>
                    </div>

                    <!-- Personel Giderleri -->
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-sm text-gray-600">Personel Giderleri</p>
                        <p class="text-lg font-semibold mt-1">₺{{ number_format($expenses->personnel_expenses ?? 0, 2, '.', ',') }}</p>
                    </div>

                    <!-- Kira Giderleri -->
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-sm text-gray-600">Kira Giderleri</p>
                        <p class="text-lg font-semibold mt-1">₺{{ number_format($expenses->rent_expenses ?? 0, 2, '.', ',') }}</p>
                    </div>

                    <!-- Fatura Giderleri -->
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-sm text-gray-600">Fatura Giderleri</p>
                        <p class="text-lg font-semibold mt-1">₺{{ number_format($expenses->utility_expenses ?? 0, 2, '.', ',') }}</p>
                    </div>

                    <!-- Diğer Giderler -->
                    <div class="bg-white border rounded-lg p-4">
                        <p class="text-sm text-gray-600">Diğer Giderler</p>
                        <p class="text-lg font-semibold mt-1">₺{{ number_format($expenses->other_expenses ?? 0, 2, '.', ',') }}</p>
                    </div>
                </div>
            </div>
            @endif

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
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Toplam Gider</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Net Kazanç</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($revenue as $index => $day)
                        @php
                            $dayExpenses = $expenses->where('date', $day->date)->first();
                            $netProfit = ($day->total_revenue ?? 0) - ($dayExpenses?->total_expense_amount ?? 0);
                        @endphp
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
                                <div class="text-sm text-yellow-600">₺{{ number_format($dayExpenses?->total_expense_amount ?? 0, 2, '.', ',') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ₺{{ number_format($netProfit, 2, '.', ',') }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
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
