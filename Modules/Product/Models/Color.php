<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'name',
        'code',
        'hex',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
