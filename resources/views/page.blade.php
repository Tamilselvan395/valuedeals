@extends('layouts.app')

@section('title', $page->title . ' - ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12">
        <h1 class="text-3xl font-black text-gray-900 mb-8 border-b border-gray-100 pb-6">{{ $page->title }}</h1>
        <div class="prose prose-lg prose-primary max-w-none text-gray-700">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection
