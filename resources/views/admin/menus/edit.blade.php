<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menü Düzenle - Saklı Saray Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-[#2A2F4E] text-white">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-xl font-bold">Saklı Saray</h2>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-home mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.menus.index') }}" class="flex items-center px-4 py-3 bg-[#1E2238] text-white">
                    <i class="fas fa-utensils mr-3"></i>
                    Menüler
                </a>
                <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-users mr-3"></i>
                    Müşteriler
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-8 py-4">
                    <h1 class="text-xl font-semibold">Menü Düzenle</h1>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
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
                                    Boyut
                                </label>
                                <select name="size"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">Seçiniz</option>
                                    <option value="yarım" {{ $menu->size == 'yarım' ? 'selected' : '' }}>Yarım</option>
                                    <option value="tam" {{ $menu->size == 'tam' ? 'selected' : '' }}>Tam</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stok Miktarı
                                </label>
                                <input type="number" name="stock" required value="{{ $menu->stock }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Durum
                                </label>
                                <select name="is_available" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="1" {{ $menu->is_available ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$menu->is_available ? 'selected' : '' }}>Pasif</option>
                                </select>
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
                            <button type="submit" class="bg-[#2A2F4E] text-white px-4 py-2 rounded-lg hover:bg-[#1E2238] transition-colors">
                                Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
