<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_amount',
        'max_discount_amount',
        'max_uses',
        'used_count',
        'is_active',
        'expires_at',
        'starts_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'starts_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeNotUsedUp($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_uses')
              ->orWhere('used_count', '<', 'max_uses');
        });
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->min_amount && $subtotal < $this->min_amount) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? $subtotal * ($this->discount_value / 100)
            : $this->discount_value;

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $subtotal);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
