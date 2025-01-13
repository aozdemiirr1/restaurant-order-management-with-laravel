<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler - Saklı Saray Admin Panel</title>
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
                    <h1 class="text-xl font-semibold">Siparişler</h1>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.orders.create') }}" class="bg-[#2A2F4E] text-white px-4 py-2 rounded-lg hover:bg-[#1E2238] transition-colors">
                            <i class="fas fa-plus mr-2"></i>Yeni Sipariş
                        </a>
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

                <div class="bg-white rounded-lg shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Sipariş No</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Ürünler</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->customer->phone }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                @foreach($order->items as $item)
                                                    <div>{{ $item->quantity }}x {{ $item->menu->name }}</div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₺{{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="inline">
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
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bu siparişi silmek istediğinize emin misiniz?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
