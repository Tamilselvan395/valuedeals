<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SitemapController;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/category/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/shop/tag/{slug}', [ShopController::class, 'tag'])->name('shop.tag');
Route::get('/shop/{slug}', [ProductController::class, 'show'])->name('shop.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/contact', [LeadController::class, 'show'])->name('contact');
Route::post('/contact', [LeadController::class, 'store'])->name('leads.store');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',                        [CartController::class, 'index'])->name('index');
    Route::post('/add',                    [CartController::class, 'add'])->name('add');
    Route::patch('/update/{cartItemId}',   [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItemId}',  [CartController::class, 'remove'])->name('remove');
    Route::post('/coupon',                 [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon',               [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

// ─── Auth Required ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/shipping-quote', [CheckoutController::class, 'shippingQuote'])->name('checkout.shipping-quote');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/stripe/success', [CheckoutController::class, 'stripeSuccess'])->name('checkout.stripe.success');
    Route::get('/checkout/stripe/cancel', [CheckoutController::class, 'stripeCancel'])->name('checkout.stripe.cancel');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',                    [OrderController::class, 'index'])->name('index');
        Route::get('/{order}',             [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/invoice',     [OrderController::class, 'invoice'])->name('invoice');
        Route::post('/{order}/cancel',     [OrderController::class, 'cancel'])->name('cancel');
    });
});

require __DIR__ . '/auth.php';

// ─── Pages Catch-all (Must be last) ───────────────────────────────────────────
Route::get('/{slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');
