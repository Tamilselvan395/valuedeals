<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\EmirateShippingRate;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class BookstoreConfigSeeder extends Seeder
{
    public function run(): void
    {
        if (StoreSetting::query()->doesntExist()) {
            StoreSetting::query()->create([
                'free_shipping_threshold' => 99,
                'default_shipping_rate'   => 15,
                'currency_code'           => 'AED',
                'currency_symbol'         => 'AED',
            ]);
        }

        $emirates = [
            ['slug' => 'abu-dhabi', 'name' => 'Abu Dhabi', 'sort_order' => 1],
            ['slug' => 'dubai', 'name' => 'Dubai', 'sort_order' => 2],
            ['slug' => 'sharjah', 'name' => 'Sharjah', 'sort_order' => 3],
            ['slug' => 'ajman', 'name' => 'Ajman', 'sort_order' => 4],
            ['slug' => 'umm-al-quwain', 'name' => 'Umm Al Quwain', 'sort_order' => 5],
            ['slug' => 'ras-al-khaimah', 'name' => 'Ras Al Khaimah', 'sort_order' => 6],
            ['slug' => 'fujairah', 'name' => 'Fujairah', 'sort_order' => 7],
        ];

        foreach ($emirates as $row) {
            EmirateShippingRate::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name'          => $row['name'],
                    'shipping_rate' => 15,
                    'is_active'     => true,
                    'sort_order'    => $row['sort_order'],
                ]
            );
        }

        Coupon::query()->firstOrCreate(
            ['code' => 'BOOKS10'],
            [
                'description'      => 'Demo: 10% off your order',
                'discount_type'    => Coupon::TYPE_PERCENT,
                'discount_value'   => 10,
                'min_order_amount' => 0,
                'max_uses'         => null,
                'expires_at'       => null,
                'is_active'        => true,
            ]
        );

        $this->command->info('Store settings, UAE emirate rates, and sample coupon BOOKS10 are ready.');
    }
}
