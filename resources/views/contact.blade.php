@extends('layouts.app')
@section('meta_title', 'Contact Us — ' . config('bookstore.store_name'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-playfair font-bold text-gray-900">Get In Touch</h1>
        <p class="text-gray-500 mt-3">We'd love to hear from you. Drop us a message!</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div class="space-y-8">
            <h2 class="text-xl font-playfair font-bold text-gray-900 mb-4">Contact Information</h2>
            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-yellow-50 rounded-xl">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Email</p>
                        <p class="text-sm text-gray-800">{{ config('bookstore.store_email') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-yellow-50 rounded-xl">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">{{ isset($storeSettings) && $storeSettings->mobile_number ? $storeSettings->mobile_number : config('bookstore.store_phone') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-yellow-50 rounded-xl">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Address</p>
                        <p class="text-sm text-gray-800">{{ isset($storeSettings) && $storeSettings->address ? $storeSettings->address : config('bookstore.store_address') }}</p>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-8">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-6 text-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            <form action="{{ route('leads.store') }}" method="POST"
                class="">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">

                    <!-- Name -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Your Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="Enter your name"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="Enter your email"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Phone Number *</label>
                        <div class="flex mt-2">
                            <select name="phone_code"
                                class="rounded-l-xl border border-gray-200 px-3 py-3 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
                                @php $codes = config('country_codes', ['+971']); @endphp
                                @foreach($codes as $code)
                                    <option value="{{ $code }}" @selected(old('phone_code', '+971') === $code)>
                                        {{ $code }}
                                    </option>
                                @endforeach
                            </select>

                            <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required
                                placeholder="Enter phone number"
                                class="w-full rounded-r-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Subject -->
                    <!-- <div>
                        <label class="text-sm font-medium text-gray-600">Subject</label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                            placeholder="Optional"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div> -->

                </div>

                <!-- Message -->
                <div>
                    <label class="text-sm font-medium text-gray-600">Message *</label>
                    <textarea name="message" rows="5" required
                        placeholder="How can we help you?"
                        class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition resize-none">{{ old('message') }}</textarea>
                    @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Button -->
                <div class="text-center pt-4">
                    <button type="submit" class="w-full bg-primary text-white py-3 rounded-full font-semibold hover:bg-secondary transition shadow-md text-sm">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div>
    @if(isset($storeSettings) && $storeSettings->map_embed)
    <div class="mt-8 rounded-xl overflow-hidden shadow-sm w-full bg-gray-100">
        {!! $storeSettings->map_embed !!}
    </div>
    @endif
</div>


@endsection
