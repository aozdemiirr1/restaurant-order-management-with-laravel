<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        return view('admin.revenue.index', compact('revenue', 'period', 'date'));
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
                DB::raw('SUM(CASE WHEN status = \'cancelled\' THEN total_amount ELSE 0 END) as cancelled_amount'),
                DB::raw('COUNT(CASE WHEN archived_at IS NOT NULL THEN 1 END) as archived_orders'),
                DB::raw('SUM(CASE WHEN archived_at IS NOT NULL AND status != \'cancelled\' THEN total_amount ELSE 0 END) as archived_revenue')
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
                DB::raw('SUM(CASE WHEN status = \'cancelled\' THEN total_amount ELSE 0 END) as cancelled_amount'),
                DB::raw('COUNT(CASE WHEN archived_at IS NOT NULL THEN 1 END) as archived_orders'),
                DB::raw('SUM(CASE WHEN archived_at IS NOT NULL AND status != \'cancelled\' THEN total_amount ELSE 0 END) as archived_revenue')
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
                DB::raw('SUM(CASE WHEN status = \'cancelled\' THEN total_amount ELSE 0 END) as cancelled_amount'),
                DB::raw('COUNT(CASE WHEN archived_at IS NOT NULL THEN 1 END) as archived_orders'),
                DB::raw('SUM(CASE WHEN archived_at IS NOT NULL AND status != \'cancelled\' THEN total_amount ELSE 0 END) as archived_revenue')
            )
            ->groupBy('date')
            ->get();
    }
}
