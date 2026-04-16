<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'payment_method', 'payment_status',
        'stripe_session_id', 'stripe_payment_intent_id',
        'subtotal', 'shipping_cost', 'total',
        'coupon_code', 'discount_amount',
        'shipping_name', 'shipping_phone', 'shipping_email',
        'shipping_address', 'shipping_city', 'shipping_state',
        'shipping_pincode', 'shipping_country', 'notes',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'shipping_cost'    => 'decimal:2',
        'total'            => 'decimal:2',
        'discount_amount'  => 'decimal:2',
    ];

    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique order number in the format: VD-YYYYMMDD-XXX
     *
     * - VD        = ValueDeals prefix
     * - YYYYMMDD  = today's date
     * - XXX       = daily running counter (001, 002, 003…), resets every day
     *
     * Must be called inside a DB transaction — lockForUpdate() prevents
     * duplicate numbers under concurrent requests.
     */
    public static function generateOrderNumber(): string
    {
        $today  = now()->format('Ymd');
        $prefix = "VD-{$today}-";

        // Lock the latest today's order so concurrent requests queue up
        $last = static::query()
            ->where('order_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('order_number')
            ->value('order_number');

        $sequence = $last
            ? (int) substr($last, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'processing' => 'info',
            'shipped'    => 'primary',
            'delivered'  => 'success',
            'cancelled'  => 'danger',
            default      => 'secondary',
        };
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'stripe' => 'Stripe',
            default => 'Cash on Delivery',
        };
    }
}
