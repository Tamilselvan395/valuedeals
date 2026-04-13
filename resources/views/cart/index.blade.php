@extends('layouts.app')
@section('meta_title', 'Your Cart — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-playfair font-bold text-gray-900 mb-8">Your Cart</h1>
    @if($cart->items->isEmpty())
    <div class="text-center py-24 bg-white rounded-2xl shadow-sm">
        <div class="text-7xl mb-5">🛒</div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-3">Your cart is empty</h2>
        <p class="text-gray-500 mb-8">Looks like you haven't added any books yet.</p>
        <a href="{{ route('shop.index') }}" class="bg-primary text-white px-8 py-3 rounded-full font-semibold hover:bg-secondary transition shadow-md">Browse Books</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4" id="cart-items-container">
            @foreach($cart->items as $item)
            <div class="bg-white rounded-2xl shadow-sm p-5 flex gap-4 items-start" id="cart-item-{{ $item->id }}">
                <a href="{{ route('shop.show', $item->product->slug) }}" class="flex-shrink-0">
                    @if($item->product->cover_image)
                    <img src="{{ Storage::url($item->product->cover_image) }}" alt="{{ $item->product->title }}" class="w-20 h-24 object-cover rounded-xl">
                    @else
                    <div class="w-20 h-24 bg-yellow-100 rounded-xl flex items-center justify-center text-3xl">📖</div>
                    @endif
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('shop.show', $item->product->slug) }}" class="font-semibold text-gray-900 hover:text-secondary transition text-sm line-clamp-2 leading-snug">{{ $item->product->title }}</a>
                    @if($item->product->author)<p class="text-xs text-gray-500 mt-0.5">{{ $item->product->author }}</p>@endif
                    <p class="text-sm font-bold text-gray-800 mt-2">{{ config('bookstore.currency_symbol') }}{{ number_format($item->unit_price, 2) }}</p>
                    <div class="flex items-center justify-between mt-3">
                        <div class="flex items-center border border-gray-200 rounded-full overflow-hidden">
                            <button onclick="updateCartItem({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition font-bold">−</button>
                            <span id="qty-{{ $item->id }}" class="w-10 text-center text-sm font-semibold">{{ $item->quantity }}</span>
                            <button onclick="updateCartItem({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition font-bold">+</button>
                        </div>
                        <span id="subtotal-{{ $item->id }}" class="font-bold text-gray-900 text-sm">{{ config('bookstore.currency_symbol') }}{{ number_format($item->subtotal, 2) }}</span>
                        <button onclick="removeCartItem({{ $item->id }})" class="text-red-400 hover:text-red-600 transition p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                <h2 class="text-lg font-playfair font-bold text-gray-900 mb-5">Order Summary</h2>
                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="code" value="{{ old('code', $appliedCoupon?->code) }}" placeholder="Coupon code"
                        class="flex-1 min-w-0 border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none uppercase">
                    <button type="submit" class="shrink-0 bg-gray-800 text-white px-3 py-2 rounded-xl text-sm font-medium hover:bg-gray-900">Apply</button>
                </form>
                @if($appliedCoupon)
                <form action="{{ route('cart.coupon.remove') }}" method="POST" class="mb-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline">Remove coupon {{ $appliedCoupon->code }}</button>
                </form>
                @endif
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span id="summary-subtotal">{{ config('bookstore.currency_symbol') }}{{ number_format($subtotal, 2) }}</span></div>
                    @if(($discount ?? 0) > 0)
                    <div class="flex justify-between text-green-700"><span>Discount</span><span id="summary-discount">−{{ config('bookstore.currency_symbol') }}{{ number_format($discount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span id="summary-shipping">
                            @if($shippingCost == 0)<span class="text-green-600 font-semibold">FREE</span>
                            @else{{ config('bookstore.currency_symbol') }}{{ number_format($shippingCost, 2) }}@endif
                        </span>
                    </div>
                    @if($shippingCost > 0 && $subtotal < config('bookstore.free_shipping_threshold'))
                    <p class="text-xs text-primary">Add {{ config('bookstore.currency_symbol') }}{{ number_format(max(0, config('bookstore.free_shipping_threshold') - $subtotal), 2) }} more for free shipping.</p>
                    @endif
                    <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-gray-900 text-base">
                        <span>Total</span><span id="summary-total">{{ config('bookstore.currency_symbol') }}{{ number_format($total, 2) }}</span>
                    </div>
                </div>
                @auth
                <a href="{{ route('checkout.index') }}" class="mt-6 block w-full bg-primary text-white text-center py-3 rounded-full font-semibold hover:bg-secondary transition shadow-md">Proceed to Checkout</a>
                @else
                <a href="{{ route('register', ['checkout' => '1']) }}" class="mt-6 block w-full bg-primary text-white text-center py-3 rounded-full font-semibold hover:bg-secondary transition shadow-md">Register to Checkout</a>
                <p class="mt-2 text-center text-sm text-gray-600">Already have an account? <a href="{{ route('login') }}" class="text-secondary font-medium hover:underline">Log in</a></p>
                @endauth
                <a href="{{ route('shop.index') }}" class="mt-3 block text-center text-sm text-gray-500 hover:text-primary transition">← Continue Shopping</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
