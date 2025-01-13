<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address'
    ];

    // Siparişler ilişkisi
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Toplam sipariş sayısı
    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    // Toplam harcama
    public function getTotalSpentAttribute()
    {
        return $this->orders()->where('status', 'delivered')->sum('total_amount');
    }
}
