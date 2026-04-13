@extends('layouts.app')
@section('meta_title', 'Order ' . $order->order_number . ' — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('orders.index') }}" class="text-sm text-primary hover:underline flex items-center gap-1 mb-2">← Back to Orders</a>
            <h1 class="text-2xl font-playfair font-bold text-gray-900">Order {{ $order->order_number }}</h1>
            <p class="text-sm text-gray-500 mt-1">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('orders.invoice', $order) }}" class="bg-primary text-white px-5 py-2 rounded-full font-semibold text-sm hover:bg-secondary transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Invoice PDF
        </a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
            @php $steps = ['pending','processing','shipped','delivered']; $currentIdx = array_search($order->status, $steps); @endphp
            @if($order->status !== 'cancelled')
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wider">Order Status</h2>
                <div class="flex items-center">
                    @foreach($steps as $i => $step)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $i <= $currentIdx ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                            {{ $i < $currentIdx ? '✓' : $i + 1 }}
                        </div>
                        <p class="text-xs mt-1 text-center {{ $i <= $currentIdx ? 'text-secondary font-semibold' : 'text-gray-400' }}">{{ ucfirst($step) }}</p>
                    </div>
                    @if($i < count($steps) - 1)
                    <div class="flex-1 h-0.5 {{ $i < $currentIdx ? 'bg-primary' : 'bg-gray-200' }} mb-5"></div>
                    @endif
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-red-700 text-sm font-semibold">❌ This order has been cancelled.</div>
            @endif
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wider">Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4">
                        @if($item->product && $item->product->cover_image)
                        <img src="{{ Storage::url($item->product->cover_image) }}" alt="{{ $item->product_title }}" class="w-14 h-16 object-cover rounded-lg flex-shrink-0">
                        @else
                        <div class="w-14 h-16 bg-yellow-100 rounded-lg flex items-center justify-center text-2xl flex-shrink-0">📖</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm">{{ $item->product_title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ config('bookstore.currency_symbol') }}{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">{{ config('bookstore.currency_symbol') }}{{ number_format($item->subtotal, 2) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="space-y-5">
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wider">Payment Summary</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>{{ config('bookstore.currency_symbol') }}{{ number_format($order->subtotal, 2) }}</span></div>
                    @if((float) $order->discount_amount > 0)
                    <div class="flex justify-between text-green-700"><span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span><span>−{{ config('bookstore.currency_symbol') }}{{ number_format($order->discount_amount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between text-gray-600"><span>Shipping</span><span>{{ $order->shipping_cost == 0 ? 'FREE' : config('bookstore.currency_symbol') . number_format($order->shipping_cost, 2) }}</span></div>
                    <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-900"><span>Total</span><span>{{ config('bookstore.currency_symbol') }}{{ number_format($order->total, 2) }}</span></div>
                    <div class="flex justify-between text-gray-600 pt-1"><span>Payment Method</span><span class="font-semibold text-gray-800">{{ $order->payment_method_label }}</span></div>
                    <div class="flex justify-between text-gray-600 pt-1"><span>Payment Status</span><span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-gray-800' }}">{{ ucfirst($order->payment_status) }}</span></div>
                </div>
            </div>
            @php
                $emirateLabel = $order->shipping_state
                    ? (\App\Models\EmirateShippingRate::where('slug', $order->shipping_state)->value('name') ?? $order->shipping_state)
                    : null;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wider">Shipping To</h2>
                <address class="not-italic text-sm text-gray-600 space-y-1">
                    <p class="font-semibold text-gray-900">{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_phone }}</p>
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}@if($emirateLabel), {{ $emirateLabel }}@endif — {{ $order->shipping_pincode }}</p>
                    <p>{{ $order->shipping_country }}</p>
                </address>
            </div>
            @if($order->isCancellable())
            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                @csrf
                <button type="submit" class="w-full border border-red-400 text-red-500 py-2 rounded-full font-semibold text-sm hover:bg-red-50 transition">Cancel Order</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
