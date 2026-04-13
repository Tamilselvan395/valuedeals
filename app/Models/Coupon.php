<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Coupon extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_uses',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value'    => 'decimal:2',
            'min_order_amount'  => 'decimal:2',
            'expires_at'        => 'datetime',
            'is_active'         => 'boolean',
        ];
    }

    public static function findValidByCode(string $code): ?self
    {
        $coupon = static::query()
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))])
            ->first();

        if (! $coupon || ! $coupon->isCurrentlyValid()) {
            return null;
        }

        return $coupon;
    }

    public static function generateUniqueCode(int $length = 10): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }

    public function isCurrentlyValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function computeDiscount(float $subtotal): float
    {
        if ($subtotal < (float) $this->min_order_amount) {
            return 0.0;
        }

        if ($this->discount_type === self::TYPE_PERCENT) {
            return round(min($subtotal, $subtotal * ((float) $this->discount_value / 100)), 2);
        }

        return round(min((float) $this->discount_value, $subtotal), 2);
    }
}
