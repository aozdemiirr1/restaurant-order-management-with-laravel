<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_available',
        'category',
        'size',
        'stock'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'stock' => 'integer'
    ];

    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'döner' => 'Döner',
            'içecek' => 'İçecek',
            'tatlı' => 'Tatlı',
            default => $this->category
        };
    }
}
