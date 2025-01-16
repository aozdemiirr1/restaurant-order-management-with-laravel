@extends('layouts.admin')

@section('title', 'Siparişler')

@section('content')
<div x-data="{
    showAddModal: false,
    showViewModal: false,
    showFilters: false,
    showDeleteModal: false,
    orderData: null,
    deleteModalTitle: '',
    deleteModalMessage: '',
    deleteModalAction: '',
    selectedOrders: [],
    selectAll: false,

    toggleSelectAll() {
        this.selectAll = !this.selectAll;
        this.selectedOrders = this.selectAll ? [...document.querySelectorAll('input[name=\'order_ids[]\']')].map(cb => cb.value) : [];
    },

    confirmBulkDelete() {
        if (this.selectedOrders.length === 0) return;
        this.deleteModalTitle = 'Siparişleri Arşivle';
        this.deleteModalMessage = `${this.selectedOrders.length} adet siparişi arşivlemek istediğinize emin misiniz?`;
        this.deleteModalAction = '{{ route('admin.orders.bulk-delete') }}';
        this.showDeleteModal = true;
    },

    async viewOrder(id) {
        try {
            const response = await fetch(`/admin/orders/${id}`);
            this.orderData = await response.json();
            this.showViewModal = true;
        } catch (error) {
            console.error('Sipariş bilgileri alınamadı:', error);
        }
    },

    confirmDelete(id) {
        this.deleteModalTitle = 'Siparişi Arşivle';
        this.deleteModalMessage = `#${id} numaralı siparişi arşivlemek istediğinize emin misiniz?`;
        this.deleteModalAction = '{{ route('admin.orders.destroy', '') }}/' + id;
        this.showDeleteModal = true;
    }
}" class="bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center p-4 border-b">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Sipariş Listesi</h2>
            <button
                @click="confirmBulkDelete"
                x-show="selectedOrders.length > 0"
                class="text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200 px-4 py-2 rounded text-sm transition-colors flex items-center gap-1.5">
                <i class="fas fa-trash"></i>
                <span x-text="'Seçili Olanları Sil (' + selectedOrders.length + ')'"></span>
            </button>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <button @click="showFilters = !showFilters" class="text-white bg-blue-400 px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-1.5">
                    <i class="fas fa-filter"></i>
                    <span>Filtreler</span>
                    <i class="fas" :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                @if(request()->hasAny(['search', 'customer', 'status', 'start_date', 'end_date', 'min_amount', 'max_amount', 'sort']))
                    <a href="{{ route('admin.orders.index') }}"
                    class="text-white bg-red-400 px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-1.5">
                        <i class="fas fa-times text-xs"></i>
                        <span>Sıfırla</span>
                    </a>
                @endif
            </div>
            <button @click="showAddModal = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-1.5 font-medium">
                <i class="fas fa-plus text-xs"></i>
                <span>Yeni Sipariş</span>
            </button>
        </div>
    </div>

    <!-- Filtreleme Alanı -->
    <div x-show="showFilters" x-transition
         class="border-b bg-gray-50/50 p-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Arama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border-gray-200 rounded-lg text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                            placeholder="Sipariş ID, müşteri adı, telefon...">
                    </div>
                </div>

                <!-- Müşteri -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri</label>
                    <div class="relative">
                        <select name="customer" class="block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg shadow-sm">
                            <option value="">Tüm Müşteriler</option>
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

                <!-- Tarih Aralığı -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarih Aralığı</label>
                    <div class="flex space-x-2">
                        <div class="relative flex-1">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="block w-full pl-3 pr-3 py-2 border-gray-200 rounded-lg text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                placeholder="Başlangıç">
                        </div>
                        <div class="relative flex-1">
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="block w-full pl-3 pr-3 py-2 border-gray-200 rounded-lg text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                placeholder="Bitiş">
                        </div>
                    </div>
                </div>

                <!-- Tutar Aralığı -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tutar Aralığı</label>
                    <div class="flex space-x-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₺</span>
                            </div>
                            <input type="number" name="min_amount" value="{{ request('min_amount') }}" step="0.01"
                                class="block w-full pl-7 pr-3 py-2 border-gray-200 rounded-lg text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                placeholder="Min">
                        </div>
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₺</span>
                            </div>
                            <input type="number" name="max_amount" value="{{ request('max_amount') }}" step="0.01"
                                class="block w-full pl-7 pr-3 py-2 border-gray-200 rounded-lg text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                placeholder="Max">
                        </div>
                    </div>
                </div>

                <!-- Durum ve Sıralama -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                        <div class="relative">
                            <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg shadow-sm">
                                <option value="">Tümü</option>
                                <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Hazırlanıyor</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                        <div class="relative">
                            <select name="sort" class="block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg shadow-sm">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>En Yeni</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>En Eski</option>
                                <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Tutar (Artan)</option>
                                <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Tutar (Azalan)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">
                        <label class="inline-flex items-center">
                            <div class="relative flex items-center">
                                <input type="checkbox"
                                       x-model="selectAll"
                                       @click="toggleSelectAll"
                                       class="peer h-4 w-4 cursor-pointer appearance-none rounded-sm border border-gray-300 checked:border-red-500 checked:bg-red-500 hover:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                                <svg class="pointer-events-none absolute h-4 w-4 text-white opacity-0 peer-checked:opacity-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <span class="ml-2 text-xs font-medium text-gray-500">Tümünü Seç</span>
                        </label>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Sipariş No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Müşteri</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Durum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tarih</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-4 py-3">
                        <label class="inline-flex items-center">
                            <div class="relative flex items-center">
                                <input type="checkbox"
                                       value="{{ $order->id }}"
                                       x-model="selectedOrders"
                                       name="order_ids[]"
                                       class="peer h-4 w-4 cursor-pointer appearance-none rounded-sm border border-gray-300 checked:border-red-500 checked:bg-red-500 hover:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                                <svg class="pointer-events-none absolute h-4 w-4 text-white opacity-0 peer-checked:opacity-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <span class="ml-2 text-xs font-medium text-gray-400">#{{ $order->id }}</span>
                        </label>
                    </td>
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
                            @if($order->status == 'preparing') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'delivered') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            @if($order->status == 'preparing') Hazırlanıyor
                            @elseif($order->status == 'delivered') Teslim Edildi
                            @else İptal Edildi @endif
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </td>
                    <td class="px-4 py-3 text-right space-x-1">
                        <button @click="viewOrder({{ $order->id }})"
                                class="text-blue-400 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 rounded px-2 py-1 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button @click="confirmDelete({{ $order->id }})"
                                class="text-red-400 hover:text-red-800 bg-red-100 hover:bg-red-200 rounded px-2 py-1 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center min-h-[200px] space-y-3">
                            <i class="fas fa-search text-3xl text-gray-400"></i>
                            <p class="text-sm">Sipariş bulunamadı.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $orders->links() }}
    </div>

    <!-- Yeni Sipariş Modal -->
    <div x-show="showAddModal" x-cloak
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
                        <h3 class="text-base font-medium text-gray-700">Yeni Sipariş</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('admin.orders.store') }}" method="POST" class="p-4">
                        @csrf
                        <div class="space-y-5">
                            <!-- Müşteri Seçimi -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Müşteri</label>
                                    <div class="relative">
                                        <select name="customer_id" required
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer">
                                            <option value="">Müşteri Seçin</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Sipariş Notu</label>
                                    <div class="relative">
                                        <textarea name="notes" rows="1"
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="Sipariş notu girin"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Ürün Seçimi -->
                            <div class="relative">
                                <div class="flex justify-between items-center mb-4">
                                    <label class="block text-sm font-medium text-gray-600">Sipariş Öğeleri</label>
                                    <button type="button" onclick="addOrderItem()"
                                        class="text-sm bg-red-700 text-white px-3 py-1.5 rounded transition-colors">
                                        Ürün Ekle
                                    </button>
                                </div>
                                <div id="order-items" class="space-y-4">
                                    <!-- Dinamik olarak eklenecek ürünler -->
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" @click="showAddModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                                İptal
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <span id="submit-button-text">Siparişi Oluştur</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                                    <span class="px-3 py-1 text-xs font-medium rounded"
                                        :class="{
                                            'bg-yellow-50 text-yellow-700': orderData.status === 'preparing',
                                            'bg-green-50 text-green-700': orderData.status === 'delivered',
                                            'bg-red-50 text-red-700': orderData.status === 'cancelled'
                                        }"
                                        x-text="orderData.status === 'preparing' ? 'Hazırlanıyor' :
                                               orderData.status === 'delivered' ? 'Teslim Edildi' : 'İptal Edildi'">
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
                                                        <div class="text-sm text-gray-800" x-text="`₺${parseFloat(item.unit_price).toFixed(2)}`"></div>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-right">
                                                        <div class="text-sm text-gray-800" x-text="`₺${parseFloat(item.subtotal).toFixed(2)}`"></div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray-50">
                                                <td colspan="3" class="px-4 py-2.5 text-sm font-medium text-gray-700 text-right">Toplam:</td>
                                                <td class="px-4 py-2.5 text-sm font-medium text-gray-700 text-right" x-text="`₺${parseFloat(orderData.total_amount).toFixed(2)}`"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Sipariş Notu -->
                                <div x-show="orderData.notes" class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Sipariş Notu</h4>
                                    <p class="text-sm text-gray-600" x-text="orderData.notes"></p>
                                </div>

                                <!-- Durum Güncelleme Butonları -->
                                <div x-show="orderData.status === 'preparing'" class="flex justify-end gap-3">
                                    <form :action="'/admin/orders/' + orderData.id + '/status'" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                            Teslim Edildi
                                        </button>
                                    </form>
                                    <form :action="'/admin/orders/' + orderData.id + '/status'" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            İptal Et
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak
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
            <div class="inline-block align-bottom bg-white rounded-sm text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <div class="bg-white">
                    <div class="p-4 border-b">
                        <h3 class="text-base font-medium text-gray-700" x-text="deleteModalTitle"></h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-500" x-text="deleteModalMessage"></p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form :action="deleteModalAction" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <template x-if="selectedOrders.length > 0">
                                <template x-for="id in selectedOrders" :key="id">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                            </template>
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Evet
                            </button>
                        </form>
                        <button type="button" @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            İptal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let itemCount = 0;

function addOrderItem() {
    const itemsContainer = document.getElementById('order-items');
    const itemTemplate = `
        <div class="grid grid-cols-12 gap-4 items-start pb-4">
            <div class="col-span-5">
                <div class="relative">
                    <select name="menu_ids[]" required
                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer">
                        <option value="">Ürün Seçin</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" data-price="{{ $menu->price }}">{{ $menu->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="col-span-3">
                <input type="number" name="quantities[]" min="1" value="1" required
                    class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer">
            </div>

            <div class="col-span-3">
                <input type="text" readonly
                    class="block w-full px-4 py-2.5 text-sm text-gray-500 bg-gray-50 border border-gray-300 rounded-lg">
            </div>

            <div class="col-span-1 flex items-center justify-center">
                <button type="button" onclick="this.closest('.grid').remove()"
                    class="text-gray-400 hover:text-red-600 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    const tempContainer = document.createElement('div');
    tempContainer.innerHTML = itemTemplate;
    itemsContainer.appendChild(tempContainer.firstElementChild);

    // Ürün seçildiğinde fiyatı otomatik doldur
    const select = itemsContainer.lastElementChild.querySelector('select');
    const priceInput = itemsContainer.lastElementChild.querySelector('input[readonly]');

    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.dataset.price || '';
        priceInput.value = price ? `₺${parseFloat(price).toFixed(2)}` : '';
    });

    itemCount++;
}

// Sayfa yüklendiğinde bir ürün satırı ekle
document.addEventListener('DOMContentLoaded', function() {
    addOrderItem();
});
</script>
@endpush
@endsection
