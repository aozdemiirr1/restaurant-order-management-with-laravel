@extends('layouts.admin')

@section('title', 'Sipariş Detayı')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sipariş Detayları -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-800">Sipariş #{{ $order->id }}</h2>
                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    @if($order->status === 'preparing') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    @if($order->status === 'preparing') Hazırlanıyor
                    @elseif($order->status === 'delivered') Teslim Edildi
                    @else İptal Edildi
                    @endif
                </span>
            </div>

            <!-- Sipariş Öğeleri -->
            <div class="border rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Adet</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $item->menu->name }}</div>
                                @if($item->menu->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->menu->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900">₺{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900">₺{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Toplam:</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">₺{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($order->notes)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-800 mb-2">Sipariş Notu</h3>
                <p class="text-sm text-gray-600">{{ $order->notes }}</p>
            </div>
            @endif

            @if($order->status === 'preparing')
            <div class="mt-6 flex justify-end space-x-4">
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="delivered">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 transition-colors">
                        Teslim Edildi
                    </button>
                </form>
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition-colors">
                        İptal Et
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Müşteri Bilgileri -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Müşteri Bilgileri</h3>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-500">Ad Soyad</p>
                <p class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</p>
            </div>
            @if($order->customer->phone)
            <div>
                <p class="text-sm text-gray-500">Telefon</p>
                <p class="text-sm font-medium text-gray-900">{{ $order->customer->phone }}</p>
            </div>
            @endif
            @if($order->customer->email)
            <div>
                <p class="text-sm text-gray-500">E-posta</p>
                <p class="text-sm font-medium text-gray-900">{{ $order->customer->email }}</p>
            </div>
            @endif
            @if($order->customer->address)
            <div>
                <p class="text-sm text-gray-500">Adres</p>
                <p class="text-sm text-gray-900">{{ $order->customer->address }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
