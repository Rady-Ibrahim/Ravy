<?php

namespace Modules\Notification\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRecipient extends Model
{
    protected $fillable = [
        'channel',
        'event',
        'address',
        'label',
        'is_active',
        'filters',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'filters' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function matchesContext(array $context): bool
    {
        $filters = $this->filters ?? [];

        if ($filters === []) {
            return true;
        }

        if (isset($filters['source'])) {
            $orderSource = $context['source'] ?? null;
            $allowed = is_array($filters['source']) ? $filters['source'] : [$filters['source']];

            if (! in_array($orderSource, $allowed, true)) {
                return false;
            }
        }

        return true;
    }
}
