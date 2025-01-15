<?php

namespace App\Http\Livewire;

use App\Models\Expense;
use LivewireUI\Modal\ModalComponent;

class ExpenseForm extends ModalComponent
{
    public $expense;
    public $title;
    public $description;
    public $amount;
    public $expense_date;
    public $category;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'amount' => 'required|numeric|min:0',
        'expense_date' => 'required|date',
        'category' => 'required|string|max:255',
    ];

    public function mount($expense = null)
    {
        if ($expense) {
            $this->expense = Expense::find($expense);
            $this->title = $this->expense->title;
            $this->description = $this->expense->description;
            $this->amount = $this->expense->amount;
            $this->expense_date = $this->expense->expense_date->format('Y-m-d');
            $this->category = $this->expense->category;
        } else {
            $this->expense_date = now()->format('Y-m-d');
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->expense) {
            $this->expense->update([
                'title' => $this->title,
                'description' => $this->description,
                'amount' => $this->amount,
                'expense_date' => $this->expense_date,
                'category' => $this->category,
            ]);
            $message = 'Gider başarıyla güncellendi.';
        } else {
            Expense::create([
                'title' => $this->title,
                'description' => $this->description,
                'amount' => $this->amount,
                'expense_date' => $this->expense_date,
                'category' => $this->category,
            ]);
            $message = 'Gider başarıyla eklendi.';
        }

        $this->closeModalWithEvents([
            'expenseUpdated' => true,
            'notify' => [
                'type' => 'success',
                'message' => $message
            ]
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'md';
    }

    public function render()
    {
        return view('livewire.expense-form');
    }
}
