<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Models\User;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForGuest($query, $guestId)
    {
        return $query->where('guest_id', $guestId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isGuest(): bool
    {
        return $this->guest_id !== null;
    }

    public function isUser(): bool
    {
        return $this->user_id !== null;
    }
}
