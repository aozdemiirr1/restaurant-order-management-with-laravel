@extends('layouts.admin')

@section('title', 'Müşteri Düzenle')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Müşteri Adı
                </label>
                <input type="text" name="name" required value="{{ $customer->name }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Telefon
                </label>
                <input type="tel" name="phone" value="{{ $customer->phone }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    E-posta
                </label>
                <input type="email" name="email" value="{{ $customer->email }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Adres
                </label>
                <textarea name="address" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $customer->address }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <a href="{{ route('admin.customers.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                İptal
            </a>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
