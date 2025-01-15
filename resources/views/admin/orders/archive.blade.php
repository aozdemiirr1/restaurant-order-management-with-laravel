@extends('layouts.admin')

@section('title', 'Sipariş Arşivi')

@section('content')
<div x-data="{
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
        this.deleteModalTitle = 'Siparişleri Sil';
        this.deleteModalMessage = `${this.selectedOrders.length} adet siparişi kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`;
        this.deleteModalAction = '{{ route('admin.orders.archive.bulk-delete') }}';
        this.showDeleteModal = true;
    },

    async viewOrder(id) {
        try {
            const response = await fetch('{{ route('admin.orders.archive.show', '') }}/' + id);
            this.orderData = await response.json();
            this.showViewModal = true;
        } catch (error) {
            console.error('Sipariş bilgileri alınamadı:', error);
        }
    },

    confirmDelete(id) {
        this.deleteModalTitle = 'Siparişi Sil';
        this.deleteModalMessage = `#${id} numaralı siparişi kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`;
        this.deleteModalAction = '{{ route('admin.orders.archive.destroy', '') }}/' + id;
        this.showDeleteModal = true;
    }
}" class="bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center p-4 border-b">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Sipariş Arşivi</h2>
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Silinme Tarihi</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($archivedOrders as $order)
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
                            @if($order->status == 'delivered') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $order->status == 'delivered' ? 'Teslim Edildi' : 'İptal Edildi' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900">{{ $order->archived_at->format('d.m.Y H:i') }}</div>
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
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full mx-4">
                <form :action="deleteModalAction" method="POST" class="bg-white">
                    @csrf
                    @method('DELETE')
                    <template x-if="selectedOrders.length > 0">
                        <template x-for="id in selectedOrders" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                    </template>
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <h3 class="text-lg font-medium text-gray-900 mb-2" x-text="deleteModalTitle"></h3>
                            <p class="text-sm text-gray-500" x-text="deleteModalMessage"></p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                            İptal
                        </button>
                        <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                            Sil
                        </button>
                    </div>
                </form>
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
            this.deleteModalTitle = 'Siparişleri Sil';
            this.deleteModalMessage = `${this.selectedOrders.length} adet siparişi kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`;
            this.deleteModalAction = '{{ route('admin.orders.archive.bulk-delete') }}';
            this.showDeleteModal = true;
        },

        async viewOrder(id) {
            try {
                const response = await fetch('{{ route('admin.orders.archive.show', '') }}/' + id);
                this.orderData = await response.json();
                this.showViewModal = true;
            } catch (error) {
                console.error('Sipariş bilgileri alınamadı:', error);
            }
        },

        confirmDelete(id) {
            this.deleteModalTitle = 'Siparişi Sil';
            this.deleteModalMessage = `#${id} numaralı siparişi kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`;
            this.deleteModalAction = '{{ route('admin.orders.archive.destroy', '') }}/' + id;
            this.showDeleteModal = true;
        }
    }));
});
</script>
@endpush
@endsection
