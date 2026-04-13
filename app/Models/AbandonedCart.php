<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'session_id', 'email', 'cart_data',
        'cart_total', 'item_count', 'last_activity_at',
    ];

    protected $casts = [
        'cart_data'        => 'array',
        'cart_total'       => 'decimal:2',
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
