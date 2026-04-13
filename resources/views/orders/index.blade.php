@extends('layouts.app')
@section('meta_title', 'My Orders — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-playfair font-bold text-gray-900 mb-8">My Orders</h1>
    @if($orders->isEmpty())
    <div class="text-center py-24 bg-white rounded-2xl shadow-sm">
        <div class="text-6xl mb-4">📦</div>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">No orders yet</h2>
        <p class="text-gray-500 mb-6">When you place an order, it will appear here.</p>
        <a href="{{ route('shop.index') }}" class="bg-primary text-white px-8 py-3 rounded-full font-semibold hover:bg-secondary transition">Start Shopping</a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        @php
        $colors = ['pending'=>'bg-yellow-100 text-yellow-700','processing'=>'bg-blue-100 text-blue-700','shipped'=>'bg-purple-100 text-purple-700','delivered'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700'];
        @endphp
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($order->status) }}</span>
                    <span class="font-bold text-gray-900">{{ config('bookstore.currency_symbol') }}{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 mb-4">
                @foreach($order->items->take(3) as $item)
                <span class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full">{{ Str::limit($item->product_title, 25) }} ×{{ $item->quantity }}</span>
                @endforeach
                @if($order->items->count() > 3)
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">+{{ $order->items->count() - 3 }} more</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-primary hover:text-secondary border border-primary px-4 py-1.5 rounded-full hover:bg-yellow-50 transition">View Details</a>
                <a href="{{ route('orders.invoice', $order) }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900 border border-gray-300 px-4 py-1.5 rounded-full hover:bg-gray-50 transition">Download Invoice</a>
                @if($order->isCancellable())
                <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel this order?')">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 border border-red-200 px-4 py-1.5 rounded-full hover:bg-red-50 transition">Cancel Order</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
