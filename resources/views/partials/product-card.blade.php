<div class="group h-full bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col">
    <a href="{{ route('shop.show', $product->slug) }}" class="block overflow-hidden relative">
        @if($product->discount_percentage > 0)
        <span class="absolute top-2 left-2 z-10 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">-{{ $product->discount_percentage }}%</span>
        @endif
        @if($product->cover_image)
        <div class="w-full h-72 bg-white relative">
            <img src="{{ Storage::url($product->cover_image) }}" alt="{{ $product->title }}"
                 class="absolute inset-0 w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
        </div>
        @else
        <div class="w-full h-72 bg-yellow-50 flex items-center justify-center text-6xl group-hover:bg-yellow-100 transition-colors">📖</div>
        @endif
    </a>
    <div class="p-4 flex flex-col flex-1">
        @if($product->category)
        <span class="text-xs text-primary font-semibold uppercase tracking-wider mb-1">{{ $product->category->name }}</span>
        @endif
        <a href="{{ route('shop.show', $product->slug) }}">
            <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 hover:text-secondary transition-colors leading-snug">{{ $product->title }}</h3>
        </a>
        @if($product->author)
        <p class="text-xs text-gray-500 mb-3">by {{ $product->author }}</p>
        @endif
        <div class="mt-auto flex items-center justify-between">
            <div>
                <span class="text-lg font-bold text-gray-900">{{ config('bookstore.currency_symbol') }}{{ number_format($product->selling_price, 2) }}</span>
                @if($product->discount_price)
                <span class="text-sm text-gray-400 line-through ml-1">{{ config('bookstore.currency_symbol') }}{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            <button onclick="addToCart({{ $product->id }}, this)"
                class="bg-primary text-white text-xs font-semibold px-3 py-2 rounded-full hover:bg-secondary transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add
            </button>
        </div>
    </div>
</div>
