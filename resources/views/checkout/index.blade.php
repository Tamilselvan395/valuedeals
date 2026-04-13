@extends('layouts.app')
@section('meta_title', 'Checkout — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-playfair font-bold text-gray-900 mb-8">Checkout</h1>
    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Shipping Address</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', $user->name) }}" required
                                class="w-full border @error('shipping_name') border-red-400 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                            @error('shipping_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <div class="flex">
                                <select name="phone_code" class="border border-r-0 border-gray-300 rounded-l-xl px-2 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none bg-gray-50 text-gray-600">
                                    @php $codes = config('country_codes', ['+971']); @endphp
                                    @foreach($codes as $code)
                                    <option value="{{ $code }}" @selected(old('phone_code', '+971') === $code)>{{ $code }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="shipping_phone_number" value="{{ old('shipping_phone_number') }}" required
                                    class="w-full border @error('shipping_phone') border-red-400 @else border-gray-300 @enderror rounded-r-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                            </div>
                            @error('shipping_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="shipping_email" value="{{ old('shipping_email', $user->email) }}" required
                                class="w-full border @error('shipping_email') border-red-400 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                            @error('shipping_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                            <input type="text" name="shipping_address" value="{{ old('shipping_address') }}" required placeholder="House no., street name, area"
                                class="w-full border @error('shipping_address') border-red-400 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                            @error('shipping_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">UAE emirate *</label>
                            <select id="shipping_state" name="shipping_state" required
                                class="w-full border @error('shipping_state') border-red-400 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none bg-white">
                                @foreach($emirates as $emirate)
                                <option value="{{ $emirate->slug }}" @selected(old('shipping_state', $defaultEmirate) === $emirate->slug)>{{ $emirate->name }}</option>
                                @endforeach
                            </select>
                            @error('shipping_state')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Rates are set per emirate in the admin panel.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Area / district *</label>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required placeholder="e.g. Marina, Khalifa City"
                                class="w-full border @error('shipping_city') border-red-400 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                            @error('shipping_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="shipping_country" value="{{ old('shipping_country', 'UAE') }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none bg-gray-50">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order Notes (optional)</label>
                            <textarea name="notes" rows="2" placeholder="Any special instructions..."
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none resize-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>
                    <div class="space-y-3" id="payment-method-container">
                        <label id="label-cod" class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ old('payment_method', 'cod') === 'cod' ? 'border-primary bg-yellow-50' : 'border-gray-200 bg-white' }}">
                            <input type="radio" name="payment_method" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }} class="accent-primary">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Cash on Delivery (COD)</p>
                                <p class="text-xs text-gray-500 mt-0.5">Pay when your books arrive at your doorstep</p>
                            </div>
                            <span class="ml-auto text-2xl">💵</span>
                        </label>
                        <label id="label-stripe" class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all {{ old('payment_method') === 'stripe' ? 'border-primary bg-yellow-50' : 'border-gray-200 bg-white' }}">
                            <input type="radio" name="payment_method" value="stripe" {{ old('payment_method') === 'stripe' ? 'checked' : '' }} class="accent-primary">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Pay with Stripe (Card / UPI / Wallets)</p>
                                <p class="text-xs text-gray-500 mt-0.5">You will be redirected to Stripe secure checkout</p>
                            </div>
                            <span class="ml-auto text-2xl">💳</span>
                        </label>
                        @error('payment_method')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                    <h2 class="text-lg font-playfair font-bold text-gray-900 mb-5">Your Order</h2>
                    <div class="space-y-4 mb-5 max-h-64 overflow-y-auto pr-1">
                        @foreach($cart->items as $item)
                        <div class="flex gap-3 items-center">
                            @if($item->product->cover_image)
                            <img src="{{ Storage::url($item->product->cover_image) }}" alt="{{ $item->product->title }}" class="w-12 h-14 object-cover rounded-lg flex-shrink-0">
                            @else
                            <div class="w-12 h-14 bg-yellow-100 rounded-lg flex items-center justify-center text-xl flex-shrink-0">📖</div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 line-clamp-2 leading-snug">{{ $item->product->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Qty: {{ $item->quantity }}</p>
                            </div>
                            <p class="text-xs font-bold text-gray-900 flex-shrink-0">{{ config('bookstore.currency_symbol') }}{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 pt-4 space-y-3 text-sm">
                        @if($appliedCoupon)
                        <p class="text-xs text-gray-500">Coupon <strong>{{ $appliedCoupon->code }}</strong> applied (from cart).</p>
                        @endif
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>{{ config('bookstore.currency_symbol') }}{{ number_format($subtotal, 2) }}</span></div>
                        @if(($discount ?? 0) > 0)
                        <div class="flex justify-between text-green-700"><span>Discount</span><span>−{{ config('bookstore.currency_symbol') }}{{ number_format($discount, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span id="checkout-shipping-display">@if($shippingCost == 0)<span class="text-green-600 font-semibold">FREE</span>@else{{ config('bookstore.currency_symbol') }}{{ number_format($shippingCost, 2) }}@endif</span>
                        </div>
                        @if($subtotal < config('bookstore.free_shipping_threshold'))
                        <p class="text-xs text-primary">Free shipping on orders over {{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}.</p>
                        @endif
                        <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-gray-900 text-base">
                            <span>Total</span><span id="checkout-total-display">{{ config('bookstore.currency_symbol') }}{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                    <button type="submit" class="mt-6 w-full bg-primary text-white py-3 rounded-full font-semibold hover:bg-secondary transition shadow-md text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Place Order
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-3">By placing your order, you agree to our terms & conditions.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
(function () {
    const sel = document.getElementById('shipping_state');
    const shipEl = document.getElementById('checkout-shipping-display');
    const totalEl = document.getElementById('checkout-total-display');
    if (!sel || !shipEl || !totalEl) return;
    sel.addEventListener('change', async function () {
        const url = `{{ route('checkout.shipping-quote') }}?emirate=${encodeURIComponent(sel.value)}`;
        const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
        if (!r.ok) return;
        const d = await r.json();
        if (d.shipping_free) {
            shipEl.innerHTML = '<span class="text-green-600 font-semibold">FREE</span>';
        } else {
            shipEl.textContent = d.currency + d.shipping;
        }
        totalEl.textContent = d.currency + d.total;
    });

    // Payment Method UI Toggle
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const labelCod = document.getElementById('label-cod');
    const labelStripe = document.getElementById('label-stripe');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'cod') {
                labelCod.className = 'flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all border-primary bg-yellow-50';
                labelStripe.className = 'flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all border-gray-200 bg-white';
            } else if (this.value === 'stripe') {
                labelStripe.className = 'flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all border-primary bg-yellow-50';
                labelCod.className = 'flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all border-gray-200 bg-white';
            }
        });
    });
})();
</script>
@endpush
@endsection
