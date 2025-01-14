@extends('layouts.admin')

@section('title', 'Yeni Sipariş')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Müşteri Seçimi -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Müşteri Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Müşteri Seçin</label>
                    <select name="customer_id" id="customer_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Müşteri Seçin</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Sipariş Notu</label>
                    <textarea name="notes" id="notes" rows="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Ürün Seçimi -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-800">Sipariş Öğeleri</h3>
                <button type="button" onclick="addOrderItem()" class="text-sm bg-primary-500 text-white px-4 py-2 rounded-lg hover:bg-primary-600 transition-colors">
                    Ürün Ekle
                </button>
            </div>

            <div id="order-items" class="space-y-4">
                <!-- Dinamik olarak eklenecek ürünler -->
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                İptal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-500 rounded-lg hover:bg-primary-600">
                Siparişi Oluştur
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemCount = 0;

function addOrderItem() {
    const itemsContainer = document.getElementById('order-items');
    const itemTemplate = `
        <div class="grid grid-cols-12 gap-4 items-start border-b border-gray-200 pb-4">
            <div class="col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ürün</label>
                <select name="items[${itemCount}][menu_id]" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Ürün Seçin</option>
                    @foreach($menus as $menu)
                        <option value="{{ $menu->id }}" data-price="{{ $menu->price }}">{{ $menu->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Adet</label>
                <input type="number" name="items[${itemCount}][quantity]" min="1" value="1"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Birim Fiyat</label>
                <input type="text" name="items[${itemCount}][unit_price]" readonly
                    class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm">
            </div>

            <div class="col-span-1 pt-7">
                <button type="button" onclick="this.closest('.grid').remove()"
                    class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    const tempContainer = document.createElement('div');
    tempContainer.innerHTML = itemTemplate;
    itemsContainer.appendChild(tempContainer.firstElementChild);

    // Ürün seçildiğinde fiyatı otomatik doldur
    const select = itemsContainer.querySelector(`select[name="items[${itemCount}][menu_id]"]`);
    const priceInput = itemsContainer.querySelector(`input[name="items[${itemCount}][unit_price]"]`);

    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.dataset.price || '';
        priceInput.value = price;
    });

    itemCount++;
}

// Sayfa yüklendiğinde bir ürün satırı ekle
document.addEventListener('DOMContentLoaded', function() {
    addOrderItem();
});
</script>
@endpush
@endsection
