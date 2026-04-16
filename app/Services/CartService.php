<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\EmirateShippingRate;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_COUPON_ID = 'cart_coupon_id';

    public function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );
        }

        $cartId = Session::get('guest_cart_id');
        if ($cartId) {
            $cart = Cart::find($cartId);
            if ($cart && !$cart->user_id) {
                return $cart;
            }
        }

        $sessionId = Session::getId();

        $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null]
        );

        Session::put('guest_cart_id', $cart->id);

        return $cart;
    }

    public function addItem(int $productId, int $quantity = 1): array
    {
        $product = Product::active()->inStock()->findOrFail($productId);

        if ($quantity > $product->stock) {
            return ['success' => false, 'message' => 'Not enough stock available.'];
        }

        $cart     = $this->getOrCreateCart();
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $quantity;
            if ($newQty > $product->stock) {
                return ['success' => false, 'message' => 'Not enough stock available.'];
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $productId,
                'quantity'   => $quantity,
                'unit_price' => $product->selling_price,
            ]);
        }

        return [
            'success'    => true,
            'message'    => '"' . $product->title . '" added to cart.',
            'cart_count' => $this->getCartCount(),
            'cart_item_id' => $cartItem->id,
            'new_quantity' => $cartItem->quantity,
        ];
    }

    public function updateItem(int $cartItemId, int $quantity): array
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $this->authorizeCartItem($cartItem);

        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        if ($quantity > $cartItem->product->stock) {
            return ['success' => false, 'message' => 'Not enough stock available.'];
        }

        $cartItem->update([
            'quantity'   => $quantity,
            'unit_price' => $cartItem->product->selling_price,
        ]);

        $subtotal = $this->getCartTotal();
        $discount = $this->getCouponDiscount($subtotal);
        $shipping = $this->calculateShipping($subtotal);
        $total    = $subtotal - $discount + $shipping;

        return [
            'success'         => true,
            'message'         => 'Cart updated.',
            'cart_count'      => $this->getCartCount(),
            'item_subtotal'   => number_format($cartItem->fresh()->subtotal, 0),
            'cart_subtotal'   => number_format($subtotal, 0),
            'cart_discount'   => number_format($discount, 0),
            'cart_shipping'   => number_format($shipping, 0),
            'cart_total'      => number_format($total, 0),
            'shipping_raw'    => $shipping,
            'threshold_gap'   => max(0, config('bookstore.free_shipping_threshold') - $subtotal),
            'currency_symbol' => config('bookstore.currency_symbol'),
        ];
    }

    public function removeItem(int $cartItemId): array
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $this->authorizeCartItem($cartItem);
        $cartItem->delete();

        $subtotal = $this->getCartTotal();
        $discount = $this->getCouponDiscount($subtotal);
        $shipping = $this->calculateShipping($subtotal);
        $total    = $subtotal - $discount + $shipping;

        return [
            'success'         => true,
            'message'         => 'Item removed from cart.',
            'cart_count'      => $this->getCartCount(),
            'cart_subtotal'   => number_format($subtotal, 0),
            'cart_discount'   => number_format($discount, 0),
            'cart_shipping'   => number_format($shipping, 0),
            'cart_total'      => number_format($total, 0),
            'shipping_raw'    => $shipping,
            'threshold_gap'   => max(0, config('bookstore.free_shipping_threshold') - $subtotal),
            'currency_symbol' => config('bookstore.currency_symbol'),
        ];
    }

    public function clearCart(): void
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();
        $this->removeCoupon();
    }

    public function getCartCount(): int
    {
        $cart = $this->getOrCreateCart();
        return $cart->items()->sum('quantity');
    }

    public function getCartTotal(): float
    {
        $cart = $this->getOrCreateCart();
        return $cart->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }

    public function getCartWithItems(): Cart
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product.images']);
        return $cart;
    }

    public function mergeGuestCart(int $userId): void
    {
        $guestCartId = Session::get('guest_cart_id');
        $guestCart = null;
        
        if ($guestCartId) {
            $guestCart = Cart::where('id', $guestCartId)->where('user_id', null)->first();
        } else {
            $sessionId = Session::getId();
            $guestCart = Cart::where('session_id', $sessionId)->where('user_id', null)->first();
        }
        $userCart  = Cart::firstOrCreate(['user_id' => $userId]);

        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()->where('product_id', $guestItem->product_id)->first();
            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'quantity'   => $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                ]);
            }
        }

        $guestCart->delete();
        Session::forget('guest_cart_id');
    }

    private function authorizeCartItem(CartItem $cartItem): void
    {
        $cart = $this->getOrCreateCart();
        abort_if((int) $cartItem->cart_id !== (int) $cart->id, 403, 'Unauthorized.');
    }

    public function calculateShipping(float $subtotal, ?string $emirateSlug = null): float
    {
        $threshold = (float) config('bookstore.free_shipping_threshold');
        if ($subtotal >= $threshold) {
            return 0.0;
        }

        if ($emirateSlug) {
            $row = EmirateShippingRate::query()
                ->where('slug', $emirateSlug)
                ->where('is_active', true)
                ->first();
            if ($row) {
                return (float) $row->shipping_rate;
            }
        }

        return (float) config('bookstore.flat_shipping_rate');
    }

    public function getAppliedCoupon(): ?Coupon
    {
        $id = Session::get(self::SESSION_COUPON_ID);
        if (! $id) {
            return null;
        }

        $coupon = Coupon::query()->find($id);
        if (! $coupon || ! $coupon->isCurrentlyValid()) {
            Session::forget(self::SESSION_COUPON_ID);

            return null;
        }

        return $coupon;
    }

    public function getCouponDiscount(float $subtotal): float
    {
        $coupon = $this->getAppliedCoupon();
        if (! $coupon) {
            return 0.0;
        }

        return $coupon->computeDiscount($subtotal);
    }

    /**
     * @return array{success: bool, message: string, code?: string}
     */
    public function applyCouponCode(string $code): array
    {
        $coupon = Coupon::findValidByCode($code);
        if (! $coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $subtotal = $this->getCartTotal();
        if ($subtotal < (float) $coupon->min_order_amount) {
            return [
                'success' => false,
                'message' => 'Minimum order of '.config('bookstore.currency_symbol').number_format((float) $coupon->min_order_amount, 2).' required for this coupon.',
            ];
        }

        Session::put(self::SESSION_COUPON_ID, $coupon->id);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'code'    => $coupon->code,
        ];
    }

    public function removeCoupon(): void
    {
        Session::forget(self::SESSION_COUPON_ID);
    }
}
