<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteriler - Saklı Saray Admin Panel</title>
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
                <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-3 bg-[#1E2238] text-white">
                    <i class="fas fa-users mr-3"></i>
                    Müşteriler
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-[#1E2238] transition-colors">
                    <i class="fas fa-shopping-bag mr-3"></i>
                    Siparişler
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-8 py-4">
                    <h1 class="text-xl font-semibold">Müşteriler</h1>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.customers.create') }}" class="bg-[#2A2F4E] text-white px-4 py-2 rounded-lg hover:bg-[#1E2238] transition-colors">
                            <i class="fas fa-plus mr-2"></i>Yeni Müşteri
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
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">İletişim</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Adres</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Sipariş</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Harcama</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($customers as $customer)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                            @if($customer->notes)
                                                <div class="text-sm text-gray-500">{{ Str::limit($customer->notes, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $customer->phone }}</div>
                                            <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ Str::limit($customer->address, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $customer->total_orders }} sipariş
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₺{{ number_format($customer->total_spent, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.customers.edit', $customer) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?')">
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
