@extends('layouts.app')
@section('meta_title', 'Blog — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-playfair font-bold text-gray-900">Reading Corner</h1>
        <p class="text-gray-500 mt-3 max-w-xl mx-auto">Book reviews, reading tips, and literary inspiration from our editors.</p>
    </div>
    @if($posts->isEmpty())
    <div class="text-center py-20"><div class="text-6xl mb-4">📰</div><p class="text-gray-500">No posts yet. Check back soon!</p></div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col">
            @if($post->cover_image)
            <div class="overflow-hidden h-52">
                <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
            @else
            <div class="h-52 bg-gradient-to-br from-primary to-orange-100 flex items-center justify-center text-5xl">📰</div>
            @endif
            <div class="p-6 flex flex-col flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-xs text-primary font-semibold">{{ $post->published_at?->format('d M Y') }}</span>
                    @if($post->author)<span class="text-xs text-gray-400">by {{ $post->author->name }}</span>@endif
                </div>
                <h2 class="font-playfair font-bold text-gray-900 text-xl mb-3 group-hover:text-secondary transition-colors line-clamp-2 leading-snug">{{ $post->title }}</h2>
                @if($post->excerpt)<p class="text-sm text-gray-500 line-clamp-3 flex-1 leading-relaxed">{{ $post->excerpt }}</p>@endif
                <div class="mt-4 flex items-center text-primary text-sm font-semibold">
                    Read Article <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
