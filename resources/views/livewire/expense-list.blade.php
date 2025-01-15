<div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Başlık</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Kategori</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tarih</th>
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($expense->category === 'Malzeme') bg-blue-100 text-blue-800
                            @elseif($expense->category === 'Personel') bg-green-100 text-green-800
                            @elseif($expense->category === 'Kira') bg-purple-100 text-purple-800
                            @elseif($expense->category === 'Fatura') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $expense->category }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="text-sm font-medium text-gray-900">₺{{ number_format($expense->amount, 2, ',', '.') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900">{{ $expense->expense_date->format('d.m.Y') }}</div>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button wire:click="$emit('openModal', 'expense-form', {{ json_encode(['expense' => $expense->id]) }})"
                                class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="deleteExpense({{ $expense->id }})"
                                onclick="return confirm('Bu gideri silmek istediğinizden emin misiniz?')"
                                class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
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
