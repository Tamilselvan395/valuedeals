@extends('layouts.app')

@section('content')

{{-- ===== HERO BANNER ===== --}}
<section class="bg-white border-b border-gray-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center gap-6 md:gap-16 py-10 md:py-14">

            {{-- Left: Text --}}
            <div class="flex-1 min-w-0 text-center md:text-left">
                <span class="inline-flex items-center gap-1.5 bg-primary text-black text-xs font-black px-3 py-1.5 rounded-full mb-5 tracking-wider uppercase shadow-sm">
                    🚚 Free Shipping above {{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}
                </span>
                <h1 class="text-4xl md:text-5xl xl:text-6xl font-black text-gray-900 leading-[1.1] mb-4">
                    Books You'll<br>
                    <span class="relative inline-block text-black">
                        Love to Read
                        <span class="absolute -bottom-1 left-0 right-0 h-3 bg-primary/50 -z-10 rounded-sm"></span>
                    </span>
                </h1>
                <p class="text-gray-500 text-base md:text-lg mb-7 max-w-sm mx-auto md:mx-0 leading-relaxed">Thousands of titles at unbeatable prices. Fast delivery across UAE.</p>
                <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                    <a href="{{ route('shop.index') }}" class="bg-black text-white px-7 py-3 rounded-xl font-black text-sm hover:bg-gray-800 transition-all duration-200 shadow-lg shadow-black/10 flex items-center gap-2">
                        Shop Now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('shop.index', ['sort' => 'latest']) }}" class="bg-primary text-black px-7 py-3 rounded-xl font-black text-sm hover:bg-yellow-300 transition-all duration-200 border border-primary">
                        New Arrivals
                    </a>
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-5 mt-9 justify-center md:justify-start">
                    <div class="text-center">
                        <p class="text-2xl font-black text-gray-900">10K+</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Books</p>
                    </div>
                    <div class="w-px h-10 bg-gray-200"></div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-gray-900">50K+</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Readers</p>
                    </div>
                    <div class="w-px h-10 bg-gray-200"></div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-gray-900">4.9★</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Rating</p>
                    </div>
                </div>
            </div>

            {{-- Right: 3D Coverflow Product Slider --}}
            <div class="flex-1 min-w-0 flex items-center justify-center" style="max-width:50%;">
                <div id="cf-wrap" style="position:relative;width:100%;height:320px;perspective:1100px;overflow:visible;">
                    @php $heroProducts = $featuredProducts->take(6)->values(); @endphp

                    @foreach($heroProducts as $i => $product)
                    <a href="{{ route('shop.show', $product->slug) }}"
                       class="cf-card"
                       data-index="{{ $i }}"
                       style="position:absolute;left:50%;top:50%;transform:translateX(-50%) translateY(-50%);
                              width:195px;border-radius:20px;background:#fff;
                              box-shadow:0 8px 32px rgba(0,0,0,0.18);
                              overflow:hidden;text-decoration:none;cursor:pointer;
                              transition:all 0.5s cubic-bezier(.4,0,.2,1);
                              display:flex;flex-direction:column;">
                        <div style="height:200px;background:#f9fafb;display:flex;align-items:center;justify-content:center;padding:12px;">
                            @if($product->cover_image)
                                <img src="{{ Storage::url($product->cover_image) }}" alt="{{ $product->title }}"
                                     style="max-height:150px;max-width:100%;object-fit:contain;">
                            @else
                                <span style="font-size:48px;">📖</span>
                            @endif
                        </div>
                        <div style="padding:10px 12px 14px;">
                            @if($product->category)
                            <p style="font-size:9px;font-weight:700;text-transform:uppercase;color:#9ca3af;letter-spacing:0.06em;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $product->category->name }}</p>
                            @endif
                            <p style="font-size:12px;font-weight:700;color:#111;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;">{{ $product->title }}</p>
                            <p style="font-size:15px;font-weight:900;color:#111;letter-spacing:-0.3px;">{{ config('bookstore.currency_symbol') }}{{ number_format($product->selling_price, 0) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>

                <style>
                .cf-card { will-change: transform, opacity; }
                </style>

                <script>
                (function(){
                    const cards = Array.from(document.querySelectorAll('.cf-card'));
                    const total = cards.length;
                    if(!total) return;
                    let active = 0;

                    // Position config for each relative offset from center
                    function getStyle(offset) {
                        const abs = Math.abs(offset);
                        if (abs === 0) return {
                            translateX: '-50%', translateY: '-50%',
                            translateZ: '0px', rotateY: '0deg',
                            scale: 1, opacity: 1, zIndex: 10
                        };
                        if (abs === 1) return {
                            translateX: offset < 0 ? 'calc(-50% - 145px)' : 'calc(-50% + 145px)',
                            translateY: '-50%',
                            translateZ: '-90px', rotateY: offset < 0 ? '30deg' : '-30deg',
                            scale: 0.80, opacity: 0.88, zIndex: 7
                        };
                        if (abs === 2) return {
                            translateX: offset < 0 ? 'calc(-50% - 255px)' : 'calc(-50% + 255px)',
                            translateY: '-50%',
                            translateZ: '-180px', rotateY: offset < 0 ? '48deg' : '-48deg',
                            scale: 0.62, opacity: 0.50, zIndex: 4
                        };
                        return {
                            translateX: offset < 0 ? 'calc(-50% - 340px)' : 'calc(-50% + 340px)',
                            translateY: '-50%',
                            translateZ: '-260px', rotateY: offset < 0 ? '60deg' : '-60deg',
                            scale: 0.48, opacity: 0, zIndex: 1
                        };
                    }

                    function update() {
                        cards.forEach((card, i) => {
                            let offset = i - active;
                            // Wrap around for circular
                            if (offset > total / 2) offset -= total;
                            if (offset < -total / 2) offset += total;
                            const s = getStyle(offset);
                            card.style.transform = `translateX(${s.translateX}) translateY(${s.translateY}) translateZ(${s.translateZ}) rotateY(${s.rotateY}) scale(${s.scale})`;
                            card.style.opacity = s.opacity;
                            card.style.zIndex = s.zIndex;
                            card.style.pointerEvents = offset === 0 ? 'auto' : 'none';
                        });
                    }

                    function next() { active = (active + 1) % total; update(); }
                    function prev() { active = (active - 1 + total) % total; update(); }

                    // Click on side cards to bring them to center
                    cards.forEach((card, i) => {
                        card.addEventListener('click', (e) => {
                            const offset = i - active;
                            if (offset !== 0) { e.preventDefault(); active = i; update(); }
                        });
                    });

                    update();
                    setInterval(next, 2500);
                })();
                </script>
            </div>

        </div>
    </div>
</section>

{{-- ===== TRUST BAR ===== --}}
<div class="bg-black text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-white/10">
            <div class="flex items-center gap-3 py-3 px-4">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                </div>
                <div>
                    <p class="text-xs font-black text-white leading-none">Free Shipping</p>
                    <p class="text-[10px] text-white/40 leading-tight mt-0.5">Above {{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 py-3 px-4">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-black text-white leading-none">100% Genuine</p>
                    <p class="text-[10px] text-white/40 leading-tight mt-0.5">Authentic originals</p>
                </div>
            </div>
            <div class="flex items-center gap-3 py-3 px-4">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-black text-white leading-none">Cash on Delivery</p>
                    <p class="text-[10px] text-white/40 leading-tight mt-0.5">Pay at your door</p>
                </div>
            </div>
            <div class="flex items-center gap-3 py-3 px-4">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-black text-white leading-none">24/7 Support</p>
                    <p class="text-[10px] text-white/40 leading-tight mt-0.5">Always here for you</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== CATEGORIES ===== --}}
@if($categories->isNotEmpty())
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-7 bg-primary rounded-full"></div>
            <h2 class="text-2xl font-black text-gray-900">Shop by Category</h2>
        </div>
        <div class="flex items-center gap-2">
            <button id="cat-prev" class="w-7 h-7 rounded-full bg-gray-100 hover:bg-primary hover:text-black flex items-center justify-center transition-colors" aria-label="Previous">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button id="cat-next" class="w-7 h-7 rounded-full bg-gray-100 hover:bg-primary hover:text-black flex items-center justify-center transition-colors" aria-label="Next">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
            <a href="{{ route('shop.index') }}" class="text-xs font-bold text-gray-400 hover:text-black transition flex items-center gap-1">
                View all <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- Single carousel for ALL screen sizes --}}
    <div id="cat-carousel" style="display:flex; gap:12px; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; padding-bottom:8px; scrollbar-width:none; -ms-overflow-style:none;">
        @foreach($categories as $category)
        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
           style="flex-shrink:0; scroll-snap-align:start; width:150px; display:flex; flex-direction:column; align-items:center; gap:8px; text-align:center; padding:16px; background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07); border:1px solid #f3f4f6; text-decoration:none; transition:box-shadow 0.2s, border-color 0.2s;"
           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)';this.style.borderColor='#feee00';"
           onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.07)';this.style.borderColor='#f3f4f6';">
            <div style="width:52px;height:52px;border-radius:12px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f9fafb;">
                @if($category->image)
                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" style="width:100%;height:100%;object-fit:contain;padding:4px;">
                @else
                <span style="font-size:24px;">📚</span>
                @endif
            </div>
            <div>
                <p style="font-size:14px;font-weight:700;color:#1f2937;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $category->name }}</p>
                <p style="font-size:10px;color:#9ca3af;margin-top:2px;">{{ $category->products_count }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>

<style>#cat-carousel::-webkit-scrollbar { display: none; }</style>

<script>
(function () {
    const carousel = document.getElementById('cat-carousel');
    const prev = document.getElementById('cat-prev');
    const next = document.getElementById('cat-next');
    if (!carousel || !prev || !next) return;
    const scrollAmt = 320;
    next.addEventListener('click', () => carousel.scrollBy({ left: scrollAmt, behavior: 'smooth' }));
    prev.addEventListener('click', () => carousel.scrollBy({ left: -scrollAmt, behavior: 'smooth' }));
})();
</script>
@endif

{{-- ===== FEATURED PRODUCTS ===== --}}
@if($featuredProducts->isNotEmpty())
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-7 bg-primary rounded-full"></div>
                <div>
                    <h2 class="text-2xl font-black text-gray-900">Featured Books</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Hand-picked by our editors</p>
                </div>
            </div>
            <a href="{{ route('shop.index') }}" class="text-xs font-bold text-gray-400 hover:text-black transition flex items-center gap-1">
                View all <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @foreach($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ===== PROMO STRIP ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-black rounded-2xl p-5 sm:p-6 flex items-center gap-5 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-32 h-full opacity-5">
                <svg viewBox="0 0 100 100" class="w-full h-full"><circle cx="80" cy="20" r="60" fill="white"/></svg>
            </div>
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center flex-shrink-0 text-2xl shadow-lg">📦</div>
            <div>
                <p class="text-primary text-[11px] font-black uppercase tracking-widest mb-0.5">Free Delivery</p>
                <p class="text-white font-black text-base leading-tight">On orders above {{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}</p>
                <p class="text-white/40 text-xs mt-1">Fast delivery across UAE</p>
            </div>
        </div>
        <div class="bg-primary rounded-2xl p-5 sm:p-6 flex items-center gap-5 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-32 h-full opacity-10">
                <svg viewBox="0 0 100 100" class="w-full h-full"><circle cx="80" cy="20" r="60" fill="black"/></svg>
            </div>
            <div class="w-12 h-12 bg-black rounded-xl flex items-center justify-center flex-shrink-0 text-2xl shadow-lg">💳</div>
            <div>
                <p class="text-black/50 text-[11px] font-black uppercase tracking-widest mb-0.5">Easy Payment</p>
                <p class="text-black font-black text-base leading-tight">Cash on Delivery Available</p>
                <p class="text-black/50 text-xs mt-1">Pay when you receive</p>
            </div>
        </div>
    </div>
</div>

{{-- ===== NEW ARRIVALS ===== --}}
@if($newArrivals->isNotEmpty())
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-7 bg-black rounded-full"></div>
            <div>
                <h2 class="text-2xl font-black text-gray-900">New Arrivals</h2>
                <p class="text-xs text-gray-400 mt-0.5">Fresh titles just added</p>
            </div>
        </div>
        <a href="{{ route('shop.index', ['sort' => 'latest']) }}" class="text-xs font-bold text-gray-400 hover:text-black transition flex items-center gap-1">
            View all <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @foreach($newArrivals as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</div>
@endif

{{-- ===== BLOG ===== --}}
@if($latestBlogs->isNotEmpty())
<div class="bg-white py-8 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-7 bg-primary rounded-full"></div>
                <div>
                    <h2 class="text-2xl font-black text-gray-900">Reading Corner</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Tips, reviews, and inspiration</p>
                </div>
            </div>
            <a href="{{ route('blog.index') }}" class="text-xs font-bold text-gray-400 hover:text-black transition flex items-center gap-1">
                All Articles <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($latestBlogs as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col bg-gray-50 rounded-2xl overflow-hidden hover:shadow-md transition-all duration-200 border border-gray-100 hover:border-gray-200">
                @if($post->cover_image)
                <div class="w-full h-44 flex-shrink-0 overflow-hidden">
                    <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="w-full h-44 flex-shrink-0 bg-primary/20 flex items-center justify-center text-5xl">📰</div>
                @endif
                <div class="p-4 flex flex-col flex-1">
                    <p class="text-[11px] font-black uppercase tracking-wider text-gray-400 mb-2">{{ $post->published_at?->format('d M Y') }}</p>
                    <h3 class="font-black text-sm sm:text-base leading-snug text-gray-900 group-hover:text-black transition-colors line-clamp-2 mb-2">{{ $post->title }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed flex-1">{{ $post->excerpt }}</p>
                    <span class="inline-flex items-center gap-1 text-xs font-black text-black mt-3 group-hover:gap-2 transition-all">
                        Read more <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
