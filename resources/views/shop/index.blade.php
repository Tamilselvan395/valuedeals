@extends('layouts.app')
@section('meta_title', ($activeCategory ? $activeCategory->name : ($activeTag ? $activeTag->name : 'Shop Books')) . ' — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <h1 class="text-3xl font-playfair font-bold text-gray-900">
            @if($activeCategory) {{ $activeCategory->name }}
            @elseif($activeTag) Books tagged "{{ $activeTag->name }}"
            @else All Books
            @endif
        </h1>
        <p class="text-gray-500 mt-1">{{ $products->total() }} books found</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-5 sticky top-20">
                <div class="mb-6">
                    <h3 class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3">Categories</h3>
                    <ul class="space-y-2 max-h-52 overflow-y-auto pr-1">
                        <li>
                            <a href="{{ route('shop.index') }}" class="flex items-center justify-between text-sm {{ !$activeCategory ? 'font-bold text-primary' : 'text-gray-700 hover:text-primary transition' }}">
                                <span>All Categories</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('shop.category', $cat->slug) }}" class="flex items-center justify-between text-sm {{ ($activeCategory && $activeCategory->id === $cat->id) ? 'font-bold text-primary' : 'text-gray-700 hover:text-primary transition' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $cat->products_count }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <form method="GET" action="{{ url()->current() }}" id="filter-form">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3">Price Range</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg text-sm font-semibold hover:bg-secondary transition">Apply Filters</button>
                    @if(request()->anyFilled(['search','min_price','max_price']))
                    <a rel="nofollow" href="{{ url()->current() }}" class="block text-center text-sm text-gray-500 hover:text-red-500 mt-2 transition">Clear Filters</a>
                    @endif
                    <input type="hidden" name="sort" value="{{ request('sort','latest') }}">
                </form>

                <div class="mt-8 mb-2">
                    <h3 class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3">Popular Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                        <a href="{{ route('shop.tag', $tag->slug) }}"
                            class="text-xs px-3 py-1 rounded-full border {{ ($activeTag && $activeTag->id === $tag->id) ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 hover:border-primary hover:text-primary' }} transition">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>
        
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-6 bg-white rounded-xl px-4 py-3 shadow-sm">
                <p class="text-sm text-gray-600">Showing <span class="font-semibold">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $products->total() }}</span></p>
                <form method="GET" action="{{ url()->current() }}">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-primary focus:outline-none">
                        <option value="latest" {{ request('sort','latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>A–Z</option>
                    </select>
                </form>
            </div>
            
            @if($products->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No books found</h3>
                <p class="text-gray-500 mb-6">Try adjusting your search or filters.</p>
                <a href="{{ route('shop.index') }}" class="bg-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-secondary transition">Browse All Books</a>
            </div>
            @else
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
