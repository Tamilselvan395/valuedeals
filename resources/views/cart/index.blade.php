@extends('layouts.app')
@section('meta_title', 'Your Cart — ' . config('bookstore.store_name'))

@section('content')
<style>
.cart-item-row { transition: box-shadow 0.2s, border-color 0.2s; }
.cart-item-row:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); border-color: #e5e7eb; }
.qty-btn { width:32px;height:32px;border-radius:50%;border:2px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:16px;color:#111;transition:all 0.15s; }
.qty-btn:hover { background:#feee00;border-color:#feee00; }
.remove-btn { width:34px;height:34px;border-radius:50%;border:none;background:#f9fafb;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#d1d5db;transition:all 0.2s;margin-left:auto; }
.remove-btn:hover { background:#fee2e2;color:#ef4444; }
.checkout-btn { display:block;width:100%;text-align:center;padding:15px;border-radius:14px;background:#feee00;color:#000;font-weight:900;font-size:15px;text-decoration:none;border:none;cursor:pointer;transition:background 0.15s,transform 0.1s,box-shadow 0.15s;box-shadow:0 4px 14px rgba(254,238,0,0.5); }
.checkout-btn:hover { background:#f5e500;transform:translateY(-1px);box-shadow:0 6px 20px rgba(254,238,0,0.6); }
.checkout-btn:active { transform:translateY(0); }
.coupon-input { width:100%;border:2px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;outline:none;transition:border-color 0.2s;box-sizing:border-box; }
.coupon-input:focus { border-color:#feee00; }
.coupon-btn { padding:10px 18px;background:#111;color:#fff;border:none;border-radius:10px;font-weight:800;font-size:13px;cursor:pointer;white-space:nowrap;transition:background 0.15s; }
.coupon-btn:hover { background:#333; }
/* responsive layout */
.cart-layout { display:grid;grid-template-columns:1fr;gap:20px; }
@media (min-width:1024px) { .cart-layout { grid-template-columns:1fr 380px; } .summary-card { position:sticky;top:88px; } }
/* mobile cart item */
@media (max-width:479px) {
    .cart-item-row { padding:12px !important; }
    .cart-item-img { width:60px !important;height:78px !important; }
    .qty-btn { width:28px;height:28px;font-size:14px; }
    .remove-btn { width:28px;height:28px; }
    .cart-actions-row { flex-wrap:wrap;gap:8px !important; }
}
</style>

<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-10">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px;">
        <span style="width:5px;height:28px;background:#feee00;border-radius:99px;display:inline-block;"></span>
        <h1 style="font-size:24px;font-weight:900;color:#111;">Shopping Cart
            @if(!$cart->items->isEmpty())
            <span style="font-size:14px;font-weight:600;color:#9ca3af;margin-left:8px;">{{ $cart->items->sum('quantity') }} item(s)</span>
            @endif
        </h1>
    </div>

    @if($cart->items->isEmpty())
    {{-- Empty State --}}
    <div style="text-align:center;padding:80px 20px;background:#fff;border-radius:20px;border:1px solid #f3f4f6;">
        <div style="font-size:72px;margin-bottom:16px;line-height:1;">🛒</div>
        <h2 style="font-size:20px;font-weight:900;color:#111;margin-bottom:8px;">Your cart is empty</h2>
        <p style="font-size:14px;color:#9ca3af;margin-bottom:28px;">Looks like you haven't added any books yet.</p>
        <a href="{{ route('shop.index') }}" style="display:inline-block;background:#feee00;color:#000;font-weight:900;font-size:14px;padding:12px 32px;border-radius:12px;text-decoration:none;box-shadow:0 4px 14px rgba(254,238,0,0.4);">Browse Books →</a>
    </div>

    @else
    <div class="cart-layout">

        {{-- Cart Items --}}
        <div id="cart-items-container" style="display:flex;flex-direction:column;gap:12px;">
            @foreach($cart->items as $item)
            <div class="cart-item-row" id="cart-item-{{ $item->id }}"
                 style="background:#fff;border-radius:16px;border:1px solid #f3f4f6;padding:16px;display:flex;gap:16px;align-items:flex-start;">

                {{-- Book Cover --}}
                <a href="{{ route('shop.show', $item->product->slug) }}" style="flex-shrink:0;">
                    <div class="cart-item-img" style="width:72px;height:96px;border-radius:10px;overflow:hidden;background:#f9fafb;display:flex;align-items:center;justify-content:center;">
                        @if($item->product->cover_image)
                        <img src="{{ Storage::url($item->product->cover_image) }}" alt="{{ $item->product->title }}"
                             style="width:100%;height:100%;object-fit:contain;padding:4px;">
                        @else
                        <span style="font-size:32px;">📖</span>
                        @endif
                    </div>
                </a>

                {{-- Details --}}
                <div style="flex:1;min-width:0;">
                    <a href="{{ route('shop.show', $item->product->slug) }}" style="text-decoration:none;">
                        <h3 style="font-size:14px;font-weight:800;color:#111;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:3px;">{{ $item->product->title }}</h3>
                    </a>
                    @if($item->product->author)
                    <p style="font-size:12px;color:#9ca3af;margin-bottom:8px;">{{ $item->product->author }}</p>
                    @endif

                    {{-- Mobile: price --}}
                    <p style="font-size:16px;font-weight:900;color:#111;margin-bottom:12px;">
                        {{ config('bookstore.currency_symbol') }}{{ number_format($item->unit_price, 0) }}
                    </p>

                    {{-- Qty + Remove row --}}
                    <div class="cart-actions-row" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        {{-- Qty stepper --}}
                        <div style="display:flex;align-items:center;gap:8px;background:#f9fafb;border-radius:99px;padding:4px 8px;">
                            <button class="qty-btn" onclick="updateCartItem({{ $item->id }}, {{ $item->quantity - 1 }})" aria-label="Decrease">−</button>
                            <span id="qty-{{ $item->id }}" style="font-size:14px;font-weight:900;color:#111;min-width:20px;text-align:center;">{{ $item->quantity }}</span>
                            <button class="qty-btn" onclick="updateCartItem({{ $item->id }}, {{ $item->quantity + 1 }})" aria-label="Increase">+</button>
                        </div>

                        {{-- Subtotal --}}
                        <span id="subtotal-{{ $item->id }}" style="font-size:14px;font-weight:900;color:#111;">
                            {{ config('bookstore.currency_symbol') }}{{ number_format($item->subtotal, 0) }}
                        </span>

                        {{-- Remove --}}
                        <button class="remove-btn" onclick="removeCartItem({{ $item->id }})" aria-label="Remove item">
                            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Order Summary --}}
        <div class="order-summary-col">
            <div style="background:#fff;border-radius:20px;border:1px solid #f3f4f6;padding:24px;position:sticky;top:88px;">
                <h2 style="font-size:16px;font-weight:900;color:#111;margin-bottom:20px;">Order Summary</h2>

                {{-- Coupon --}}
                <div style="margin-bottom:16px;">
                    <form action="{{ route('cart.coupon.apply') }}" method="POST" style="display:flex;gap:8px;">
                        @csrf
                        <input type="text" name="code" value="{{ old('code', $appliedCoupon?->code) }}"
                               placeholder="Promo code" class="coupon-input" style="flex:1;">
                        <button type="submit" class="coupon-btn">Apply</button>
                    </form>
                    @if($appliedCoupon)
                    <form action="{{ route('cart.coupon.remove') }}" method="POST" style="margin-top:8px;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;cursor:pointer;font-size:12px;font-weight:700;color:#ef4444;display:flex;align-items:center;gap:4px;">
                            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Remove "{{ $appliedCoupon->code }}"
                        </button>
                    </form>
                    @endif
                </div>

                {{-- Line items --}}
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#6b7280;">Subtotal</span>
                        <span id="summary-subtotal" style="font-size:13px;font-weight:700;color:#111;">{{ config('bookstore.currency_symbol') }}{{ number_format($subtotal, 0) }}</span>
                    </div>

                    @if(($discount ?? 0) > 0)
                    <div style="display:flex;justify-content:space-between;align-items:center;background:#f0fdf4;border-radius:8px;padding:8px 10px;">
                        <span style="font-size:13px;color:#16a34a;font-weight:700;">🎉 Discount</span>
                        <span id="summary-discount" style="font-size:13px;font-weight:800;color:#16a34a;">−{{ config('bookstore.currency_symbol') }}{{ number_format($discount, 0) }}</span>
                    </div>
                    @endif

                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#6b7280;">Shipping</span>
                        <span id="summary-shipping" style="font-size:13px;font-weight:700;">
                            @if($shippingCost == 0)
                            <span style="color:#16a34a;font-weight:800;">FREE 🚚</span>
                            @else
                            {{ config('bookstore.currency_symbol') }}{{ number_format($shippingCost, 0) }}
                            @endif
                        </span>
                    </div>

                    @if($shippingCost > 0 && $subtotal < config('bookstore.free_shipping_threshold'))
                    <div style="background:#fffbeb;border-radius:10px;padding:10px 12px;border:1px solid #fde68a;">
                        <p style="font-size:11px;color:#92400e;font-weight:600;margin:0;">
                            Add <strong style="color:#000;">{{ config('bookstore.currency_symbol') }}{{ number_format(max(0, config('bookstore.free_shipping_threshold') - $subtotal), 0) }}</strong> more for FREE shipping! 🎁
                        </p>
                    </div>
                    @endif

                    <div style="border-top:2px dashed #f3f4f6;padding-top:14px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:16px;font-weight:900;color:#111;">Total</span>
                        <span id="summary-total" style="font-size:20px;font-weight:900;color:#111;">{{ config('bookstore.currency_symbol') }}{{ number_format($total, 0) }}</span>
                    </div>
                </div>

                {{-- CTA --}}
                <div style="margin-top:20px;">
                    @auth
                    <a href="{{ route('checkout.index') }}" class="checkout-btn">
                        Proceed to Checkout →
                    </a>
                    @else
                    <a href="{{ route('register', ['checkout' => '1']) }}" class="checkout-btn">Register to Checkout</a>
                    <p style="margin-top:10px;text-align:center;font-size:12px;color:#9ca3af;">Already have an account? <a href="{{ route('login') }}" style="font-weight:800;color:#111;text-decoration:none;">Sign in</a></p>
                    @endauth
                    <a href="{{ route('shop.index') }}" style="display:block;text-align:center;font-size:12px;font-weight:700;color:#9ca3af;text-decoration:none;margin-top:12px;">← Continue Shopping</a>
                </div>
            </div>

            {{-- Trust badges --}}
            <div style="background:#fff;border-radius:16px;border:1px solid #f3f4f6;padding:16px;margin-top:12px;display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:16px;height:16px;color:#16a34a;" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <span style="font-size:12px;font-weight:600;color:#374151;">Secure & encrypted checkout</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span style="font-size:12px;font-weight:600;color:#374151;">Cash on delivery available</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:#fefce8;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#ca8a04" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                    </div>
                    <span style="font-size:12px;font-weight:600;color:#374151;">Fast delivery across UAE</span>
                </div>
            </div>
        </div>

    </div>
    @endif
</div>

<style>
@media (min-width: 1024px) {
    .cart-layout { grid-template-columns: 1fr 380px !important; }
}
</style>
@endsection
