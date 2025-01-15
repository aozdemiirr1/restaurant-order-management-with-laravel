<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));

        $revenue = match ($period) {
            'daily' => $this->getDailyRevenue($date),
            'weekly' => $this->getWeeklyRevenue($date),
            'monthly' => $this->getMonthlyRevenue($date),
            default => $this->getDailyRevenue($date),
        };

        $expenses = match ($period) {
            'daily' => $this->getDailyExpenses($date),
            'weekly' => $this->getWeeklyExpenses($date),
            'monthly' => $this->getMonthlyExpenses($date),
            default => $this->getDailyExpenses($date),
        };

        return view('admin.revenue.index', compact('revenue', 'expenses', 'period', 'date'));
    }

    private function getDailyRevenue($date)
    {
        return Order::query()
            ->whereDate('created_at', $date)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status != \'cancelled\' THEN total_amount ELSE 0 END) as total_revenue'),
                DB::raw('COUNT(CASE WHEN status = \'cancelled\' THEN 1 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN status = \'cancelled\' THEN total_amount ELSE 0 END) as cancelled_amount')
            )
            ->groupBy('date')
            ->first();
    }

    private function getDailyExpenses($date)
    {
        return Expense::query()
            ->whereDate('expense_date', $date)
            ->select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('COUNT(*) as total_expenses'),
                DB::raw('SUM(amount) as total_expense_amount'),
                DB::raw('SUM(CASE WHEN category = \'Malzeme\' THEN amount ELSE 0 END) as material_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Personel\' THEN amount ELSE 0 END) as personnel_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Kira\' THEN amount ELSE 0 END) as rent_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Fatura\' THEN amount ELSE 0 END) as utility_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Diğer\' THEN amount ELSE 0 END) as other_expenses')
            )
            ->groupBy('date')
            ->first();
    }

    private function getWeeklyRevenue($date)
    {
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();

        return Order::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status != \'cancelled\' THEN total_amount ELSE 0 END) as total_revenue'),
                DB::raw('COUNT(CASE WHEN status = \'cancelled\' THEN 1 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN status = \'cancelled\' THEN total_amount ELSE 0 END) as cancelled_amount')
            )
            ->groupBy('date')
            ->get();
    }

    private function getWeeklyExpenses($date)
    {
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();

        return Expense::query()
            ->whereBetween('expense_date', [$startOfWeek, $endOfWeek])
            ->select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('COUNT(*) as total_expenses'),
                DB::raw('SUM(amount) as total_expense_amount'),
                DB::raw('SUM(CASE WHEN category = \'Malzeme\' THEN amount ELSE 0 END) as material_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Personel\' THEN amount ELSE 0 END) as personnel_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Kira\' THEN amount ELSE 0 END) as rent_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Fatura\' THEN amount ELSE 0 END) as utility_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Diğer\' THEN amount ELSE 0 END) as other_expenses')
            )
            ->groupBy('date')
            ->get();
    }

    private function getMonthlyRevenue($date)
    {
        $startOfMonth = Carbon::parse($date)->startOfMonth();
        $endOfMonth = Carbon::parse($date)->endOfMonth();

        return Order::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status != \'cancelled\' THEN total_amount ELSE 0 END) as total_revenue'),
                DB::raw('COUNT(CASE WHEN status = \'cancelled\' THEN 1 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN status = \'cancelled\' THEN total_amount ELSE 0 END) as cancelled_amount')
            )
            ->groupBy('date')
            ->get();
    }

    private function getMonthlyExpenses($date)
    {
        $startOfMonth = Carbon::parse($date)->startOfMonth();
        $endOfMonth = Carbon::parse($date)->endOfMonth();

        return Expense::query()
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('COUNT(*) as total_expenses'),
                DB::raw('SUM(amount) as total_expense_amount'),
                DB::raw('SUM(CASE WHEN category = \'Malzeme\' THEN amount ELSE 0 END) as material_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Personel\' THEN amount ELSE 0 END) as personnel_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Kira\' THEN amount ELSE 0 END) as rent_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Fatura\' THEN amount ELSE 0 END) as utility_expenses'),
                DB::raw('SUM(CASE WHEN category = \'Diğer\' THEN amount ELSE 0 END) as other_expenses')
            )
            ->groupBy('date')
            ->get();
    }
}
