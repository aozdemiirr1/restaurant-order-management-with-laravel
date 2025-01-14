@extends('layouts.admin')

@section('title', 'Menüler')

@section('content')
<div x-data="{
    showAddModal: false,
    showEditModal: false,
    editingMenu: null,
    menuData: null,
    async editMenu(id) {
        this.editingMenu = id;
        try {
            const response = await fetch(`/admin/menus/${id}/edit`);
            this.menuData = await response.json();
            this.showEditModal = true;
        } catch (error) {
            console.error('Menü bilgileri alınamadı:', error);
        }
    }
}" class="bg-white">
    <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-base font-medium text-gray-700">Menü Listesi</h2>
        <button @click="showAddModal = true" class="bg-[#f39c12] text-white px-3 py-1.5 rounded text-sm hover:bg-[#e67e22] transition-colors flex items-center gap-1.5">
            <i class="fas fa-plus text-xs"></i>
            <span>Yeni Menü</span>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Görsel</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Menü Adı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Fiyat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Durum</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($menus as $menu)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-4 py-2.5 whitespace-nowrap">
                        @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-12 h-12 object-cover rounded">
                        @else
                            <div class="w-12 h-12 bg-gray-50 rounded flex items-center justify-center">
                                <i class="fas fa-utensils text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $menu->name }}</div>
                        <div class="text-xs text-gray-500">{{ Str::limit($menu->description, 50) }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">₺{{ number_format($menu->price, 2) }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="text-sm text-gray-800">{{ $menu->category_name }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-sm {{ $menu->is_available ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                            {{ $menu->is_available ? 'Aktif' : 'Pasif' }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-1">
                        <button @click="editMenu({{ $menu->id }})" class="text-white bg-blue-500 rounded px-2 py-1 ">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-white bg-red-500 rounded px-2 py-1" onclick="return confirm('Bu menüyü silmek istediğinize emin misiniz?')">
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
        {{ $menus->links() }}
    </div>

    <!-- Yeni Menü Ekleme Modal -->
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
                        <h3 class="text-base font-medium text-gray-700">Yeni Menü Ekle</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
                        @csrf
                        <div class="space-y-5">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Menü Adı</label>
                                <div class="relative">
                                    <input type="text" name="name" required
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                        placeholder="Menü adını girin">
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Açıklama</label>
                                <div class="relative">
                                    <textarea name="description" rows="3"
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                        placeholder="Menü açıklamasını girin"></textarea>
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Fiyat</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="price" required
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500 text-sm">₺</span>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Kategori</label>
                                <div class="relative">
                                    <select name="category_id" required
                                        class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer">
                                        <option value="">Kategori seçin</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Görsel</label>
                                <div class="relative">
                                    <input type="file" name="image" accept="image/*"
                                        class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                        file:text-sm file:font-medium
                                        file:bg-gray-50 file:text-gray-700
                                        hover:file:bg-gray-100
                                        focus:outline-none">
                                </div>
                            </div>

                            <div class="flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_available" value="1" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#f39c12]"></div>
                                    <span class="ml-2 text-sm font-medium text-gray-600">Aktif</span>
                                </label>
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

    <!-- Menü Düzenleme Modal -->
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
                        <h3 class="text-base font-medium text-gray-700">Menü Düzenle</h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <template x-if="menuData">
                        <form :action="'/admin/menus/' + editingMenu" method="POST" enctype="multipart/form-data" class="p-4">
                            @csrf
                            @method('PUT')
                            <div class="space-y-5">
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Menü Adı</label>
                                    <div class="relative">
                                        <input type="text" name="name" x-model="menuData.name" required
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="Menü adını girin">
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Açıklama</label>
                                    <div class="relative">
                                        <textarea name="description" rows="3" x-model="menuData.description"
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="Menü açıklamasını girin"></textarea>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Fiyat</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="price" x-model="menuData.price" required
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer"
                                            placeholder="0.00">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <span class="text-gray-500 text-sm">₺</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Kategori</label>
                                    <div class="relative">
                                        <select name="category_id" x-model="menuData.category_id" required
                                            class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-transparent border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-0 focus:border-[#f39c12] peer">
                                            <option value="">Kategori seçin</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Görsel</label>
                                    <div x-show="menuData.image" class="mb-3">
                                        <img :src="'/storage/' + menuData.image" class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                                    </div>
                                    <div class="relative">
                                        <input type="file" name="image" accept="image/*"
                                            class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                            file:text-sm file:font-medium
                                            file:bg-gray-50 file:text-gray-700
                                            hover:file:bg-gray-100
                                            focus:outline-none">
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_available" x-model="menuData.is_available" value="1" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#f39c12]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-600">Aktif</span>
                                    </label>
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
