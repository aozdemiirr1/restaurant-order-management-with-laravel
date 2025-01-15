@extends('layouts.admin')

@section('title', 'Giderler')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Giderler</h2>
            <button onclick="Modals.show('expense-modal')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Yeni Gider Ekle</span>
            </button>
        </div>
    </div>

    <div class="p-4">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Başlık</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Kategori</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600">Tutar</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600">Tarih</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50/40">
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $expense->title }}</div>
                            @if($expense->description)
                            <div class="text-xs text-gray-500">{{ Str::limit($expense->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium
                                @if($expense->category === 'Malzeme') bg-blue-100 text-blue-800
                                @elseif($expense->category === 'Personel') bg-green-100 text-green-800
                                @elseif($expense->category === 'Kira') bg-purple-100 text-purple-800
                                @elseif($expense->category === 'Fatura') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="text-sm font-medium text-gray-900">₺{{ number_format($expense->amount, 2, ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="text-sm text-gray-900">{{ $expense->expense_date->format('d.m.Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <button onclick="editExpense({{ $expense->id }})" class="text-blue-400 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 rounded px-2 py-1 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-800 bg-red-100 hover:bg-red-200 rounded px-2 py-1 transition-colors" onclick="return confirm('Bu gideri silmek istediğinizden emin misiniz?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Henüz gider kaydı bulunmamaktadır.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    </div>
</div>

<!-- Modal -->
<div id="expense-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" style="display: none;">
    <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-4 mb-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Yeni Gider Ekle</h3>
            <button onclick="Modals.hide('expense-modal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="expense-form" method="POST" action="{{ route('admin.expenses.store') }}">
            @csrf
            <div id="method-update"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="title" class="block text-sm font-medium text-gray-700">Başlık</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-tag text-gray-400"></i>
                        </div>
                        <input type="text" name="title" id="title"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Gider başlığı" required>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-folder text-gray-400"></i>
                        </div>
                        <select name="category" id="category"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                            <option value="">Kategori Seçin</option>
                            <option value="Malzeme">Malzeme Gideri</option>
                            <option value="Personel">Personel Gideri</option>
                            <option value="Kira">Kira Gideri</option>
                            <option value="Fatura">Fatura Gideri</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Tutar (₺)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lira-sign text-gray-400"></i>
                        </div>
                        <input type="number" step="0.01" name="amount" id="amount"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="0.00" required>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="expense_date" class="block text-sm font-medium text-gray-700">Tarih</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <input type="date" name="expense_date" id="expense_date"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-1">
                    <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute top-3 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-align-left text-gray-400"></i>
                        </div>
                        <textarea name="description" id="description" rows="3"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Gider açıklaması..."></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-x-3">
                <button type="button" onclick="Modals.hide('expense-modal')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    İptal
                </button>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <span id="submit-button-text">Kaydet</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const Modals = {
    show: function(modalId, shouldReset = true) {
        document.getElementById(modalId).style.display = 'block';
        document.body.classList.add('overflow-hidden');
        if (shouldReset) {
            resetForm();
        }
    },
    hide: function(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.classList.remove('overflow-hidden');
        resetForm();
    }
};

function resetForm() {
    document.getElementById('expense-form').reset();
    document.getElementById('method-update').innerHTML = '';
    document.getElementById('modal-title').textContent = 'Yeni Gider Ekle';
    document.getElementById('submit-button-text').textContent = 'Kaydet';
    document.getElementById('expense-form').action = "{{ route('admin.expenses.store') }}";
    document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
}

function editExpense(id) {
    fetch(`/admin/expenses/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modal-title').textContent = 'Gider Düzenle';
            document.getElementById('submit-button-text').textContent = 'Güncelle';
            document.getElementById('method-update').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('expense-form').action = `/admin/expenses/${id}`;

            // Form verilerini doldur
            document.getElementById('title').value = data.title;
            document.getElementById('category').value = data.category;
            document.getElementById('amount').value = data.amount;
            document.getElementById('expense_date').value = data.expense_date;
            document.getElementById('description').value = data.description || '';

            // Modalı aç (resetForm çağırmadan)
            Modals.show('expense-modal', false);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gider bilgileri yüklenirken bir hata oluştu.');
        });
}

document.getElementById('expense-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        Modals.hide('expense-modal');
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        Modals.hide('expense-modal');
    }
});
</script>
@endpush
@endsection
