<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.menu']);

        // Müşteri filtresi
        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tarih aralığı filtresi
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Tutar aralığı filtresi
        if ($request->filled('min_amount')) {
            $query->where('total_amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        // Arama filtresi (Sipariş ID veya müşteri bilgileri)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('customer', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Sıralama
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'amount_asc':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $orders = $query->paginate(10)->withQueryString();
        $customers = Customer::all();
        $menus = Menu::where('is_available', true)->get();

        return view('admin.orders.index', compact('orders', 'customers', 'menus'));
    }

    public function create()
    {
        $customers = Customer::all();
        $menus = Menu::where('is_available', true)->get();
        return view('admin.orders.create', compact('customers', 'menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'menu_ids' => 'required|array|min:1',
            'menu_ids.*' => 'required|exists:menus,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Sipariş oluştur
            $order = new Order();
            $order->customer_id = $validated['customer_id'];
            $order->notes = $validated['notes'];
            $order->total_amount = 0;
            $order->save();

            $total = 0;

            // Sipariş detaylarını ekle
            foreach ($validated['menu_ids'] as $index => $menuId) {
                $menu = Menu::findOrFail($menuId);
                $quantity = $validated['quantities'][$index];

                $orderItem = new OrderItem([
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'unit_price' => $menu->price
                ]);

                $order->items()->save($orderItem);
                $total += $orderItem->subtotal;
            }

            // Toplam tutarı güncelle
            $order->total_amount = $total;
            $order->save();

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Sipariş başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Sipariş oluşturulurken bir hata oluştu.');
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.menu']);

        $orderData = [
            'id' => $order->id,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'notes' => $order->notes,
            'created_at' => $order->created_at->format('d.m.Y H:i'),
            'customer' => [
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
                'email' => $order->customer->email,
                'address' => $order->customer->address,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'menu_id' => $item->menu_id,
                    'menu_name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price,
                ];
            }),
        ];

        return response()->json($orderData);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:preparing,delivered,cancelled'
        ]);

        $order->status = $validated['status'];
        $order->save();

        return redirect()->back()
            ->with('success', 'Sipariş durumu güncellendi.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Sipariş başarıyla silindi.');
    }
}
