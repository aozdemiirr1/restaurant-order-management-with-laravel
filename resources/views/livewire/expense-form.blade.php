<div class="p-6">
    <div class="text-lg font-medium mb-6">
        {{ $expense ? 'Gider Düzenle' : 'Yeni Gider Ekle' }}
    </div>

    <form wire:submit.prevent="save">
        <div class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Başlık</label>
                <input type="text" wire:model.defer="title" id="title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @error('title')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                <select wire:model.defer="category" id="category"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    <option value="">Kategori Seçin</option>
                    <option value="Malzeme">Malzeme Gideri</option>
                    <option value="Personel">Personel Gideri</option>
                    <option value="Kira">Kira Gideri</option>
                    <option value="Fatura">Fatura Gideri</option>
                    <option value="Diğer">Diğer</option>
                </select>
                @error('category')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Tutar (₺)</label>
                <input type="number" step="0.01" wire:model.defer="amount" id="amount"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @error('amount')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="expense_date" class="block text-sm font-medium text-gray-700">Tarih</label>
                <input type="date" wire:model.defer="expense_date" id="expense_date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                @error('expense_date')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
                <textarea wire:model.defer="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                @error('description')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" wire:click="$emit('closeModal')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                İptal
            </button>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                {{ $expense ? 'Güncelle' : 'Kaydet' }}
            </button>
        </div>
    </form>
</div>
