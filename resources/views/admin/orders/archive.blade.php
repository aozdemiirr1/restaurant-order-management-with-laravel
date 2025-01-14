@extends('layouts.admin')

@section('title', 'Sipariş Arşivi')

@section('content')
<div x-data="orderArchive" class="bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center p-4 border-b">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Sipariş Arşivi</h2>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <button @click="showFilters = !showFilters" class="text-white bg-blue-400 px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-1.5">
                    <i class="fas fa-filter"></i>
                    <span>Filtreler</span>
                    <i class="fas" :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                @if(request()->hasAny(['status', 'customer', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.archive') }}"
                    class="text-white bg-red-400 px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-1.5">
                        <i class="fas fa-times text-xs"></i>
                        <span>Sıfırla</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Filtreleme Alanı -->
    <div x-show="showFilters" x-transition
         class="border-b bg-gray-50/50 p-4">
        <form action="{{ route('admin.orders.archive') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Müşteri Seçimi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri</label>
                    <div class="relative">
                        <select name="customer" class="block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg shadow-sm">
                            <option value="">Tümü</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Durum -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                    <div class="relative">
                        <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg shadow-sm">
                            <option value="">Tümü</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Tarih Aralığı -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg shadow-sm">
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="fas fa-filter mr-2 text-xs"></i>
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Tablo -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Sipariş No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Müşteri</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Durum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Silinme Tarihi</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($archivedOrders as $order)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->customer->phone }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900">₺{{ number_format($order->total_amount, 2) }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded
                            @if($order->status == 'delivered') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $order->status == 'delivered' ? 'Teslim Edildi' : 'İptal Edildi' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900">{{ $order->deleted_at->format('d.m.Y H:i') }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button @click="viewOrder({{ $order->id }})"
                                class="text-blue-400 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 rounded px-2 py-1 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-archive text-2xl"></i>
                            <p class="text-sm">Arşivlenmiş sipariş bulunamadı.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $archivedOrders->links() }}
    </div>

    <!-- Sipariş Detay Modal -->
    <div x-show="showViewModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-sm text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white">
                    <div class="flex justify-between items-center p-4 border-b">
                        <div>
                            <h3 class="text-base font-medium text-gray-700" x-text="'Sipariş #' + orderData?.id"></h3>
                            <p class="text-sm text-gray-500 mt-0.5" x-text="orderData?.created_at"></p>
                        </div>
                        <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <template x-if="orderData">
                            <div class="space-y-6">
                                <!-- Sipariş Durumu -->
                                <div class="flex justify-between items-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                                        :class="{
                                            'bg-green-50 text-green-700': orderData.status === 'delivered',
                                            'bg-red-50 text-red-700': orderData.status === 'cancelled'
                                        }"
                                        x-text="orderData.status === 'delivered' ? 'Teslim Edildi' : 'İptal Edildi'">
                                    </span>
                                </div>

                                <!-- Müşteri Bilgileri -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Müşteri Bilgileri</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500">Ad Soyad</p>
                                            <p class="text-sm text-gray-800" x-text="orderData.customer.name"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Telefon</p>
                                            <p class="text-sm text-gray-800" x-text="orderData.customer.phone"></p>
                                        </div>
                                        <div x-show="orderData.customer.email">
                                            <p class="text-xs text-gray-500">E-posta</p>
                                            <p class="text-sm text-gray-800" x-text="orderData.customer.email"></p>
                                        </div>
                                        <div x-show="orderData.customer.address">
                                            <p class="text-xs text-gray-500">Adres</p>
                                            <p class="text-sm text-gray-800" x-text="orderData.customer.address"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sipariş Öğeleri -->
                                <div class="border rounded-lg overflow-hidden">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-50 border-b">
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Ürün</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-600">Adet</th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Birim Fiyat</th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Toplam</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="item in orderData.items" :key="item.menu_id">
                                                <tr>
                                                    <td class="px-4 py-2.5">
                                                        <div class="text-sm text-gray-800" x-text="item.menu_name"></div>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-center">
                                                        <div class="text-sm text-gray-800" x-text="item.quantity"></div>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-right">
                                                        <div class="text-sm text-gray-800" x-text="'₺' + item.unit_price.toFixed(2)"></div>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-right">
                                                        <div class="text-sm text-gray-800" x-text="'₺' + item.subtotal.toFixed(2)"></div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray-50">
                                                <td colspan="3" class="px-4 py-2.5 text-sm font-medium text-gray-700 text-right">Toplam:</td>
                                                <td class="px-4 py-2.5 text-sm font-medium text-gray-700 text-right" x-text="'₺' + orderData.total_amount.toFixed(2)"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Sipariş Notu -->
                                <div x-show="orderData.notes" class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Sipariş Notu</h4>
                                    <p class="text-sm text-gray-600" x-text="orderData.notes"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderArchive', () => ({
        showFilters: false,
        showViewModal: false,
        orderData: null,

        async viewOrder(id) {
            try {
                const response = await fetch(`/admin/orders/archive/${id}`);
                if (!response.ok) throw new Error('Sipariş bilgileri alınamadı');
                this.orderData = await response.json();
                this.showViewModal = true;
            } catch (error) {
                console.error('Sipariş bilgileri alınamadı:', error);
                alert('Sipariş bilgileri alınamadı: ' + error.message);
            }
        }
    }));
});
</script>
@endpush
@endsection
