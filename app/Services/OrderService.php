<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function placeOrder(array $data): Order
    {
        $cart = $this->cartService->getCartWithItems();
        $paymentMethod = $data['payment_method'] ?? 'cod';

        abort_if($cart->items->isEmpty(), 422, 'Your cart is empty.');

        foreach ($cart->items as $item) {
            abort_if(
                $item->product->stock < $item->quantity,
                422,
                "'{$item->product->title}' only has {$item->product->stock} copies left."
            );
        }

        $order = DB::transaction(function () use ($data, $cart, $paymentMethod) {
            $subtotal = $this->cartService->getCartTotal();
            $coupon   = $this->cartService->getAppliedCoupon();
            $discount = round(min($this->cartService->getCouponDiscount($subtotal), $subtotal), 2);

            $emirateSlug = !empty($data['shipping_state'])
                ? (string) $data['shipping_state']
                : null;

            $shippingCost = $this->cartService->calculateShipping($subtotal, $emirateSlug);
            $total        = round($subtotal - $discount + $shippingCost, 2);

            $order = Order::create([
                'user_id'          => auth()->id(),
                'order_number'     => Order::generateOrderNumber(),
                'status'           => Order::STATUS_PENDING,
                'payment_method'   => $paymentMethod,
                'payment_status'   => 'unpaid',
                'coupon_code'      => $coupon?->code,
                'discount_amount'  => $discount,
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'total'            => $total,
                'shipping_name'    => $data['shipping_name'] ?? null,
                'shipping_phone'   => $data['shipping_phone'] ?? null,
                'shipping_email'   => $data['shipping_email'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
                'shipping_city'    => $data['shipping_city'] ?? null,
                'shipping_state'   => $data['shipping_state'] ?? null,
                'shipping_pincode' => $data['shipping_pincode'] ?? null,
                'shipping_country' => $data['shipping_country'] ?? 'UAE',
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'product_title' => $item->product->title,
                    'unit_price'    => $item->unit_price,
                    'quantity'      => $item->quantity,
                    'subtotal'      => $item->unit_price * $item->quantity,
                ]);
            }

            if ($coupon instanceof Coupon) {
                $coupon->increment('uses_count');
            }

            if ($paymentMethod === 'cod' || $paymentMethod === 'stripe') {
                $this->finalizeOrderInventoryAndCart($order);
            }

            return $order;
        });

        $this->cartService->removeCoupon();

        return $order;
    }

    public function markStripeOrderPaid(Order $order, string $sessionId, ?string $paymentIntentId = null): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        DB::transaction(function () use ($order, $sessionId, $paymentIntentId) {
            $order->update([
                'payment_status'           => 'paid',
                'stripe_session_id'        => $sessionId,
                'stripe_payment_intent_id' => $paymentIntentId,
            ]);

            $this->finalizeOrderInventoryAndCart($order);
        });
    }

    protected function finalizeOrderInventoryAndCart(Order $order): void
    {
        foreach ($order->items as $item) {
            abort_if(
                $item->product->stock < $item->quantity,
                422,
                "'{$item->product->title}' is out of stock now. Please try again."
            );
            $item->product->decrement('stock', $item->quantity);
        }

        $this->cartService->clearCart();
    }

    public function cancelOrder(Order $order): bool
    {
        if (! $order->isCancellable()) {
            return false;
        }

        DB::transaction(function () use ($order) {
            if ($order->payment_method === 'cod' || $order->payment_status === 'paid') {
                foreach ($order->items as $item) {
                    $item->product?->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);
        });

        return true;
    }
}
