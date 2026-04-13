<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmirateShippingRate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'shipping_rate',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'shipping_rate' => 'decimal:2',
            'is_active'     => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
