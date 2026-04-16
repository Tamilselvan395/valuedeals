@extends('layouts.app')
@section('meta_title', ($activeCategory ? $activeCategory->name : ($activeTag ? $activeTag->name : 'Shop Books')) . ' — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">

    {{-- Page Title --}}
    <div class="mb-4">
        <h1 class="text-xl sm:text-2xl font-black text-gray-900">
            @if($activeCategory) {{ $activeCategory->name }}
            @elseif($activeTag) Tagged: "{{ $activeTag->name }}"
            @else All Books
            @endif
        </h1>
        <p class="text-gray-500 text-xs sm:text-sm mt-0.5">{{ $products->total() }} results</p>
    </div>

    {{-- Mobile Filter Toggle --}}
    <div class="lg:hidden mb-3">
        <button onclick="document.getElementById('mobile-filters').classList.toggle('hidden')"
            class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm w-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filters & Categories
            <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">

        {{-- SIDEBAR --}}
        <aside class="lg:col-span-1">
            <div id="mobile-filters" class="hidden lg:block bg-white rounded-xl shadow-sm p-4 lg:sticky lg:top-36">
                {{-- Categories --}}
                <div class="mb-5">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3">Categories</h3>
                    <ul class="space-y-0.5 max-h-60 overflow-y-auto">
                        <li>
                            <a href="{{ route('shop.index') }}" class="flex items-center justify-between px-2 py-1.5 rounded-lg text-sm {{ !$activeCategory ? 'bg-primary font-bold text-black' : 'text-gray-700 hover:bg-gray-50 transition' }}">
                                <span>All Categories</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('shop.category', $cat->slug) }}" class="flex items-center justify-between px-2 py-1.5 rounded-lg text-sm {{ ($activeCategory && $activeCategory->id === $cat->id) ? 'bg-primary font-bold text-black' : 'text-gray-700 hover:bg-gray-50 transition' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $cat->products_count }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <hr class="my-4">

                {{-- Price Filter --}}
                <form method="GET" action="{{ url()->current() }}" id="filter-form">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3">Price Range</h3>
                    <div class="flex gap-2 mb-3">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                    </div>
                    <button type="submit" class="w-full bg-black text-white py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition">Apply</button>
                    @if(request()->anyFilled(['search','min_price','max_price']))
                    <a rel="nofollow" href="{{ url()->current() }}" class="block text-center text-xs text-gray-400 hover:text-red-500 mt-2 transition">Clear Filters</a>
                    @endif
                    <input type="hidden" name="sort" value="{{ request('sort','latest') }}">
                </form>

                <hr class="my-4">

                {{-- Tags --}}
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3">Tags</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($tags as $tag)
                        <a href="{{ route('shop.tag', $tag->slug) }}"
                            class="text-xs px-2.5 py-1 rounded-full border font-medium {{ ($activeTag && $activeTag->id === $tag->id) ? 'bg-primary border-primary text-black' : 'border-gray-200 text-gray-600 hover:border-black hover:text-black' }} transition">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- PRODUCTS --}}
        <div class="lg:col-span-3">
            {{-- Sort Bar --}}
            <div class="flex items-center justify-between mb-4 bg-white rounded-xl px-3 sm:px-4 py-2.5 shadow-sm">
                <p class="text-xs sm:text-sm text-gray-500">
                    Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900">{{ $products->total() }}</span>
                </p>
                <form method="GET" action="{{ url()->current() }}">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="text-xs sm:text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-primary focus:outline-none bg-white font-medium">
                        <option value="latest" {{ request('sort','latest') === 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>A → Z</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                <div class="text-5xl mb-4">📭</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">No books found</h3>
                <p class="text-gray-400 mb-5 text-sm">Try adjusting your filters or search.</p>
                <a href="{{ route('shop.index') }}" class="bg-primary text-black px-6 py-2 rounded-lg font-bold hover:bg-yellow-400 transition text-sm">Browse All Books</a>
            </div>
            @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-6">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
