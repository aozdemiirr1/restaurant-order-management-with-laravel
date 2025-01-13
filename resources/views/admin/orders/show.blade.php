<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayı - Saklı Saray Admin Panel</title>
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
                        <h1 class="text-xl font-semibold">Sipariş #{{ $order->id }}</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors" onclick="return confirm('Bu siparişi silmek istediğinize emin misiniz?')">
                                <i class="fas fa-trash mr-2"></i>Siparişi Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-6">
                    <!-- Sipariş Bilgileri -->
                    <div class="col-span-2 bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Sipariş Bilgileri</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Sipariş Durumu</p>
                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()"
                                        class="text-sm rounded-full px-3 py-1 font-semibold
                                        {{ $order->status == 'preparing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        @foreach(App\Models\Order::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Sipariş Tarihi</p>
                                <p class="text-sm font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Toplam Tutar</p>
                                <p class="text-sm font-medium">₺{{ number_format($order->total_amount, 2) }}</p>
                            </div>
                            @if($order->notes)
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500">Notlar</p>
                                    <p class="text-sm">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6">
                            <h3 class="text-md font-semibold mb-3">Sipariş Detayı</h3>
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500 uppercase">
                                        <th class="pb-2">Ürün</th>
                                        <th class="pb-2">Adet</th>
                                        <th class="pb-2">Birim Fiyat</th>
                                        <th class="pb-2">Toplam</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td class="py-3">
                                                <div class="text-sm font-medium">{{ $item->menu->name }}</div>
                                            </td>
                                            <td class="py-3">
                                                <div class="text-sm">{{ $item->quantity }}</div>
                                            </td>
                                            <td class="py-3">
                                                <div class="text-sm">₺{{ number_format($item->unit_price, 2) }}</div>
                                            </td>
                                            <td class="py-3">
                                                <div class="text-sm">₺{{ number_format($item->subtotal, 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="pt-4 text-right text-sm font-medium">Toplam:</td>
                                        <td class="pt-4 text-sm font-medium">₺{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Müşteri Bilgileri -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Müşteri Bilgileri</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Ad Soyad</p>
                                <p class="text-sm font-medium">{{ $order->customer->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Telefon</p>
                                <p class="text-sm font-medium">{{ $order->customer->phone }}</p>
                            </div>
                            @if($order->customer->email)
                                <div>
                                    <p class="text-sm text-gray-500">E-posta</p>
                                    <p class="text-sm font-medium">{{ $order->customer->email }}</p>
                                </div>
                            @endif
                            @if($order->customer->address)
                                <div>
                                    <p class="text-sm text-gray-500">Adres</p>
                                    <p class="text-sm">{{ $order->customer->address }}</p>
                                </div>
                            @endif
                            <div class="pt-4">
                                <p class="text-sm text-gray-500">Toplam Sipariş</p>
                                <p class="text-sm font-medium">{{ $order->customer->total_orders }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Toplam Harcama</p>
                                <p class="text-sm font-medium">₺{{ number_format($order->customer->total_spent, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
