<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Models\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class StoreAbandonedCarts extends Command
{
    protected $signature   = 'bookstore:store-abandoned-carts';
    protected $description = 'Detect and store carts inactive for more than 1 hour';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subHour();

        $inactiveCarts = Cart::where('updated_at', '<', $cutoff)
            ->whereHas('items')
            ->with(['items.product', 'user'])
            ->get();

        $stored = 0;

        foreach ($inactiveCarts as $cart) {
            $cartData = $cart->items->map(fn ($item) => [
                'product_id'    => $item->product_id,
                'product_title' => $item->product?->title,
                'quantity'      => $item->quantity,
                'unit_price'    => $item->unit_price,
                'subtotal'      => $item->subtotal,
                'cover_image'   => $item->product?->cover_image,
            ])->toArray();

            $total     = array_sum(array_column($cartData, 'subtotal'));
            $itemCount = array_sum(array_column($cartData, 'quantity'));

            AbandonedCart::updateOrCreate(
                ['user_id' => $cart->user_id, 'session_id' => $cart->session_id],
                [
                    'email'            => $cart->user?->email,
                    'cart_data'        => $cartData,
                    'cart_total'       => $total,
                    'item_count'       => $itemCount,
                    'last_activity_at' => $cart->updated_at,
                ]
            );

            $stored++;
        }

        $this->info("Stored {$stored} abandoned cart(s).");

        return Command::SUCCESS;
    }
}
