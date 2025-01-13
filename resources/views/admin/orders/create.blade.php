<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Sipariş - Saklı Saray Admin Panel</title>
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
                <a href="{{ route('admin.menus.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-utensils mr-3"></i>
                    Menüler
                </a>
                <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-users mr-3"></i>
                    Müşteriler
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 bg-[#1E2238] text-white">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    Siparişler
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-8 py-4">
                    <div class="flex items-center">
                        <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-xl font-semibold">Yeni Sipariş</h1>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.orders.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Müşteri Seçimi -->
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Müşteri</label>
                            <select name="customer_id" id="customer_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2A2F4E] focus:ring focus:ring-[#2A2F4E] focus:ring-opacity-50">
                                <option value="">Müşteri Seçin</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} - {{ $customer->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notlar -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                            <textarea name="notes" id="notes" rows="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2A2F4E] focus:ring focus:ring-[#2A2F4E] focus:ring-opacity-50">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Ürün Seçimi -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ürünler</h3>
                        <div id="menu-items" class="space-y-4">
                            <div class="menu-item grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="col-span-2">
                                    <select name="menu_ids[]" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2A2F4E] focus:ring focus:ring-[#2A2F4E] focus:ring-opacity-50">
                                        <option value="">Ürün Seçin</option>
                                        @foreach($menus as $menu)
                                            <option value="{{ $menu->id }}" data-price="{{ $menu->price }}">
                                                {{ $menu->name }} - ₺{{ number_format($menu->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <input type="number" name="quantities[]" value="1" min="1" required placeholder="Adet" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2A2F4E] focus:ring focus:ring-[#2A2F4E] focus:ring-opacity-50">
                                </div>
                                <div class="flex items-center">
                                    <button type="button" class="remove-item text-red-600 hover:text-red-800" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-item" class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2A2F4E]">
                            <i class="fas fa-plus mr-2"></i>
                            Ürün Ekle
                        </button>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-[#2A2F4E] text-white px-6 py-2 rounded-lg hover:bg-[#1E2238] transition-colors">
                            Siparişi Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.getElementById('menu-items');
            const addItemButton = document.getElementById('add-item');

            // İlk ürün satırındaki silme butonunu göster/gizle
            updateRemoveButtons();

            addItemButton.addEventListener('click', function() {
                const newItem = menuItems.children[0].cloneNode(true);

                // Seçili değerleri temizle
                newItem.querySelector('select').value = '';
                newItem.querySelector('input[type="number"]').value = '1';

                // Silme butonunu göster
                newItem.querySelector('.remove-item').style.display = 'block';

                menuItems.appendChild(newItem);
                updateRemoveButtons();

                // Yeni eklenen satırın silme butonuna event listener ekle
                newItem.querySelector('.remove-item').addEventListener('click', function() {
                    newItem.remove();
                    updateRemoveButtons();
                });
            });

            // Mevcut silme butonlarına event listener ekle
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    button.closest('.menu-item').remove();
                    updateRemoveButtons();
                });
            });

            function updateRemoveButtons() {
                const items = menuItems.children;
                const removeButtons = document.querySelectorAll('.remove-item');

                // Eğer sadece bir ürün satırı varsa silme butonunu gizle
                if (items.length === 1) {
                    removeButtons[0].style.display = 'none';
                } else {
                    removeButtons.forEach(button => {
                        button.style.display = 'block';
                    });
                }
            }
        });
    </script>
</body>
</html>
