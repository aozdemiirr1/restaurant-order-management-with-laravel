<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total_amount',
        'status',
        'notes',
        'archived_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'archived_at' => 'datetime'
    ];

    // Müşteri ilişkisi
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Sipariş detayları ilişkisi
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Sipariş durumu için kullanılabilir değerler
    public static function getStatusOptions()
    {
        return [
            'preparing' => 'Hazırlanıyor',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal Edildi'
        ];
    }

    // Sipariş durumunun Türkçe karşılığı
    public function getStatusTextAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    // Toplam tutarı otomatik hesaplama
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->total_amount)) {
                $order->calculateTotalAmount();
            }
        });

        static::updating(function ($order) {
            $order->calculateTotalAmount();
        });
    }

    // Toplam tutarı hesaplama metodu
    public function calculateTotalAmount()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->quantity * $item->unit_price;
        }
        $this->total_amount = $total;
        return $this->total_amount;
    }

    // Bugünün siparişleri için scope
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    // Bu ayki siparişler için scope
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }
}
