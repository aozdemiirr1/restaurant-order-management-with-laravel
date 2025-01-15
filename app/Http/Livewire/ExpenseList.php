<?php

namespace App\Http\Livewire;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseList extends Component
{
    use WithPagination;

    protected $listeners = ['expenseUpdated' => '$refresh'];

    public function deleteExpense($id)
    {
        $expense = Expense::find($id);
        if ($expense) {
            $expense->delete();
            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => 'Gider başarıyla silindi.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.expense-list', [
            'expenses' => Expense::latest()->paginate(10)
        ]);
    }
}
