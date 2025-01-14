@extends('layouts.admin')

@section('title', 'Menü Düzenle')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ürün Adı
                </label>
                <input type="text" name="name" required value="{{ $menu->name }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori
                </label>
                <select name="category" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="döner" {{ $menu->category == 'döner' ? 'selected' : '' }}>Döner</option>
                    <option value="içecek" {{ $menu->category == 'içecek' ? 'selected' : '' }}>İçecek</option>
                    <option value="tatlı" {{ $menu->category == 'tatlı' ? 'selected' : '' }}>Tatlı</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fiyat (₺)
                </label>
                <input type="number" step="0.01" name="price" required value="{{ $menu->price }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Stok
                </label>
                <input type="number" name="stock" required value="{{ $menu->stock }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Boyut
                </label>
                <input type="text" name="size" value="{{ $menu->size }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_available" value="1" {{ $menu->is_available ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label class="ml-2 block text-sm text-gray-900">
                    Aktif
                </label>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Açıklama
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $menu->description }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <a href="{{ route('admin.menus.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                İptal
            </a>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
