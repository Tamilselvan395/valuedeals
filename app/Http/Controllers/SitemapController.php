<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\BlogPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::active()->get();
        $categories = Category::where('is_active', true)->get();
        $posts = BlogPost::where('is_published', true)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Static routes
        $xml .= $this->createUrlNode(route('home'), '1.0', 'daily');
        $xml .= $this->createUrlNode(route('shop.index'), '0.9', 'daily');
        $xml .= $this->createUrlNode(route('blog.index'), '0.8', 'weekly');
        $xml .= $this->createUrlNode(route('contact'), '0.5', 'monthly');

        // Categories
        foreach ($categories as $category) {
            $xml .= $this->createUrlNode(route('shop.category', $category->slug), '0.8', 'weekly', $category->updated_at->toAtomString());
        }

        // Products
        foreach ($products as $product) {
            $xml .= $this->createUrlNode(route('shop.show', $product->slug), '0.9', 'daily', $product->updated_at->toAtomString());
        }

        // Blog Posts
        foreach ($posts as $post) {
            $xml .= $this->createUrlNode(route('blog.show', $post->slug), '0.7', 'monthly', $post->updated_at->toAtomString());
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function createUrlNode($loc, $priority, $changefreq, $lastmod = null)
    {
        $node = '<url>';
        $node .= '<loc>' . htmlspecialchars($loc) . '</loc>';
        if ($lastmod) {
            $node .= '<lastmod>' . $lastmod . '</lastmod>';
        }
        $node .= '<changefreq>' . $changefreq . '</changefreq>';
        $node .= '<priority>' . $priority . '</priority>';
        $node .= '</url>';
        return $node;
    }
}
