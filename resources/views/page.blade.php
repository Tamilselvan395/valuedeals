@extends('layouts.app')

@section('title', $page->title . ' - ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h1 style="font-size:20px;font-weight:900;color:#111827;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f3f4f6;">{{ $page->title }}</h1>
        <style>
            .cms-content p  { font-size: 14px; color: #4b5563; line-height: 1.75; margin-bottom: 12px; }
            .cms-content h1 { font-size: 18px !important; font-weight: 900; color: #111827; margin: 18px 0 8px; }
            .cms-content h2, .cms-content h3, .cms-content h4 { font-size: 16px !important; font-weight: 800; color: #111827; margin: 16px 0 6px; }
            .cms-content ul, .cms-content ol { font-size: 14px; color: #4b5563; padding-left: 18px; margin-bottom: 12px; }
            .cms-content li { margin-bottom: 4px; line-height: 1.65; }
            .cms-content a  { color: #000; font-weight: 600; text-decoration: underline; }
            .cms-content strong { color: #111827; }
        </style>
        <div class="cms-content">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection
