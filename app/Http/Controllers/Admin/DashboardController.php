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
            ->where('status', 'delivered')
            ->sum('total_amount');

        Log::info('Daily revenue:', ['amount' => $daily_revenue]);

        // Debug için sipariş detaylarını logla
        $orderDetails = Order::today()
            ->where('status', 'delivered')
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
            ->where('status', 'delivered')
            ->sum('total_amount');

        $revenue_change = $yesterday_revenue > 0
            ? round((($daily_revenue - $yesterday_revenue) / $yesterday_revenue) * 100, 1)
            : 0;

        // Daily orders - tüm siparişleri say
        $daily_orders_count = Order::today()
            ->where('status', 'delivered')
            ->count();

        $daily_cancelled_orders = Order::today()
            ->where('status', 'cancelled')
            ->count();

        // Popular items today - en çok sipariş edilen ürünler (kategorilere göre gruplandırılmış)
        $daily_popular_items = OrderItem::with(['menu.category'])
            ->whereHas('order', function($query) {
                $query->today()->where('status', 'delivered');
            })
            ->select('menu_id', DB::raw('SUM(quantity) as count'))
            ->groupBy('menu_id')
            ->orderByDesc('count')
            ->get()
            ->groupBy(function($item) {
                return $item->menu->category->name ?? 'Diğer';
            })
            ->map(function($items) {
                return $items->map(function($item) {
                    return (object)[
                        'name' => $item->menu->name,
                        'count' => $item->count,
                        'category' => $item->menu->category->name ?? 'Diğer'
                    ];
                });
            });

        // Monthly orders
        $monthly_orders_count = Order::thisMonth()
            ->where('status', 'delivered')
            ->count();

        $monthly_cancelled_orders = Order::thisMonth()
            ->where('status', 'cancelled')
            ->count();

        // Average daily orders for last week
        $average_daily_orders = Order::whereDate('created_at', '>=', $lastWeek)
            ->where('status', 'delivered')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->avg('count') ?? 0;

        // Orders change percentage
        $last_week_average = Order::whereDate('created_at', '>=', $lastWeek->copy()->subWeek())
            ->whereDate('created_at', '<', $lastWeek)
            ->where('status', 'delivered')
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
            'orders_count' => Order::today()->where('status', 'delivered')->count(),
            'cancelled_orders_count' => Order::today()->where('status', 'cancelled')->count(),
            'raw_daily_revenue' => $daily_revenue,
            'formatted_daily_revenue' => number_format($daily_revenue, 2)
        ];

        // Get sales data for the last 7 days
        $sales_data = Order::whereDate('created_at', '>=', now()->subDays(7))
            ->where('status', 'delivered')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($sale) {
                return [
                    'date' => date('d M', strtotime($sale->date)),
                    'total' => round($sale->total, 2)
                ];
            });

        return view('admin.dashboard', compact(
            'daily_revenue',
            'revenue_change',
            'daily_orders_count',
            'daily_cancelled_orders',
            'daily_popular_items',
            'monthly_orders_count',
            'monthly_cancelled_orders',
            'average_daily_orders',
            'orders_change',
            'debug',
            'sales_data'
        ));
    }
}
