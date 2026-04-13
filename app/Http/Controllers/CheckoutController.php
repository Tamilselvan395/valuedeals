<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\EmirateShippingRate;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Stripe\StripeClient;

use Illuminate\Support\Facades\Gate;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService  $cartService,
        private readonly OrderService $orderService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCartWithItems();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $this->cartService->getCartTotal();
        $discount = $this->cartService->getCouponDiscount($subtotal);
        $emirates = EmirateShippingRate::query()->active()->get();
        $defaultEmirate = $emirates->first()?->slug;
        $emirateSlug    = old('shipping_state', $defaultEmirate);
        $shippingCost   = $this->cartService->calculateShipping($subtotal, $emirateSlug ? (string) $emirateSlug : null);
        $total        = round($subtotal - $discount + $shippingCost, 2);
        $user          = auth()->user();
        $appliedCoupon = $this->cartService->getAppliedCoupon();

        return view('checkout.index', compact('cart', 'subtotal', 'discount', 'shippingCost', 'total', 'user', 'emirates', 'appliedCoupon', 'defaultEmirate'));
    }

    public function shippingQuote(Request $request)
    {
        $request->validate([
            'emirate' => ['nullable', 'string', 'max:100'],
        ]);

        $subtotal = $this->cartService->getCartTotal();
        $discount = $this->cartService->getCouponDiscount($subtotal);
        $shipping = $this->cartService->calculateShipping($subtotal, $request->filled('emirate') ? (string) $request->input('emirate') : null);
        $total    = round($subtotal - $discount + $shipping, 2);
        $sym      = config('bookstore.currency_symbol');

        return response()->json([
            'shipping'      => number_format($shipping, 2, '.', ''),
            'total'         => number_format($total, 2, '.', ''),
            'shipping_free' => $shipping <= 0,
            'currency'      => $sym,
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        if ($request->payment_method === 'stripe') {
            session()->put('pending_stripe_checkout', $request->validated());

            $currency = strtolower((string) config('bookstore.currency_code', env('STRIPE_CURRENCY', 'aed')));
            $lineItems = [];

            $cart = $this->cartService->getCartWithItems();
            $subtotal = $this->cartService->getCartTotal();
            $discount = min($this->cartService->getCouponDiscount($subtotal), $subtotal);
            $shippingCost = $this->cartService->calculateShipping($subtotal, $request->input('shipping_state'));

            if ((float) $discount > 0) {
                $total = round($subtotal - $discount + $shippingCost, 2);
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => $currency,
                        'product_data' => [
                            'name' => 'BookStore order',
                        ],
                        'unit_amount' => (int) round((float) $total * 100),
                    ],
                    'quantity' => 1,
                ];
            } else {
                foreach ($cart->items as $item) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => $currency,
                            'product_data' => [
                                'name' => $item->product->title,
                            ],
                            'unit_amount' => (int) round((float) $item->unit_price * 100),
                        ],
                        'quantity' => $item->quantity,
                    ];
                }

                if ((float) $shippingCost > 0) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => $currency,
                            'product_data' => [
                                'name' => 'Shipping',
                            ],
                            'unit_amount' => (int) round((float) $shippingCost * 100),
                        ],
                        'quantity' => 1,
                    ];
                }
            }

            try {
                $stripe = new StripeClient(env('STRIPE_SECRET'));
                $session = $stripe->checkout->sessions->create([
                    'mode' => 'payment',
                    'customer_email' => $request->shipping_email,
                    'line_items' => $lineItems,
                    'success_url' => route('checkout.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('checkout.stripe.cancel'),
                ]);

                return redirect()->away($session->url);
            } catch (\Exception $e) {
                return back()->withInput()->with('error', $e->getMessage());
            }
        }

        try {
            $order = $this->orderService->placeOrder($request->validated());
            return redirect()
                ->route('orders.show', $order)
                ->with('success', "Order #{$order->order_number} placed successfully! 🎉");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = (string) $request->query('session_id');
        if ($sessionId === '') {
            return redirect()->route('checkout.index')->with('error', 'Stripe session not found.');
        }

        $checkoutData = session()->get('pending_stripe_checkout');
        if (!$checkoutData) {
            return redirect()->route('checkout.index')->with('error', 'Checkout session expired. Please try again.');
        }

        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['payment_intent']]);

            if (($session->payment_status ?? null) !== 'paid') {
                return redirect()->route('checkout.index')->with('error', 'Payment not completed yet.');
            }

            $order = $this->orderService->placeOrder($checkoutData);

            $paymentIntentId = isset($session->payment_intent->id) ? (string) $session->payment_intent->id : null;
            $this->orderService->markStripeOrderPaid($order, $sessionId, $paymentIntentId);

            session()->forget('pending_stripe_checkout');

            return redirect()->route('orders.show', $order)->with('success', "Payment received for order #{$order->order_number}.");
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }
    }

    public function stripeCancel()
    {
        session()->forget('pending_stripe_checkout');

        return redirect()
            ->route('checkout.index')
            ->with('error', 'Payment was cancelled. Your cart has been saved so you can try again.');
    }
}
