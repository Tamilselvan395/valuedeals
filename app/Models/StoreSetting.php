<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class StoreSetting extends Model
{
    protected static function booted(): void
    {
        static::saved(function (): void {
            static::loadIntoConfig();
        });
    }

    protected $fillable = [
        'free_shipping_threshold',
        'default_shipping_rate',
        'currency_code',
        'currency_symbol',
        'logo_path',
        'favicon_path',
        'mobile_number',
        'address',
        'map_embed',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'seo_title',
        'seo_og_image',
        'seo_twitter_image',
    ];

    protected function casts(): array
    {
        return [
            'free_shipping_threshold' => 'decimal:2',
            'default_shipping_rate'   => 'decimal:2',
        ];
    }

    public static function current(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::query()->create([
            'free_shipping_threshold' => config('bookstore.free_shipping_threshold', 99),
            'default_shipping_rate'   => config('bookstore.flat_shipping_rate', 15),
            'currency_code'           => config('bookstore.currency_code', 'AED'),
            'currency_symbol'         => config('bookstore.currency_symbol', 'AED'),
        ]);
    }

    public static function loadIntoConfig(): void
    {
        if (! Schema::hasTable('store_settings')) {
            return;
        }

        $s = static::query()->first();
        if (! $s) {
            return;
        }

        config([
            'bookstore.free_shipping_threshold' => (float) $s->free_shipping_threshold,
            'bookstore.flat_shipping_rate'      => (float) $s->default_shipping_rate,
            'bookstore.currency_symbol'         => $s->currency_symbol,
            'bookstore.currency_code'           => strtoupper((string) $s->currency_code),
        ]);

        \Illuminate\Support\Facades\View::share('storeSettings', $s);
    }
}
