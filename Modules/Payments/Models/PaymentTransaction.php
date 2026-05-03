<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'method',
        'status',
        'amount',
        'currency',
        'provider_transaction_id',
        'provider_order_id',
        'request_payload',
        'response_payload',
        'idempotency_key'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array'
    ];

    /**
     * Get the order that owns the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * Scope a query to only include transactions with a specific status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include transactions from a specific provider.
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Check if transaction is completed successfully.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is still pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark transaction as completed.
     */
    public function markAsCompleted(array $additionalData = []): bool
    {
        return $this->update(array_merge([
            'status' => 'completed'
        ], $additionalData));
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(array $additionalData = []): bool
    {
        return $this->update(array_merge([
            'status' => 'failed'
        ], $additionalData));
    }

    /**
     * Get human-readable status.
     */
    public function getHumanStatusAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => 'Unknown'
        };
    }

    /**
     * Get provider name in human-readable format.
     */
    public function getHumanProviderAttribute(): string
    {
        return match($this->provider) {
            'cod' => 'Cash on Delivery',
            'paymob' => 'Paymob',
            'stripe' => 'Stripe',
            default => ucfirst($this->provider)
        };
    }
}
