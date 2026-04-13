@extends('layouts.app')
@section('meta_title', ($post->meta_title ?? $post->title) . ' — ' . config('bookstore.store_name'))
@section('meta_description', $post->meta_description ?? $post->excerpt)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <a href="{{ route('blog.index') }}" class="text-sm text-primary hover:underline flex items-center gap-1 mb-6">← Back to Blog</a>
    <article>
        <header class="mb-8">
            <div class="flex items-center gap-3 mb-4 text-sm text-gray-500">
                <span>{{ $post->published_at?->format('d M Y') }}</span>
                @if($post->author)<span>&bull;</span><span>by {{ $post->author->name }}</span>@endif
            </div>
            <h1 class="text-4xl font-playfair font-bold text-gray-900 leading-tight mb-4">{{ $post->title }}</h1>
            @if($post->excerpt)<p class="text-lg text-gray-600 leading-relaxed">{{ $post->excerpt }}</p>@endif
        </header>
        @if($post->cover_image)
        <div class="rounded-2xl overflow-hidden mb-8 shadow-md">
            <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full  object-cover">
        </div>
        @endif
        <div class="prose prose-lg prose-amber max-w-none text-gray-700 leading-relaxed">
            {!! $post->content !!}
        </div>
    </article>
    @if($relatedPosts->isNotEmpty())
    <div class="mt-16 border-t border-gray-200 pt-10">
        <h2 class="text-2xl font-playfair font-bold text-gray-900 mb-6">More Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedPosts as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                @if($related->cover_image)
                <img src="{{ Storage::url($related->cover_image) }}" alt="{{ $related->title }}" class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                <div class="w-full h-36 bg-yellow-100 flex items-center justify-center text-3xl">📰</div>
                @endif
                <div class="p-4">
                    <p class="text-xs text-primary mb-1">{{ $related->published_at?->format('d M Y') }}</p>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-secondary transition-colors line-clamp-2">{{ $related->title }}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
