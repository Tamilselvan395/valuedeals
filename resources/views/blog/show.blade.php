@extends('layouts.app')
@section('meta_title', ($post->meta_title ?? $post->title) . ' — ' . config('bookstore.store_name'))
@section('meta_description', $post->meta_description ?? $post->excerpt)

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">

    {{-- Back --}}
    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-black transition mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Back to Blog
    </a>

    <article class="bg-white rounded-xl shadow-sm overflow-hidden">
        {{-- Cover Image --}}
        @if($post->cover_image)
        <div class="w-full max-h-72 sm:max-h-96 overflow-hidden">
            <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
        @endif

        <div class="p-4 sm:p-8">
            {{-- Meta --}}
            <div class="flex items-center gap-3 mb-4 flex-wrap">
                <span class="text-xs bg-primary font-bold px-3 py-1 rounded-full text-black">{{ $post->published_at?->format('d M Y') }}</span>
                @if($post->author)<span class="text-xs text-gray-400 font-semibold">by {{ $post->author->name }}</span>@endif
            </div>

            <h1 style="font-size:25px;font-weight:900;color:#111827;line-height:1.3;margin-bottom:12px;">{{ $post->title }}</h1>
            @if($post->excerpt)
            <p style="font-size:16px;color:#6b7280;line-height:1.7;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid #f3f4f6;">{{ $post->excerpt }}</p>
            @endif

            <div class="prose max-w-none">
                <style>
                    .blog-content p  { font-size: 16px !important; color: #374151; line-height: 1.8; margin-bottom: 14px; }
                    .blog-content h1 { font-size: 25px !important; font-weight: 900; color: #111827; margin: 20px 0 10px; }
                    .blog-content h2, .blog-content h3, .blog-content h4, .blog-content h5, .blog-content h6 { font-size: 20px !important; font-weight: 800; color: #111827; margin: 20px 0 8px; }
                    .blog-content ul, .blog-content ol { font-size: 16px; color: #374151; padding-left: 20px; margin-bottom: 14px; }
                    .blog-content li { margin-bottom: 6px; line-height: 1.7; }
                    .blog-content a  { color: #000; font-weight: 700; text-decoration: underline; }
                    .blog-content blockquote { border-left: 3px solid #feee00; padding-left: 14px; color: #6b7280; font-size: 16px; }
                </style>
                <div class="blog-content">
                    {!! $post->content !!}
                </div>
            </div>
        </div>
    </article>

    {{-- Related Posts --}}
    @if($relatedPosts->isNotEmpty())
    <div class="mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-1 h-5 bg-primary rounded-full"></span>
            <h2 class="text-base font-bold text-gray-900">More Articles</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach($relatedPosts as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="group noon-card overflow-hidden flex flex-col">
                @if($related->cover_image)
                <div class="w-full flex-shrink-0 overflow-hidden">
                    <img src="{{ Storage::url($related->cover_image) }}" alt="{{ $related->title }}" class="w-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="w-full flex-shrink-0 bg-primary/20 flex items-center justify-center text-2xl py-6">📰</div>
                @endif
                <div class="p-3">
                    <p class="text-[10px] text-gray-400 mb-1 font-medium">{{ $related->published_at?->format('d M Y') }}</p>
                    <h3 class="text-xs font-semibold text-gray-800 group-hover:text-black transition-colors line-clamp-2">{{ $related->title }}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
