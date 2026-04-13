<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return $this->renderShop($request);
    }

    public function category(Request $request, $slug)
    {
        return $this->renderShop($request, $slug, null);
    }

    public function tag(Request $request, $slug)
    {
        return $this->renderShop($request, null, $slug);
    }

    private function renderShop(Request $request, $categorySlug = null, $tagSlug = null)
    {
        $query = Product::active()->inStock()->with(['images', 'category']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($tagSlug) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc'   => $query->orderBy('title'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('products')->get();
        $tags       = Tag::withCount('products')->orderByDesc('products_count')->take(20)->get();

        $activeCategory = $categorySlug ? Category::where('slug', $categorySlug)->first() : null;
        $activeTag = $tagSlug ? Tag::where('slug', $tagSlug)->first() : null;

        return view('shop.index', compact('products', 'categories', 'tags', 'activeCategory', 'activeTag'));
    }
}
