@extends('layouts.app')

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-yellow-50 via-yellow-50 to-yellow-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block bg-yellow-100 text-secondary text-xs font-semibold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">
                    Free shipping above {{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}
                </span>
                <h1 class="text-5xl lg:text-6xl font-playfair font-bold text-gray-900 leading-tight mb-5">
                    Discover Your<br><span class="text-primary">Next Great Read</span>
                </h1>
                <p class="text-lg text-gray-600 mb-8 max-w-md leading-relaxed">
                    Explore thousands of books across every genre. From bestsellers to hidden gems — find your story here.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('shop.index') }}" class="bg-primary text-white px-8 py-3 rounded-full font-semibold hover:bg-secondary transition shadow-lg">Browse Books</a>
                    <a href="{{ route('blog.index') }}" class="bg-white text-secondary border border-primary px-8 py-3 rounded-full font-semibold hover:bg-yellow-50 transition">Reading Blog</a>
                </div>
                <div class="flex gap-8 mt-10">
                    <div><p class="text-2xl font-bold text-gray-900">10K+</p><p class="text-sm text-gray-500">Books Available</p></div>
                    <div><p class="text-2xl font-bold text-gray-900">50K+</p><p class="text-sm text-gray-500">Happy Readers</p></div>
                    <div><p class="text-2xl font-bold text-gray-900">4.9★</p><p class="text-sm text-gray-500">Avg. Rating</p></div>
                </div>
            </div>
            <div class="hidden lg:flex justify-center relative w-full h-full items-center">
                <div class="relative w-full rounded-3xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition-all duration-500">
                    <img src="{{ asset('storage/default-images/book-store.png') }}" alt="Featured Reading" class="w-full h-auto object-contain">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
@if($categories->isNotEmpty())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-playfair font-bold text-gray-900">Browse by Category</h2>
            <p class="text-gray-500 mt-2">Find books in your favourite genre</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
               class="group flex flex-col items-center p-4 bg-yellow-50 rounded-2xl hover:bg-primary transition-colors duration-200 text-center">
                @if($category->image)
                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-14 h-14  object-cover mb-3">
                @else
                <div class="w-14 h-14 bg-yellow-100 group-hover:bg-primary rounded-full flex items-center justify-center mb-3 text-2xl transition-colors">📚</div>
                @endif
                <p class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors">{{ $category->name }}</p>
                <p class="text-xs text-gray-500 group-hover:text-yellow-100 transition-colors">{{ $category->products_count }} books</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- FEATURED PRODUCTS --}}
@if($featuredProducts->isNotEmpty())
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-playfair font-bold text-gray-900">Featured Books</h2>
                <p class="text-gray-500 mt-1">Hand-picked by our editors</p>
            </div>
            <a href="{{ route('shop.index') }}" class="text-primary font-semibold hover:text-secondary flex items-center gap-1">
                View All <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- NEW ARRIVALS --}}
@if($newArrivals->isNotEmpty())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-playfair font-bold text-gray-900">New Arrivals</h2>
                <p class="text-gray-500 mt-1">Fresh titles just added</p>
            </div>
            <a href="{{ route('shop.index', ['sort' => 'latest']) }}" class="text-primary font-semibold hover:text-secondary flex items-center gap-1">
                View All <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($newArrivals as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- SHIPPING BANNER --}}
<section class="py-10 bg-primary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-white text-center">
            <div class="flex flex-col items-center gap-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <p class="font-semibold">Free Shipping</p>
                <p class="text-sm text-yellow-100">On orders above {{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'), 0) }}</p>
            </div>
            <div class="flex flex-col items-center gap-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-semibold">100% Genuine</p>
                <p class="text-sm text-yellow-100">All books are authentic originals</p>
            </div>
            <div class="flex flex-col items-center gap-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <p class="font-semibold">Cash on Delivery</p>
                <p class="text-sm text-yellow-100">Pay when you receive your books</p>
            </div>
        </div>
    </div>
</section>

{{-- BLOG --}}
@if($latestBlogs->isNotEmpty())
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-playfair font-bold text-gray-900">From Our Blog</h2>
            <p class="text-gray-500 mt-2">Reading tips, reviews, and more</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestBlogs as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                @if($post->cover_image)
                <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                <div class="w-full h-48 bg-yellow-100 flex items-center justify-center text-4xl">📰</div>
                @endif
                <div class="p-5">
                    <p class="text-xs text-primary font-semibold mb-2">{{ $post->published_at?->format('d M Y') }}</p>
                    <h3 class="font-playfair font-bold text-gray-900 text-lg mb-2 group-hover:text-secondary transition-colors line-clamp-2">{{ $post->title }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $post->excerpt }}</p>
                </div>
            </a>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('blog.index') }}" class="inline-block border border-primary text-primary px-8 py-3 rounded-full font-semibold hover:bg-primary hover:text-white transition">Read All Articles</a>
        </div>
    </div>
</section>
@endif
@endsection
