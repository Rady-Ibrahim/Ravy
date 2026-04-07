<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryAttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CategoryAttribute::class, 'attribute_id');
    }
}
