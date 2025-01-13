<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Sipariş ilişkisi
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Menü ilişkisi
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Ara toplam hesaplama
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            $orderItem->subtotal = $orderItem->quantity * $orderItem->unit_price;
        });

        static::updating(function ($orderItem) {
            $orderItem->subtotal = $orderItem->quantity * $orderItem->unit_price;
        });
    }
}
