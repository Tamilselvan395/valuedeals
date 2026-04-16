@extends('layouts.app')
@section('meta_title', 'Blog — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">

    {{-- Header --}}
    <div class="bg-primary rounded-xl p-5 sm:p-8 mb-6 text-center">
        <h1 class="text-2xl sm:text-3xl font-black text-black mb-2">Reading Corner</h1>
        <p class="text-sm text-black/60 max-w-md mx-auto">Book reviews, reading tips, and literary inspiration from our editors.</p>
    </div>

    @if($posts->isEmpty())
    <div class="text-center py-16 bg-white rounded-xl shadow-sm">
        <div class="text-5xl mb-4">📰</div>
        <p class="text-gray-400">No posts yet. Check back soon!</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}" class="group noon-card flex flex-col overflow-hidden">
            @if($post->cover_image)
            <div class="w-full overflow-hidden">
                <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
            @else
            <div class="w-full bg-primary/20 flex items-center justify-center text-5xl py-10">📰</div>
            @endif
            <div class="p-4 flex flex-col flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] bg-primary/20 text-black font-bold px-2 py-0.5 rounded-full">{{ $post->published_at?->format('d M Y') }}</span>
                    @if($post->author)<span class="text-[10px] text-gray-400">by {{ $post->author->name }}</span>@endif
                </div>
                <h2 class="font-black text-gray-900 text-sm sm:text-base mb-2 line-clamp-2 leading-snug group-hover:text-black transition-colors">{{ $post->title }}</h2>
                @if($post->excerpt)<p class="text-xs text-gray-500 line-clamp-2 flex-1 leading-relaxed">{{ $post->excerpt }}</p>@endif
                <div class="mt-3 flex items-center text-xs font-bold text-black gap-1">
                    Read Article
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
