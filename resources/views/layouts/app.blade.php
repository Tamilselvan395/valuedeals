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
    
    @if(request()->anyFilled(['sort', 'min_price', 'max_price', 'search']) || request()->has('page'))
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow">
    @endif
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <meta property="og:title" content="@yield('meta_title', config('bookstore.store_name'))">
    <meta property="og:description" content="@yield('meta_description', 'Discover thousands of books at great prices.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ config('bookstore.store_name') }}">
    <meta property="og:image" content="@yield('og_image', isset($storeSettings) && $storeSettings->seo_og_image ? asset('storage/' . $storeSettings->seo_og_image) : '')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('schema')

    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; font-size: 19px; }
        .noon-header { background: #feee00; }
        .noon-card { background: #fff; border-radius: 12px; transition: box-shadow 0.2s, transform 0.2s; }
        .noon-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.10); transform: translateY(-2px); }
        .noon-badge-off { background: #dcfce7; color: #16a34a; font-size: 14px; font-weight: 800; padding: 2px 7px; border-radius: 5px; white-space: nowrap; }
        .noon-btn-cart { background: #feee00; color: #000; font-weight: 800; border: none; border-radius: 10px; cursor: pointer; transition: background 0.15s, transform 0.1s; }
        .noon-btn-cart:hover { background: #f5e500; }
        .noon-btn-cart:active { transform: scale(0.95); }
        .noon-search { border-radius: 10px; border: 2px solid transparent; outline: none; transition: border-color 0.2s; font-size: 17.5px; }
        .noon-search:focus { border-color: #000; box-shadow: none; }
        .noon-category-bar { background: #fff; border-bottom: 1px solid #eee; }
        .noon-section-title { font-size: 32px; font-weight: 900; color: #111; letter-spacing: -0.3px; }
        .price-main { font-size: 21px; font-weight: 900; color: #111; letter-spacing: -0.2px; }
        .price-old { font-size: 15px; color: #aaa; text-decoration: line-through; }
    </style>
</head>
<body class="antialiased">

{{-- Flash Messages --}}
@if(session('success'))
<div id="flash-success" class="fixed top-16 right-4 z-50 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 text-sm font-medium">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div id="flash-error" class="fixed top-16 right-4 z-50 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 text-sm font-medium">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ===== MAIN HEADER ===== --}}
<header class="noon-header sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center h-14 sm:h-16 gap-3">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                @if(isset($storeSettings) && $storeSettings->logo_path)
                    <img src="{{ asset('storage/' . $storeSettings->logo_path) }}" alt="{{ config('bookstore.store_name') }}" class="h-7 sm:h-9 object-contain">
                @else
                    <span class="text-xl sm:text-2xl font-black text-black tracking-tight leading-none">value<span class="text-gray-600">deals</span></span>
                @endif
            </a>

            {{-- Search Bar (desktop) --}}
            <form action="{{ route('shop.index') }}" method="GET" class="flex-1 mx-3 hidden sm:flex">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search for books, authors, categories..."
                        class="noon-search w-full pl-4 pr-12 py-2.5 text-sm bg-white border-0 shadow-sm">
                    <button type="submit" class="absolute right-0 top-0 h-full px-4 bg-black text-white rounded-r-lg hover:bg-gray-800 transition flex items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>

            {{-- Right Icons --}}
            <div class="flex items-center gap-1 sm:gap-3 ml-auto sm:ml-0">
                {{-- Mobile Search --}}
                <!-- <a href="{{ route('shop.index') }}" class="sm:hidden p-2 flex flex-col items-center text-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </a> -->

                @auth
                {{-- Account --}}
                <div class="relative group hidden sm:flex flex-col items-center">
                    <button class="p-1.5 flex flex-col items-center text-black hover:bg-black/10 rounded-lg transition">
                        <div class="w-6 h-6 bg-black text-white rounded-full flex items-center justify-center text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <span class="text-[10px] font-semibold mt-0.5">Account</span>
                    </button>
                    <div class="absolute right-0 top-full mt-1 w-52 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            My Orders
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="hidden sm:flex flex-col items-center p-1.5 text-black hover:bg-black/10 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-[10px] font-semibold mt-0.5">Sign In</span>
                </a>
                <a href="{{ route('register') }}" class="hidden sm:block bg-black text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap">Register</a>
                @endauth

                {{-- Orders (desktop) --}}
                @auth
                <a href="{{ route('orders.index') }}" class="hidden sm:flex flex-col items-center p-1.5 text-black hover:bg-black/10 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="text-[10px] font-semibold mt-0.5">Orders</span>
                </a>
                @endauth

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center p-1.5 text-black hover:bg-black/10 rounded-lg transition">
                    <div class="relative">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span id="cart-count" class="absolute -top-1.5 -right-1.5 bg-black text-white text-[9px] rounded-full w-4 h-4 flex items-center justify-center font-bold">{{ app(\App\Services\CartService::class)->getCartCount() }}</span>
                    </div>
                    <span class="text-[10px] font-semibold mt-0.5 hidden sm:block">Cart</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Mobile Search Bar --}}
    <div class="sm:hidden px-3 pb-2">
        <form action="{{ route('shop.index') }}" method="GET" class="flex">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search books..."
                class="flex-1 px-3 py-2 text-sm bg-white rounded-l-lg border-0 outline-none focus:ring-2 focus:ring-black/20">
            <button type="submit" class="px-3 py-2 bg-black text-white rounded-r-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </form>
    </div>
</header>

{{-- ===== CATEGORY NAV BAR ===== --}}
<nav class="noon-category-bar sticky top-14 sm:top-16 z-30">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-1 sm:gap-0 overflow-x-auto scrollbar-none py-2 sm:py-0">
            <a href="{{ route('shop.index') }}" class="flex-shrink-0 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 hover:text-black hover:bg-gray-50 transition whitespace-nowrap rounded sm:rounded-none border-b-2 {{ request()->routeIs('shop.index') && !request('category') ? 'border-black text-black' : 'border-transparent' }}">All</a>
            <a href="{{ route('blog.index') }}" class="flex-shrink-0 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 hover:text-black hover:bg-gray-50 transition whitespace-nowrap rounded sm:rounded-none border-b-2 border-transparent">Blog</a>
            <a href="{{ route('contact') }}" class="flex-shrink-0 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 hover:text-black hover:bg-gray-50 transition whitespace-nowrap rounded sm:rounded-none border-b-2 border-transparent">Contact</a>
        </div>
    </div>
</nav>

{{-- ===== MAIN CONTENT ===== --}}
<main class="pb-20 md:pb-6">@yield('content')</main>

{{-- ===== FOOTER ===== --}}
<footer style="background:#fff;color:#111;margin-top:2rem;border-top:1px solid #e5e7eb;">

    {{-- ── TOP STRIP: unique CTA banner ──────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,#feee00 0%,#f5d800 100%);padding:28px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="width:48px;height:48px;background:#111;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px;">📚</div>
                    <div>
                        <p style="font-size:18px;font-weight:900;color:#111;line-height:1.2;margin:0;">Discover Your Next Favourite Book</p>
                        <p style="font-size:13px;color:#444;margin:3px 0 0;">Free shipping on orders above {{ config('bookstore.currency_symbol') }}{{ number_format(config('bookstore.free_shipping_threshold'),0) }} · Fast UAE delivery</p>
                    </div>
                </div>
                <a href="{{ route('shop.index') }}"
                   style="background:#111;color:#feee00;font-weight:900;font-size:13px;padding:12px 28px;border-radius:12px;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center;gap:8px;transition:background 0.2s;"
                   onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111'">
                    Shop Now
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Trust chips --}}
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:18px;">
                @foreach([['🚚','Free Shipping'],['✅','100% Genuine'],['💳','Cash on Delivery'],['🔒','Secure Checkout']] as $chip)
                <span style="background:rgba(0,0,0,0.10);color:#111;font-size:11px;font-weight:800;padding:5px 12px;border-radius:20px;display:flex;align-items:center;gap:5px;">
                    {{ $chip[0] }} {{ $chip[1] }}
                </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── MAIN 4-COLUMN GRID ─────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:48px;padding-bottom:40px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:36px;">

            {{-- Col 1 · Brand --}}
            <div>
                <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;text-decoration:none;margin-bottom:16px;">
                    @if(isset($storeSettings) && $storeSettings->logo_path)
                        <img src="{{ asset('storage/' . $storeSettings->logo_path) }}" alt="{{ config('bookstore.store_name') }}" style="height:36px;object-fit:contain;">
                    @else
                        <span style="font-size:22px;font-weight:900;color:#111;">value<span style="color:#6b7280;">deals</span></span>
                    @endif
                </a>
                <p style="font-size:13px;color:#6b7280;line-height:1.7;margin:0 0 16px;">Your one-stop destination for premium books across the UAE. Genuine titles, fast delivery, best prices.</p>
                {{-- Rating badge --}}
                <div style="display:inline-flex;align-items:center;gap:8px;background:#f3f4f6;border-radius:10px;padding:8px 14px;">
                    <span style="font-size:14px;">⭐</span>
                    <div>
                        <p style="font-size:12px;font-weight:900;color:#111;margin:0;">4.9 / 5 Rating</p>
                        <p style="font-size:10px;color:#9ca3af;margin:0;">50,000+ happy readers</p>
                    </div>
                </div>
            </div>

            {{-- Col 2 · Shop --}}
            <div>
                <h3 style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:0.1em;color:#111;margin:0 0 18px;">Shop</h3>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="{{ route('shop.index') }}" style="color:#6b7280;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color 0.15s;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#6b7280'"><svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>All Books</a></li>
                    <li><a href="{{ route('shop.index',['sort'=>'latest']) }}" style="color:#6b7280;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color 0.15s;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#6b7280'"><svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>New Arrivals</a></li>
                    <li><a href="{{ route('blog.index') }}" style="color:#6b7280;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color 0.15s;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#6b7280'"><svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>Reading Blog</a></li>
                    @auth
                    <li><a href="{{ route('orders.index') }}" style="color:#6b7280;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color 0.15s;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#6b7280'"><svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>My Orders</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Col 3 · Help --}}
            <div>
                <h3 style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:0.1em;color:#111;margin:0 0 18px;">Help & Legal</h3>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="{{ route('contact') }}" style="color:#6b7280;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color 0.15s;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#6b7280'"><svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>Contact Us</a></li>
                    @php $cmsPages = \App\Models\Page::where('is_active', true)->get(); @endphp
                    @foreach($cmsPages as $page)
                    <li><a href="{{ route('page.show', $page->slug) }}" style="color:#6b7280;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;transition:color 0.15s;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#6b7280'"><svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>{{ $page->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 4 · Contact & Social --}}
            <div>
                <h3 style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:0.1em;color:#111;margin:0 0 18px;">Get in Touch</h3>
                <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:22px;">
                    <a href="mailto:{{ config('bookstore.store_email') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:#6b7280;font-size:13px;" onmouseover="this.style.color='#111'" onmouseout="this.style.color='#6b7280'">
                        <div style="width:32px;height:32px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        {{ config('bookstore.store_email') }}
                    </a>
                    <div style="display:flex;align-items:center;gap:10px;color:#6b7280;font-size:13px;">
                        <div style="width:32px;height:32px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        {{ isset($storeSettings) && $storeSettings->mobile_number ? $storeSettings->mobile_number : config('bookstore.store_phone') }}
                    </div>
                </div>

                {{-- Social icons --}}
                @if(isset($storeSettings) && ($storeSettings->facebook_url || $storeSettings->instagram_url || $storeSettings->twitter_url))
                <div>
                    <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 10px;">Follow Us</p>
                    <div style="display:flex;gap:10px;">
                        @if($storeSettings->facebook_url)
                        <a href="{{ $storeSettings->facebook_url }}" target="_blank" style="width:36px;height:36px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;" onmouseover="this.style.background='#feee00'" onmouseout="this.style.background='#f3f4f6'">
                            <svg style="width:16px;height:16px;color:#6b7280;" fill="currentColor" viewBox="0 0 24 24"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                        </a>
                        @endif
                        @if($storeSettings->instagram_url)
                        <a href="{{ $storeSettings->instagram_url }}" target="_blank" style="width:36px;height:36px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;" onmouseover="this.style.background='#feee00'" onmouseout="this.style.background='#f3f4f6'">
                            <svg style="width:16px;height:16px;color:#6b7280;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        @endif
                        @if($storeSettings->twitter_url)
                        <a href="{{ $storeSettings->twitter_url }}" target="_blank" style="width:36px;height:36px;background:#f3f4f6;border-radius:10px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;" onmouseover="this.style.background='#feee00'" onmouseout="this.style.background='#f3f4f6'">
                            <svg style="width:16px;height:16px;color:#6b7280;" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ── BOTTOM COPYRIGHT BAR ────────────────────────────────────────── --}}
    <div style="border-top:1px solid #e5e7eb;padding:16px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p style="font-size:12px;color:#9ca3af;margin:0;">© {{ date('Y') }} <span style="color:#6b7280;font-weight:700;">{{ config('bookstore.store_name') }}</span>. All rights reserved.</p>
        </div>
    </div>

</footer>
<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
</style>

{{-- ===== MOBILE BOTTOM NAV ===== --}}
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden shadow-lg">
    <div class="flex justify-around items-center h-14 px-2">
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 {{ request()->routeIs('home') ? 'text-black' : 'text-gray-400' }}">
            <svg class="w-5 h-5" fill="{{ request()->routeIs('home') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-semibold">Home</span>
        </a>
        <a href="{{ route('shop.index') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 {{ request()->routeIs('shop.*') ? 'text-black' : 'text-gray-400' }}">
            <svg class="w-5 h-5" fill="{{ request()->routeIs('shop.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px] font-semibold">Shop</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 {{ request()->routeIs('cart.*') ? 'text-black' : 'text-gray-400' }} relative">
            <div class="relative">
                <svg class="w-5 h-5" fill="{{ request()->routeIs('cart.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span id="mobile-cart-badge" class="absolute -top-1.5 -right-1.5 bg-black text-white text-[9px] rounded-full w-4 h-4 flex items-center justify-center font-bold">{{ app(\App\Services\CartService::class)->getCartCount() }}</span>
            </div>
            <span class="text-[10px] font-semibold">Cart</span>
        </a>
        <a href="{{ auth()->check() ? route('orders.index') : route('login') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-0.5 {{ request()->routeIs('orders.*') || request()->routeIs('login') ? 'text-black' : 'text-gray-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] font-semibold">{{ auth()->check() ? 'Orders' : 'Sign In' }}</span>
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
