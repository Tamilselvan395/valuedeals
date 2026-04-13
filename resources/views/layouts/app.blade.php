<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="currency-symbol" content="{{ config('bookstore.currency_symbol') }}">
    
    <title>@yield('meta_title', config('bookstore.store_name') . (isset($storeSettings) && $storeSettings->seo_title ? ' - ' . $storeSettings->seo_title : ' — Your Online Book Store'))</title>
    <meta name="description" content="@yield('meta_description', 'Discover thousands of books at great prices at ' . config('bookstore.store_name') . '.')">
    
    @if(isset($storeSettings) && $storeSettings->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $storeSettings->favicon_path) }}">
    @endif
    
    {{-- SEO Canonical & Robots --}}
    @if(request()->anyFilled(['sort', 'min_price', 'max_price', 'search']) || request()->has('page'))
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow">
    @endif
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    {{-- Social Open Graph Tags --}}
    <meta property="og:title" content="@yield('meta_title', config('bookstore.store_name'))">
    <meta property="og:description" content="@yield('meta_description', 'Discover thousands of books at great prices.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ config('bookstore.store_name') }}">
    <meta property="og:image" content="@yield('og_image', isset($storeSettings) && $storeSettings->seo_og_image ? asset('storage/' . $storeSettings->seo_og_image) : '')">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', config('bookstore.store_name'))">
    <meta name="twitter:description" content="@yield('meta_description', 'Discover thousands of books at great prices.')">
    <meta name="twitter:image" content="@yield('og_image', isset($storeSettings) && $storeSettings->seo_twitter_image ? asset('storage/' . $storeSettings->seo_twitter_image) : '')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Dynamic Structured Data (JSON-LD) --}}
    @stack('schema')
</head>
<body class="bg-gray-50 text-gray-800 font-inter antialiased">

@if(session('success'))
<div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('error') }}
</div>
@endif

<nav class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @if(isset($storeSettings) && $storeSettings->logo_path)
                    <img src="{{ asset('storage/' . $storeSettings->logo_path) }}" alt="{{ config('bookstore.store_name') }}" class="h-8 object-contain">
                @else
                    <span class="text-2xl font-black text-primary">VALUE <span class="text-secondary">Deals</span></span>
                @endif
            </a>
            <form action="{{ route('shop.index') }}" method="GET" class="hidden md:flex flex-1 max-w-lg mx-8">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search products..."
                        class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>
            <div class="flex items-center gap-4">
                <a href="{{ route('shop.index') }}" class="hidden md:block text-sm font-medium text-gray-600 hover:text-primary transition">Shop</a>
                <a href="{{ route('blog.index') }}" class="hidden md:block text-sm font-medium text-gray-600 hover:text-primary transition">Blog</a>
                <a href="{{ route('contact') }}" class="hidden md:block text-sm font-medium text-gray-600 hover:text-primary transition">Contact</a>
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span id="cart-count" class="absolute -top-1 -right-1 bg-primary text-secondary text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                        {{ app(\App\Services\CartService::class)->getCartCount() }}
                    </span>
                </a>
                @auth
                <div class="relative group">
                    <button class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-primary">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center text-secondary font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <div class="p-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            My Orders
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary transition">Login</a>
                <a href="{{ route('register') }}" class="text-sm font-semibold bg-primary text-secondary px-4 py-2 rounded-full hover:bg-yellow-400 transition">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="pb-20 md:pb-0">@yield('content')</main>

<footer class="bg-secondary relative text-gray-300 mt-20 pt-10 md:pt-20 pb-16 md:pb-0 overflow-hidden">
    <!-- Decorative Glow Effects -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-70"></div>
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 relative z-10">
        <div class="flex flex-col md:flex-row md:flex-nowrap justify-between gap-12 lg:gap-8">
            <!-- Brand Column -->
            <div class="w-full md:flex-[2] md:w-auto">
                <a href="{{ route('home') }}" class="inline-block mb-6 group">
                    @if(isset($storeSettings) && $storeSettings->logo_path)
                        <img src="{{ asset('storage/' . $storeSettings->logo_path) }}" alt="{{ config('bookstore.store_name') }}" class="h-12 object-contain group-hover:scale-105 transition-transform duration-300 brightness-0 invert">
                    @else
                        <h3 class="text-4xl font-black text-primary tracking-tight group-hover:drop-shadow-[0_0_8px_rgba(var(--color-primary),0.5)] transition-all duration-300">VALUE <span class="text-white">Deals</span></h3>
                    @endif
                </a>
                <p class="text-base leading-relaxed text-gray-400 mb-8 pr-4">Your one-stop destination for premium products. Experience world-class quality with unparalleled customer service and lightning-fast delivery.</p>
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-sm text-primary font-medium text-sm hover:bg-white/10 transition-colors cursor-default shadow-[0_0_15px_rgba(0,0,0,0.1)]">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    24/7 Premium Support
                </div>
            </div>

            <!-- Quick Links -->
            <div class="w-full md:flex-1 md:w-auto relative group/nav">
                <h3 class="text-md font-bold text-white mb-6  relative inline-block">
                    Discover
                    
                </h3>
                <ul class="space-y-4 text-sm mt-4">
                    <li><a href="{{ route('shop.index') }}" class="text-gray-400 hover:text-white hover:translate-x-2 transition-all flex items-center gap-2 group/link"><span class="w-1.5 h-1.5 rounded-full bg-primary opacity-0 -ml-3 group-hover/link:opacity-100 transition-all duration-300"></span> Shop Collection</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-gray-400 hover:text-white hover:translate-x-2 transition-all flex items-center gap-2 group/link"><span class="w-1.5 h-1.5 rounded-full bg-primary opacity-0 -ml-3 group-hover/link:opacity-100 transition-all duration-300"></span> Journal</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white hover:translate-x-2 transition-all flex items-center gap-2 group/link"><span class="w-1.5 h-1.5 rounded-full bg-primary opacity-0 -ml-3 group-hover/link:opacity-100 transition-all duration-300"></span> Contact Us</a></li>
                    @auth<li><a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-white hover:translate-x-2 transition-all flex items-center gap-2 group/link"><span class="w-1.5 h-1.5 rounded-full bg-primary opacity-0 -ml-3 group-hover/link:opacity-100 transition-all duration-300"></span> My Account</a></li>@endauth
                </ul>
            </div>

            <!-- Legal / Info -->
            <div class="w-full md:flex-1 md:w-auto relative group/nav">
                <h3 class="text-md font-bold text-white mb-6  relative inline-block">
                    Legal
                    
                </h3>
                <ul class="space-y-4 text-sm mt-4">
                    @php $cmsPages = \App\Models\Page::where('is_active', true)->get(); @endphp
                    @foreach($cmsPages as $page)
                        <li><a href="{{ route('page.show', $page->slug) }}" class="text-gray-400 hover:text-white hover:translate-x-2 transition-all flex items-center gap-2 group/link"><span class="w-1.5 h-1.5 rounded-full bg-primary opacity-0 -ml-3 group-hover/link:opacity-100 transition-all duration-300"></span> {{ $page->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Contact -->
            <div class="w-full md:flex-[1.5] md:w-auto relative group/nav">
                <h3 class="text-md font-bold text-white mb-6  relative inline-block">
                    Connect
                    
                </h3>
                <ul class="space-y-5 text-sm text-gray-400 mt-4">
                    <li class="flex items-start gap-4 group cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-secondary group-hover:border-primary group-hover:-rotate-6 transition-all duration-300 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-[10px] text-gray-500 mb-0.5 uppercase tracking-widest font-bold">Email Us</p>
                            <span class="text-sm font-medium group-hover:text-white transition-colors block">{{ config('bookstore.store_email') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-4 group cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-secondary group-hover:border-primary group-hover:rotate-6 transition-all duration-300 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-[10px] text-gray-500 mb-0.5 uppercase tracking-widest font-bold">Call Us</p>
                            <span class="text-sm font-medium group-hover:text-white transition-colors block">{{ isset($storeSettings) && $storeSettings->mobile_number ? $storeSettings->mobile_number : config('bookstore.store_phone') }}</span>
                        </div>
                    </li>
                </ul>

                @if(isset($storeSettings) && ($storeSettings->facebook_url || $storeSettings->instagram_url || $storeSettings->twitter_url))
                <div class="mt-8 flex gap-3">
                    @if($storeSettings->facebook_url)
                    <a href="{{ $storeSettings->facebook_url }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-primary hover:text-secondary hover:-translate-y-1 hover:shadow-[0_4px_15px_rgba(var(--color-primary),0.4)] transition-all duration-300 group/social">
                        <span class="sr-only">Facebook</span>
                        <svg class="w-4 h-4 group-hover/social:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                    </a>
                    @endif
                    @if($storeSettings->instagram_url)
                    <a href="{{ $storeSettings->instagram_url }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-primary hover:text-secondary hover:-translate-y-1 hover:shadow-[0_4px_15px_rgba(var(--color-primary),0.4)] transition-all duration-300 group/social">
                        <span class="sr-only">Instagram</span>
                        <svg class="w-4 h-4 group-hover/social:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    @endif
                    @if($storeSettings->twitter_url)
                    <a href="{{ $storeSettings->twitter_url }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-primary hover:text-secondary hover:-translate-y-1 hover:shadow-[0_4px_15px_rgba(var(--color-primary),0.4)] transition-all duration-300 group/social">
                        <span class="sr-only">Twitter</span>
                        <svg class="w-4 h-4 group-hover/social:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-6 pb-6 relative z-10">
            <p class="text-sm text-gray-500 tracking-wide">© {{ date('Y') }} <span class="text-gray-300">{{ config('bookstore.store_name') }}</span>. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Navigation -->
<div class="fixed bottom-0 left-0 right-0 bg-secondary border-t border-secondary z-50 md:hidden shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.15)] pb-safe">
    <div class="flex justify-between items-center h-16 w-full px-2">
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center flex-1 h-full text-primary opacity-60 hover:opacity-100 {{ request()->routeIs('home') ? '!opacity-100 font-bold' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] tracking-wider">Home</span>
        </a>
        <a href="{{ route('shop.index') }}" class="flex flex-col items-center justify-center flex-1 h-full text-primary opacity-60 hover:opacity-100 {{ request()->routeIs('shop.*') ? '!opacity-100 font-bold' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px] tracking-wider">Shop</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center flex-1 h-full text-primary opacity-60 hover:opacity-100 {{ request()->routeIs('cart.*') ? '!opacity-100 font-bold' : '' }}">
            <div class="relative flex flex-col items-center">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span id="mobile-cart-badge" class="absolute -top-1.5 -right-2 bg-red-600 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center font-bold">{{ app(\App\Services\CartService::class)->getCartCount() }}</span>
            </div>
            <span class="text-[10px] tracking-wider">Cart</span>
        </a>
        <a href="{{ auth()->check() ? route('orders.index') : route('login') }}" class="flex flex-col items-center justify-center flex-1 h-full text-primary opacity-60 hover:opacity-100 {{ request()->routeIs('orders.*') || request()->routeIs('login') ? '!opacity-100 font-bold' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] tracking-wider">Profile</span>
        </a>
    </div>
</div>

<script>
setTimeout(() => {
    ['flash-success','flash-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}, 4000);
</script>
@stack('scripts')
</body>
</html>
