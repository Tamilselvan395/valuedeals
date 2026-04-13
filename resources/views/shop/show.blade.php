@extends('layouts.app')
@section('meta_title', ($product->meta_title ?? $product->title) . ' — ' . config('bookstore.store_name'))
@section('meta_description', $product->meta_description ?? strip_tags($product->description))
@section('og_type', 'product')
@if($product->cover_image)
    @section('og_image', Storage::url($product->cover_image))
@endif

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->title }}",
  "image": [
    "{{ $product->cover_image ? asset(Storage::url($product->cover_image)) : '' }}"
   ],
  "description": "{{ strip_tags($product->description) }}",
  "sku": "{{ $product->isbn ?? $product->id }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ $product->author ?? config('bookstore.store_name') }}"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ route('shop.show', $product->slug) }}",
    "priceCurrency": "{{ config('bookstore.currency_code', 'AED') }}",
    "price": "{{ $product->selling_price }}",
    "priceValidUntil": "{{ date('Y-m-d', strtotime('+1 year')) }}",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
  }
}
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-primary">Home</a><span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-primary">Shop</a>
        @if($product->category)<span>/</span><a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-primary">{{ $product->category->name }}</a>@endif
        <span>/</span><span class="text-gray-700">{{ Str::limit($product->title, 40) }}</span>
    </nav>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-white rounded-2xl shadow-sm p-8">
        <div>
            <div class="mb-4 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center h-96">
                @if($product->cover_image)
                <img id="main-image" src="{{ Storage::url($product->cover_image) }}" alt="{{ $product->title }}" class="h-full w-full object-contain">
                @else
                <div class="text-8xl">📖</div>
                @endif
            </div>
            @if($product->images->isNotEmpty())
            <div class="flex gap-3 overflow-x-auto pb-1">
                @if($product->cover_image)
                <button onclick="changeImage('{{ Storage::url($product->cover_image) }}')" class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-primary">
                    <img src="{{ Storage::url($product->cover_image) }}" class="w-full h-full object-cover">
                </button>
                @endif
                @foreach($product->images as $img)
                <button onclick="changeImage('{{ Storage::url($img->image_path) }}')" class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-primary transition">
                    <img src="{{ Storage::url($img->image_path) }}" alt="{{ $img->alt_text }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>
        <div class="flex flex-col">
            @if($product->category)
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="text-xs text-primary font-semibold uppercase tracking-wider mb-2 hover:underline">{{ $product->category->name }}</a>
            @endif
            <h1 class="text-3xl font-playfair font-bold text-gray-900 mb-2">{{ $product->title }}</h1>
            @if($product->author)<p class="text-gray-600 mb-4">by <span class="font-semibold text-gray-800">{{ $product->author }}</span></p>@endif
            <div class="flex items-center gap-3 mb-5">
                <span class="text-3xl font-bold text-gray-900">{{ config('bookstore.currency_symbol') }}{{ number_format($product->selling_price, 2) }}</span>
                @if($product->discount_price)
                <span class="text-lg text-gray-400 line-through">{{ config('bookstore.currency_symbol') }}{{ number_format($product->price, 2) }}</span>
                <span class="bg-red-100 text-red-600 text-sm font-bold px-2 py-0.5 rounded-full">{{ $product->discount_percentage }}% OFF</span>
                @endif
            </div>
            <div class="mb-4">
                @if($product->stock > 10)
                <span class="inline-flex items-center gap-1 text-green-600 text-sm font-semibold"><span class="w-2 h-2 bg-green-500 rounded-full"></span> In Stock</span>
                @elseif($product->stock > 0)
                <span class="inline-flex items-center gap-1 text-orange-600 text-sm font-semibold"><span class="w-2 h-2 bg-orange-500 rounded-full"></span> Only {{ $product->stock }} left!</span>
                @else
                <span class="inline-flex items-center gap-1 text-red-600 text-sm font-semibold"><span class="w-2 h-2 bg-red-500 rounded-full"></span> Out of Stock</span>
                @endif
            </div>
            @if($product->description)<p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $product->description }}</p>@endif
            @php
                $cartService = app(\App\Services\CartService::class);
                $cart = $cartService->getOrCreateCart();
                $existingItem = $cart->items()->where('product_id', $product->id)->first();
            @endphp
            @if($product->stock > 0)
                <div id="ui-live-update" class="w-full max-w-sm mb-6 {{ $existingItem ? 'block' : 'hidden' }}" 
                     data-cart-item-id="{{ $existingItem->id ?? '' }}" 
                     data-current-qty="{{ $existingItem->quantity ?? '' }}">
                    <div class="flex items-center gap-3 w-full">
                        <div class="flex-1 flex items-center justify-between border-2 border-primary bg-yellow-50 rounded-full overflow-hidden p-1.5 shadow-inner transition-all">
                            <button onclick="liveUpdateQty(-1)" class="w-10 h-10 flex items-center justify-center text-primary bg-white rounded-full shadow-sm hover:bg-yellow-100 transition font-bold text-xl">−</button>
                            <span id="detail-live-qty" class="text-sm font-bold text-gray-900">{{ $existingItem->quantity ?? 1 }} in cart</span>
                            <button onclick="liveUpdateQty(1)" class="w-10 h-10 flex items-center justify-center text-white bg-primary rounded-full shadow-sm hover:bg-secondary transition font-bold text-xl">+</button>
                        </div>
                    </div>
                </div>
                <div id="ui-add-to-cart" class="w-full max-w-sm mb-6 {{ $existingItem ? 'hidden' : 'block' }}">
                    <div class="flex items-center gap-3 w-full">
                        <div class="flex items-center border border-gray-300 rounded-full overflow-hidden shadow-inner bg-gray-50 h-12">
                            <button onclick="adjustQty(-1)" class="w-12 h-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition text-lg font-bold">−</button>
                            <input type="number" id="qty" value="1" min="1" max="{{ $product->stock }}" class="w-12 h-full text-center border-none focus:outline-none focus:ring-0 bg-transparent text-sm font-semibold text-gray-900" readonly>
                            <button onclick="adjustQty(1)" class="w-12 h-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition text-lg font-bold">+</button>
                        </div>
                        <button onclick="addToCartAndSetup({{ $product->id }})" class="flex-1 bg-primary text-secondary h-12 rounded-full font-bold hover:bg-yellow-400 transition flex items-center justify-center gap-2 shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            @endif
            <div class="border-t border-gray-100 pt-5 space-y-2 text-sm text-gray-600">
                @if($product->isbn)<p><span class="font-semibold text-gray-800">ISBN:</span> {{ $product->isbn }}</p>@endif
                @if($product->category)<p><span class="font-semibold text-gray-800">Category:</span> {{ $product->category->name }}</p>@endif
                @if($product->tags->isNotEmpty())
                <p class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-gray-800">Tags:</span>
                    @foreach($product->tags as $tag)
                    <a href="{{ route('shop.index', ['tag' => $tag->slug]) }}" class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs hover:bg-yellow-100 hover:text-secondary transition">{{ $tag->name }}</a>
                    @endforeach
                </p>
                @endif
            </div>
            <div class="mt-5 bg-yellow-50 rounded-xl p-4 text-sm text-secondary flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                <span>Free shipping on orders above <strong>{{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}</strong>. Below that, delivery from <strong>{{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.flat_shipping_rate'), 0) }}</strong> (varies by emirate). Cash on delivery available.</span>
            </div>
        </div>
    </div>
    @if($product->full_description)
    <div class="mt-10 bg-white rounded-2xl shadow-sm p-8">
        <h2 class="text-xl font-playfair font-bold text-gray-900 mb-4">About This Book</h2>
        <div class="prose prose-gray max-w-none text-sm leading-relaxed">{!! $product->full_description !!}</div>
    </div>
    @endif
    @if($relatedProducts->isNotEmpty())
    <div class="mt-14">
        <h2 class="text-2xl font-playfair font-bold text-gray-900 mb-6">You May Also Like</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            @foreach($relatedProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif
</div>
@push('scripts')
<script>
function changeImage(src) { document.getElementById('main-image').src = src; }
function adjustQty(delta) {
    const input = document.getElementById('qty');
    if (!input) return;
    input.value = Math.max(1, Math.min(parseInt(input.max), parseInt(input.value) + delta));
}
function addToCartAndSetup(productId) {
    const qty = document.getElementById('qty')?.value || 1;
    if (window.addToCart) {
        document.body.style.cursor = 'wait';
        fetch('/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: parseInt(qty) }),
        }).then(r => r.json()).then(data => {
            document.body.style.cursor = 'default';
            if(data.success) {
                const c1 = document.getElementById('ui-add-to-cart');
                const c2 = document.getElementById('ui-live-update');
                if (c1) { c1.classList.remove('block'); c1.classList.add('hidden'); }
                if (c2) {
                    c2.classList.remove('hidden'); c2.classList.add('block');
                    document.getElementById('detail-live-qty').innerText = data.new_quantity + ' in cart';
                    c2.setAttribute('data-cart-item-id', data.cart_item_id);
                    c2.setAttribute('data-current-qty', data.new_quantity);
                }
                if(window.updateCartBadge) window.updateCartBadge(data.cart_count);
                if(window.showToast) window.showToast(data.message, 'success');
            } else { if(window.showToast) window.showToast(data.message, 'error'); }
        }).catch(() => document.body.style.cursor = 'default');
    }
}
function liveUpdateQty(delta) {
    const c2 = document.getElementById('ui-live-update');
    const c1 = document.getElementById('ui-add-to-cart');
    const cartItemId = c2.getAttribute('data-cart-item-id');
    const currentQty = parseInt(c2.getAttribute('data-current-qty'));
    const newQty = currentQty + delta;
    
    document.body.style.cursor = 'wait';
    fetch(`/cart/update/${cartItemId}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: newQty }),
    }).then(r => r.json()).then(data => {
        document.body.style.cursor = 'default';
        if (data.success) {
            if (newQty <= 0) {
                // If they remove it via the detail page 0 qty, swap back to the 'Add to cart' view
                if (c2) { c2.classList.remove('block'); c2.classList.add('hidden'); }
                if (c1) { c1.classList.remove('hidden'); c1.classList.add('block'); }
                document.getElementById('qty').value = 1;
            } else {
                document.getElementById('detail-live-qty').innerText = newQty + ' in cart';
                c2.setAttribute('data-current-qty', newQty);
            }
            if(window.updateCartBadge) window.updateCartBadge(data.cart_count);
        } else { if(window.showToast) window.showToast(data.message, 'error'); }
    }).catch(() => document.body.style.cursor = 'default');
}
</script>
@endpush
@endsection
