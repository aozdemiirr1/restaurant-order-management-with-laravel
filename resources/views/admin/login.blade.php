<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
        }
        .graph-bg {
            background-image: url('data:image/svg+xml,<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 3H21V21H3V3Z" stroke="%23E5E7EB" stroke-width="0.4"/></svg>');
        }
    </style>
</head>
<body class="graph-bg">
    <div class="min-h-screen flex">
        <!-- Sol taraf - Grafik ve Robot -->
        <div class="w-1/2 p-12 flex items-center justify-center relative">
            <div class="absolute top-20 right-20">
                <!-- Profit Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 w-64">
                    <div class="mb-2">
                        <h3 class="text-sm text-gray-600">Profit</h3>
                        <p class="text-gray-400 text-xs">Last Month</p>
                    </div>
                    <div class="flex items-center">
                        <span class="text-2xl font-semibold">624k</span>
                        <span class="ml-2 text-green-500 text-sm">+8.24%</span>
                    </div>
                    <div class="mt-4">
                        <svg class="w-full" height="40" viewBox="0 0 200 40">
                            <path d="M0 30 Q 40 10, 80 25 T 160 20 T 200 15" fill="none" stroke="#10B981" stroke-width="2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Robot Image - Center -->
            <img src="https://file.aiquickdraw.com/imgcompressed/img/compressed_366e93819330a30cdb8ba1bf8f265b35.webp"
                 alt="AI Robot"
                 class="w-72 h-auto z-10">

            <div class="absolute bottom-20 left-20">
                <!-- Order Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 w-64">
                    <div class="mb-2">
                        <h3 class="text-sm text-gray-600">Order</h3>
                        <p class="text-gray-400 text-xs">Last week</p>
                    </div>
                    <div class="flex items-center">
                        <span class="text-2xl font-semibold">124k</span>
                        <span class="ml-2 text-green-500 text-sm">+12.6%</span>
                    </div>
                    <div class="mt-4 flex justify-between items-end space-x-2">
                        <div class="w-8 bg-purple-200 rounded-t h-8"></div>
                        <div class="w-8 bg-purple-300 rounded-t h-12"></div>
                        <div class="w-8 bg-purple-400 rounded-t h-6"></div>
                        <div class="w-8 bg-purple-500 rounded-t h-10"></div>
                        <div class="w-8 bg-purple-600 rounded-t h-16"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ taraf - Login Formu -->
        <div class="w-1/2 p-12 flex items-center">
            <div class="w-full max-w-md">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-red-900 mb-4">Sakli Saray</h2>
                    <h1 class="text-2xl font-normal text-gray-900 mb-2">Restoran Operasyon Sistemine Hoş Geldiniz! 👋</h1>
                    <p class="text-gray-600">Hesabınıza giriş yapın ve restoranınızın operasyonlarını verimli bir şekilde yönetin!</p>
                </div>

                <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-gray-600 mb-2 text-sm" for="email">E-posta</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-200 transition-all"
                               placeholder="ornek@email.com"
                               required>
                    </div>

                    <div>
                        <label class="block text-gray-600 mb-2 text-sm" for="password">Şifre</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-200 transition-all"
                               placeholder="••••••••"
                               required>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                            <span class="ml-2 text-sm text-gray-600">Beni hatırla</span>
                        </label>
                        <a href="#" class="text-sm text-orange-500 hover:text-orange-600">Şifremi Unuttum?</a>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg text-sm">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit"
                            class="w-full bg-red-500 text-white font-semibold py-3 px-4 rounded-lg hover:bg-orange-600 transition-all duration-200">
                        GİRİŞ YAP
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
