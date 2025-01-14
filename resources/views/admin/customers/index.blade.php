@extends('layouts.admin')

@section('title', 'Müşteriler')

@section('content')
<div x-data="{
    showAddModal: false,
    showEditModal: false,
    editingCustomer: null,
    customerData: null,
    async editCustomer(id) {
        this.editingCustomer = id;
        try {
            const response = await fetch(`/admin/customers/${id}/edit`);
            this.customerData = await response.json();
            this.showEditModal = true;
        } catch (error) {
            console.error('Müşteri bilgileri alınamadı:', error);
        }
    }
}" class="bg-white">
    <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-base font-medium text-gray-700">Müşteri Listesi</h2>
        <button @click="showAddModal = true" class="bg-red-700 text-white px-3 py-1.5 rounded text-sm transition-colors flex items-center gap-1.5">
            <i class="fas fa-plus text-xs"></i>
            <span>Yeni Müşteri</span>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Ad Soyad</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Telefon</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Kayıt Tarihi</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($customers as $customer)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $customer->name }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $customer->email }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $customer->phone }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $customer->created_at->format('d.m.Y') }}</div>
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-1">
                        <button @click="editCustomer({{ $customer->id }})" class="text-white bg-blue-500 rounded px-2 py-1">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-white bg-red-500 rounded px-2 py-1" onclick="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
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
                                class="px-4 py-2 text-sm font-medium text-white bg-[#f39c12] rounded-lg hover:bg-[#e67e22] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f39c12] transition-colors">
                                Kaydet
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
                        <form :action="'/admin/customers/' + editingCustomer" method="POST" class="p-4">
                            @csrf
                            @method('PUT')
                            <div class="space-y-5">
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Müşteri Adı
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="name" x-model="customerData.name" required
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="Müşteri adını girin">
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Telefon
                                    </label>
                                    <div class="relative">
                                        <input type="tel" name="phone" x-model="customerData.phone"
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
</div>
@endsection
