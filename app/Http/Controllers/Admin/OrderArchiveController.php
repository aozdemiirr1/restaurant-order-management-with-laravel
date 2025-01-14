<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class OrderArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::onlyTrashed()
            ->with('customer')
            ->orderBy('deleted_at', 'desc');

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('deleted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('deleted_at', '<=', $request->date_to);
        }

        $archivedOrders = $query->paginate(10)->withQueryString();
        $customers = Customer::all(); // Müşteri filtrelemesi için müşteri listesi

        return view('admin.orders.archive', compact('archivedOrders', 'customers'));
    }

    public function show($id)
    {
        $order = Order::onlyTrashed()
            ->with(['customer', 'items.menu'])
            ->findOrFail($id);

        return response()->json([
            'id' => $order->id,
            'customer' => $order->customer,
            'items' => $order->items->map(function ($item) {
                return [
                    'menu_id' => $item->menu_id,
                    'menu_name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) ($item->quantity * $item->unit_price),
                ];
            }),
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'notes' => $order->notes,
            'created_at' => $order->created_at->format('d.m.Y H:i'),
            'deleted_at' => $order->deleted_at->format('d.m.Y H:i'),
        ]);
    }
}
