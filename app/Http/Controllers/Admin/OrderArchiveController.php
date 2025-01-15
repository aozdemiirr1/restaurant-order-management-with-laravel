<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::whereNotNull('archived_at')
            ->with('customer')
            ->orderBy('archived_at', 'desc');

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('archived_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('archived_at', '<=', $request->date_to);
        }

        $archivedOrders = $query->paginate(10)->withQueryString();
        $customers = Customer::all(); // Müşteri filtrelemesi için müşteri listesi

        return view('admin.orders.archive', compact('archivedOrders', 'customers'));
    }

    public function show($id)
    {
        $order = Order::whereNotNull('archived_at')
            ->with(['customer', 'items.menu'])
            ->findOrFail($id);

        $orderData = [
            'id' => $order->id,
            'status' => $order->status,
            'total_amount' => (float) $order->total_amount,
            'notes' => $order->notes,
            'created_at' => $order->created_at->format('d.m.Y H:i'),
            'archived_at' => $order->archived_at->format('d.m.Y H:i'),
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
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) ($item->quantity * $item->unit_price),
                ];
            }),
        ];

        return response()->json($orderData);
    }

    public function destroy($id)
    {
        $order = Order::whereNotNull('archived_at')->findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.archive')
            ->with('success', 'Sipariş başarıyla silindi.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:orders,id'
        ]);

        try {
            DB::beginTransaction();
            Order::whereNotNull('archived_at')
                ->whereIn('id', $validated['ids'])
                ->delete();
            DB::commit();

            return redirect()->route('admin.orders.archive')
                ->with('success', count($validated['ids']) . ' adet sipariş başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Siparişler silinirken bir hata oluştu.');
        }
    }
}
