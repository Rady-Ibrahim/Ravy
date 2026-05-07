<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Models\User;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'governorate_id',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'source',
        'tracking_number',
        'subtotal',
        'shipping_amount',
        'shipping_calculated_cost',
        'discount_amount',
        'grand_total',
        'currency',
        'shipping_address_snapshot',
        'packaging_option',
        'notes',
        'promo_code_id',
        'promo_code',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'shipping_calculated_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'shipping_address_snapshot' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(\Modules\Payments\Models\PaymentTransaction::class, 'order_id');
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }
}
