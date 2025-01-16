@extends('layouts.admin')

@section('title', 'Müşteriler')

@section('content')
<div x-data="{
    showAddModal: false,
    showEditModal: false,
    showDeleteModal: false,
    customerData: null,
    deleteModalTitle: '',
    deleteModalMessage: '',
    deleteModalAction: '',
    searchTerm: '',
    customers: {{ Js::from($customers->items()) }},
    filteredCustomers: [],

    init() {
        this.filteredCustomers = this.customers;
        this.$watch('searchTerm', (value) => {
            if (value.length >= 2) {
                this.searchCustomers(value.toLowerCase());
            } else {
                this.filteredCustomers = this.customers;
            }
        });
    },

    searchCustomers(term) {
        this.filteredCustomers = this.customers.filter(customer =>
            customer.name.toLowerCase().includes(term) ||
            customer.phone?.toLowerCase().includes(term) ||
            customer.email?.toLowerCase().includes(term) ||
            customer.address?.toLowerCase().includes(term)
        );
    },

    async editCustomer(id) {
        try {
            const response = await fetch(`/admin/customers/${id}/edit`);
            this.customerData = await response.json();
            this.showEditModal = true;
        } catch (error) {
            console.error('Müşteri bilgileri alınamadı:', error);
        }
    },

    confirmDelete(id, name) {
        this.deleteModalTitle = 'Müşteriyi Sil';
        this.deleteModalMessage = `${name} isimli müşteriyi silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`;
        this.deleteModalAction = `/admin/customers/${id}`;
        this.showDeleteModal = true;
    }
}" class="bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center p-4 border-b">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Müşteri Listesi</h2>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text"
                       x-model="searchTerm"
                       class="block w-72 pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                       placeholder="Müşteri ara... (min. 2 karakter)">
            </div>
        </div>
        <button @click="showAddModal = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-1.5 font-medium">
            <i class="fas fa-plus text-xs"></i>
            <span>Yeni Müşteri</span>
        </button>
    </div>

    <!-- Tablo -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Müşteri</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">İletişim</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Adres</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="customer in filteredCustomers" :key="customer.id">
                    <tr class="hover:bg-gray-50/40 transition-colors">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900" x-text="customer.name"></div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900" x-text="customer.phone"></div>
                            <div x-show="customer.email" class="text-xs text-gray-500" x-text="customer.email"></div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900" x-text="customer.address"></div>
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <button @click="editCustomer(customer.id)"
                                    class="text-blue-400 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 rounded px-2 py-1 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="confirmDelete(customer.id, customer.name)"
                                    class="text-red-400 hover:text-red-800 bg-red-100 hover:bg-red-200 rounded px-2 py-1 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>
                <tr x-show="filteredCustomers.length === 0">
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-search text-2xl"></i>
                            <p class="text-sm">Müşteri bulunamadı.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $customers->links() }}
    </div>

    <!-- Yeni Müşteri Ekleme Modal -->
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
            <div class="inline-block align-bottom bg-white rounded-sm text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white">
                    <div class="flex justify-between items-center p-4 border-b">
                        <h3 class="text-base font-medium text-gray-700">Yeni Müşteri</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('admin.customers.store') }}" method="POST" class="p-4">
                        @csrf
                        <div class="space-y-5">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Müşteri Adı
                                </label>
                                <div class="relative">
                                    <input type="text" name="name" required
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                        placeholder="Müşteri adını girin">
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Telefon
                                </label>
                                <div class="relative">
                                    <input type="tel" name="phone"
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                        placeholder="Telefon numarası girin">
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    E-posta
                                </label>
                                <div class="relative">
                                    <input type="email" name="email"
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                        placeholder="E-posta adresini girin">
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Adres
                                </label>
                                <div class="relative">
                                    <textarea name="address" rows="3"
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                        placeholder="Adres bilgilerini girin"></textarea>
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
                                <span id="submit-button-text">Kaydet</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Müşteri Düzenleme Modal -->
    <div x-show="showEditModal" x-cloak
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
            <div class="inline-block align-bottom bg-white rounded-sm text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white">
                    <div class="flex justify-between items-center p-4 border-b">
                        <h3 class="text-base font-medium text-gray-700">Müşteri Düzenle</h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <template x-if="customerData">
                        <form :action="'/admin/customers/' + customerData.id" method="POST" class="p-4">
                            @csrf
                            @method('PUT')
                            <div class="space-y-5">
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Ad Soyad
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="name" x-model="customerData.name" required
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="Ad soyad girin">
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Telefon
                                    </label>
                                    <div class="relative">
                                        <input type="tel" name="phone" x-model="customerData.phone" required
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="Telefon numarası girin">
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        E-posta
                                    </label>
                                    <div class="relative">
                                        <input type="email" name="email" x-model="customerData.email"
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="E-posta adresini girin">
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Adres
                                    </label>
                                    <div class="relative">
                                        <textarea name="address" rows="3" x-model="customerData.address"
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="Adres bilgilerini girin"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="showEditModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                                    İptal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-[#f39c12] rounded-lg hover:bg-[#e67e22] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f39c12] transition-colors">
                                    Güncelle
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @include('partials.delete-modal')
</div>
@endsection
