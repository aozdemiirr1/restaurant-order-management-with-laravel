<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total_amount',
        'status',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
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
}
