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
  "image": ["{{ $product->cover_image ? asset(Storage::url($product->cover_image)) : '' }}"],
  "description": "{{ strip_tags($product->description) }}",
  "sku": "{{ $product->isbn ?? $product->id }}",
  "brand": {"@type": "Brand","name": "{{ $product->author ?? config('bookstore.store_name') }}"},
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
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-4 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-black transition">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-black transition">Shop</a>
        @if($product->category)
        <span>/</span>
        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-black transition">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-600 truncate max-w-[140px] sm:max-w-xs">{{ Str::limit($product->title, 35) }}</span>
    </nav>

    {{-- Main Product Panel --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">

            {{-- Image Column --}}
            <div class="p-4 sm:p-6 lg:border-r border-gray-100">
                <div class="relative bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden" style="height: 320px; max-height: 50vw;">
                    @if($product->discount_percentage > 0)
                    <span class="absolute top-3 left-3 z-10 noon-badge-off text-sm px-2 py-1">{{ $product->discount_percentage }}% OFF</span>
                    @endif
                    @if($product->cover_image)
                    <img id="main-image" src="{{ Storage::url($product->cover_image) }}" alt="{{ $product->title }}" class="w-full h-full object-contain p-4">
                    @else
                    <span class="text-8xl">📖</span>
                    @endif
                </div>
                @if($product->images->isNotEmpty())
                <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
                    @if($product->cover_image)
                    <button onclick="changeImage('{{ Storage::url($product->cover_image) }}')" class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden border-2 border-primary bg-gray-50 p-0.5">
                        <img src="{{ Storage::url($product->cover_image) }}" class="w-full h-full object-contain">
                    </button>
                    @endif
                    @foreach($product->images as $img)
                    <button onclick="changeImage('{{ Storage::url($img->image_path) }}')" class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-primary transition bg-gray-50 p-0.5">
                        <img src="{{ Storage::url($img->image_path) }}" alt="{{ $img->alt_text }}" class="w-full h-full object-contain">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Info Column --}}
            <div class="p-4 sm:p-6 flex flex-col">
                @if($product->category)
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="inline-block text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-black mb-2 transition">{{ $product->category->name }}</a>
                @endif

                <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight mb-1">{{ $product->title }}</h1>
                @if($product->author)
                <p class="text-sm text-gray-500 mb-3">by <span class="font-semibold text-gray-700">{{ $product->author }}</span></p>
                @endif

                {{-- Price Block --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <div class="flex items-baseline gap-3 flex-wrap">
                        <span class="text-3xl font-black text-black">{{ config('bookstore.currency_symbol') }}{{ number_format($product->selling_price, 0) }}</span>
                        @if($product->discount_price)
                        <span class="text-lg text-gray-400 line-through">{{ config('bookstore.currency_symbol') }}{{ number_format($product->price, 0) }}</span>
                        <span class="noon-badge-off text-sm px-2 py-0.5">{{ $product->discount_percentage }}% OFF</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Inclusive of all taxes</p>
                </div>

                {{-- Stock Status --}}
                <div class="mb-4">
                    @if($product->stock > 10)
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-green-600"><span class="w-2 h-2 bg-green-500 rounded-full"></span> In Stock</span>
                    @elseif($product->stock > 0)
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-orange-500"><span class="w-2 h-2 bg-orange-400 rounded-full"></span> Only {{ $product->stock }} left!</span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-500"><span class="w-2 h-2 bg-red-500 rounded-full"></span> Out of Stock</span>
                    @endif
                </div>

                @if($product->description)
                <p class="text-sm text-gray-600 leading-relaxed mb-4 line-clamp-3">{{ $product->description }}</p>
                @endif

                {{-- Add to Cart --}}
                @php
                    $cartService = app(\App\Services\CartService::class);
                    $cart = $cartService->getOrCreateCart();
                    $existingItem = $cart->items()->where('product_id', $product->id)->first();
                @endphp

                @if($product->stock > 0)
                {{-- In-cart controls --}}
                <div id="ui-live-update" class="mb-4 {{ $existingItem ? 'block' : 'hidden' }}"
                     data-cart-item-id="{{ $existingItem->id ?? '' }}"
                     data-current-qty="{{ $existingItem->quantity ?? '' }}">
                    <div class="flex items-center gap-3 bg-primary/10 border-2 border-primary rounded-xl p-2">
                        <button onclick="liveUpdateQty(-1)" class="w-10 h-10 flex items-center justify-center bg-white rounded-lg shadow-sm hover:bg-gray-50 transition font-black text-xl text-black">−</button>
                        <span id="detail-live-qty" class="flex-1 text-center font-bold text-black">{{ $existingItem->quantity ?? 1 }} in cart</span>
                        <button onclick="liveUpdateQty(1)" class="w-10 h-10 flex items-center justify-center bg-black text-white rounded-lg shadow-sm hover:bg-gray-800 transition font-black text-xl">+</button>
                    </div>
                </div>

                {{-- Add button --}}
                <div id="ui-add-to-cart" class="mb-4 {{ $existingItem ? 'hidden' : 'block' }}">
                    <div class="flex gap-3">
                        <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden bg-white h-12">
                            <button onclick="adjustQty(-1)" class="w-11 h-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition font-black text-lg">−</button>
                            <input type="number" id="qty" value="1" min="1" max="{{ $product->stock }}" class="w-10 h-full text-center border-none focus:outline-none bg-transparent text-base font-bold text-black" readonly>
                            <button onclick="adjustQty(1)" class="w-11 h-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition font-black text-lg">+</button>
                        </div>
                        <button onclick="addToCartAndSetup({{ $product->id }})" class="flex-1 noon-btn-cart h-12 rounded-xl font-black text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
                @endif

                {{-- Delivery Info --}}
                <div class="border border-gray-100 rounded-xl p-3 mb-4 space-y-2">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                        <span>Free shipping on orders above <strong>{{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Cash on delivery available</span>
                    </div>
                </div>

                {{-- Product Meta --}}
                @if($product->isbn || $product->category || $product->tags->isNotEmpty())
                <div class="text-xs text-gray-500 space-y-1.5 border-t border-gray-100 pt-4">
                    @if($product->isbn)<p><span class="font-semibold text-gray-700">ISBN:</span> {{ $product->isbn }}</p>@endif
                    @if($product->category)<p><span class="font-semibold text-gray-700">Category:</span> {{ $product->category->name }}</p>@endif
                    @if($product->tags->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                        <span class="font-semibold text-gray-700">Tags:</span>
                        @foreach($product->tags as $tag)
                        <a href="{{ route('shop.index', ['tag' => $tag->slug]) }}" class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full hover:bg-primary hover:text-black transition">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Full Description --}}
    @if($product->full_description)
    <div class="mt-4 bg-white rounded-xl shadow-sm p-4 sm:p-6">
        <h2 class="text-lg font-black text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-1 h-5 bg-primary rounded-full inline-block"></span>
            About This Book
        </h2>
        <div class="prose prose-gray max-w-none text-sm leading-relaxed">
            {!! $product->full_description !!}
        </div>
    </div>
    @endif

    {{-- Related Products --}}
    @if($relatedProducts->isNotEmpty())
    <div class="mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-1 h-6 bg-primary rounded-full"></span>
            <h2 class="text-lg font-black text-gray-900">You May Also Like</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-3">
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
function forceCartBadgeUpdate(count) {
    ['cart-count', 'mobile-cart-badge'].forEach(id => {
        let el = document.getElementById(id);
        if (el) el.textContent = count;
    });
}

function addToCartAndSetup(productId) {
    const qty = document.getElementById('qty')?.value || 1;
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
            forceCartBadgeUpdate(data.cart_count);
            if(window.showToast) window.showToast(data.message, 'success');
        } else { if(window.showToast) window.showToast(data.message, 'error'); }
    }).catch(() => document.body.style.cursor = 'default');
}
let _detailUpdateTimer = null;
function liveUpdateQty(delta) {
    const c2 = document.getElementById('ui-live-update');
    const c1 = document.getElementById('ui-add-to-cart');
    const cartItemId = c2.getAttribute('data-cart-item-id');
    const currentQty = parseInt(c2.getAttribute('data-current-qty'));
    const newQty = currentQty + delta;
    
    // Visually update immediately
    if (newQty <= 0) {
        if (c2) { c2.classList.remove('block'); c2.classList.add('hidden'); }
        if (c1) { c1.classList.remove('hidden'); c1.classList.add('block'); }
        document.getElementById('qty').value = 1;
        c2.setAttribute('data-current-qty', 0);
    } else {
        document.getElementById('detail-live-qty').innerText = newQty + ' in cart';
        c2.setAttribute('data-current-qty', newQty);
    }

    if (_detailUpdateTimer) clearTimeout(_detailUpdateTimer);

    _detailUpdateTimer = setTimeout(() => {
        document.body.style.cursor = 'wait';
        fetch(`/cart/update/${cartItemId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ quantity: newQty }),
        }).then(r => r.json()).then(data => {
            document.body.style.cursor = 'default';
            if (data.success) {
                forceCartBadgeUpdate(data.cart_count);
            } else { if(window.showToast) window.showToast(data.message, 'error'); }
        }).catch(() => document.body.style.cursor = 'default');
    }, 600);
}
</script>
@endpush
@endsection
