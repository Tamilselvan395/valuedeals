<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;

class HomeController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        $featuredProducts = Product::active()->inStock()->featured()
            ->with(['images', 'category'])->latest()->take(8)->get();

        $newArrivals = Product::active()->inStock()
            ->with(['images', 'category'])->latest()->take(8)->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')->take(6)->get();

        $latestBlogs = BlogPost::published()
            ->latest('published_at')->take(3)->get();

        return view('home', compact('featuredProducts', 'newArrivals', 'categories', 'latestBlogs'));
    }
}
