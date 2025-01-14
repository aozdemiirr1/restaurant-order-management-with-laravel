<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now();
        $yesterday = now()->subDay();
        $lastWeek = now()->subWeek();

        // Debug için bugünkü siparişleri logla
        $todayOrders = Order::today()->get();
        Log::info('Today\'s orders:', $todayOrders->toArray());

        // Daily revenue - tüm siparişleri dahil et (status kontrolünü kaldırdık)
        $daily_revenue = Order::today()
            ->sum('total_amount');

        Log::info('Daily revenue:', ['amount' => $daily_revenue]);

        // Debug için sipariş detaylarını logla
        $orderDetails = Order::today()
            ->with('items')
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'items_total' => $order->items->sum('subtotal'),
                    'status' => $order->status,
                    'created_at' => $order->created_at
                ];
            });
        Log::info('Order details:', $orderDetails->toArray());

        $yesterday_revenue = Order::whereDate('created_at', $yesterday)
            ->sum('total_amount');

        $revenue_change = $yesterday_revenue > 0
            ? round((($daily_revenue - $yesterday_revenue) / $yesterday_revenue) * 100, 1)
            : 0;

        // Daily orders - tüm siparişleri say
        $daily_orders_count = Order::today()->count();

        // Popular items today - en çok sipariş edilen ürünler
        $daily_popular_items = OrderItem::with('menu')
            ->whereHas('order', function($query) {
                $query->today();
            })
            ->select('menu_id', DB::raw('SUM(quantity) as count'))
            ->groupBy('menu_id')
            ->orderByDesc('count')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return (object)[
                    'name' => $item->menu->name,
                    'count' => $item->count
                ];
            });

        // Monthly orders
        $monthly_orders_count = Order::thisMonth()->count();

        // Average daily orders for last week
        $average_daily_orders = Order::whereDate('created_at', '>=', $lastWeek)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->avg('count') ?? 0;

        // Orders change percentage
        $last_week_average = Order::whereDate('created_at', '>=', $lastWeek->copy()->subWeek())
            ->whereDate('created_at', '<', $lastWeek)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->avg('count') ?? 0;

        $orders_change = $last_week_average > 0
            ? round((($average_daily_orders - $last_week_average) / $last_week_average) * 100, 1)
            : 0;

        // Debug bilgisini view'a da gönderelim
        $debug = [
            'today_date' => $today->toDateString(),
            'orders_count' => $todayOrders->count(),
            'raw_daily_revenue' => $daily_revenue,
            'formatted_daily_revenue' => number_format($daily_revenue, 2)
        ];

        return view('admin.dashboard', compact(
            'daily_revenue',
            'revenue_change',
            'daily_orders_count',
            'daily_popular_items',
            'monthly_orders_count',
            'average_daily_orders',
            'orders_change',
            'debug'
        ));
    }
}
