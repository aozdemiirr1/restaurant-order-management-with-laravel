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
    public function index()
    {
        $orders = Order::with(['customer', 'items.menu'])
            ->latest()
            ->paginate(10);
        return view('admin.orders.index', compact('orders'));
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
        return view('admin.orders.show', compact('order'));
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
