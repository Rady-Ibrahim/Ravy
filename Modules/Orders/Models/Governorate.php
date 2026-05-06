<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'shipping_cost',
        'is_active',
        'delivery_days',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the orders for this governorate.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get active governorates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get governorates ordered by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
