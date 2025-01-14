@extends('layouts.admin')

@section('title', 'Siparişler')

@section('content')
<div x-data="{
    showAddModal: false,
    showViewModal: false,
    orderData: null,
    async viewOrder(id) {
        try {
            const response = await fetch(`/admin/orders/${id}`);
            this.orderData = await response.json();
            this.showViewModal = true;
        } catch (error) {
            console.error('Sipariş bilgileri alınamadı:', error);
        }
    }
}" class="bg-white">
    <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-base font-medium text-gray-700">Sipariş Listesi</h2>
        <button @click="showAddModal = true" class="bg-[#f39c12] text-white px-3 py-1.5 rounded text-sm hover:bg-[#e67e22] transition-colors flex items-center gap-1.5">
            <i class="fas fa-plus text-xs"></i>
            <span>Yeni Sipariş</span>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Sipariş No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Müşteri</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Durum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tarih</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">#{{ $order->id }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $order->customer->name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->customer->phone }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">₺{{ number_format($order->total_amount, 2) }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-medium rounded
                            @if($order->status === 'preparing') bg-yellow-50 text-yellow-700
                            @elseif($order->status === 'delivered') bg-green-50 text-green-700
                            @else bg-red-50 text-red-700
                            @endif">
                            @if($order->status === 'preparing') Hazırlanıyor
                            @elseif($order->status === 'delivered') Teslim Edildi
                            @else İptal Edildi
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-1">
                        <button @click="viewOrder({{ $order->id }})" class="text-white bg-blue-500 rounded px-2 py-1">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if($order->status === 'preparing')
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="text-white bg-green-500 rounded px-2 py-1">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="text-white bg-red-500 rounded px-2 py-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
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
                                        class="text-sm bg-gray-100 text-gray-600 px-3 py-1.5 rounded hover:bg-gray-200 transition-colors">
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
                                class="px-4 py-2 text-sm font-medium text-white bg-[#f39c12] rounded-lg hover:bg-[#e67e22] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f39c12] transition-colors">
                                Siparişi Oluştur
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
                                    <span class="px-3 py-1 text-xs font-medium rounded-full"
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

                                <!-- Durum Güncelleme Butonları -->
                                <div x-show="orderData.status === 'preparing'" class="flex justify-end gap-3">
                                    <form :action="'/admin/orders/' + orderData.id + '/update-status'" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                            Teslim Edildi
                                        </button>
                                    </form>
                                    <form :action="'/admin/orders/' + orderData.id + '/update-status'" method="POST">
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
