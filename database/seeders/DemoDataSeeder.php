<?php

namespace Database\Seeders;

use App\Models\AbandonedCart;
use App\Models\BlogPost;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', 'admin')->first();
        if (! $admin) {
            $this->command->error('Run AdminSeeder first (admin user missing).');

            return;
        }

        $customer = User::query()->firstOrCreate(
            ['email' => 'customer@bookstore.com'],
            [
                'name'              => 'Demo Customer',
                'password'          => 'password',
                'email_verified_at' => now(),
            ]
        );

        $categories = collect([
            ['name' => 'Fiction', 'slug' => 'fiction', 'description' => 'Novels and stories'],
            ['name' => 'Non-Fiction', 'slug' => 'non-fiction', 'description' => 'Biographies, history, and more'],
            ['name' => 'Children', 'slug' => 'children', 'description' => 'Books for young readers'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Leadership and entrepreneurship'],
        ])->map(fn (array $row) => Category::query()->firstOrCreate(
            ['slug' => $row['slug']],
            [
                'name'        => $row['name'],
                'description' => $row['description'],
                'is_active'   => true,
            ]
        ));

        $tags = collect(['Bestseller', 'Award Winner', 'New Arrival', 'Classic', 'UAE Pick'])
            ->map(fn (string $name) => Tag::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            ));

        $books = [
            [
                'title'            => 'The Great Gatsby',
                'slug'             => 'the-great-gatsby',
                'author'           => 'F. Scott Fitzgerald',
                'isbn'             => '978-0-7432-7356-5',
                'category_slug'    => 'fiction',
                'description'      => 'A portrait of the Jazz Age in all of its decadence and excess.',
                'full_description' => 'The story of the mysteriously wealthy Jay Gatsby and his love for Daisy Buchanan.',
                'price'            => 45.00,
                'discount_price'   => 35.00,
                'stock'            => 24,
                'is_featured'      => true,
                'tag_slugs'        => ['classic', 'bestseller'],
            ],
            [
                'title'            => '1984',
                'slug'             => '1984',
                'author'           => 'George Orwell',
                'isbn'             => '978-0-452-28423-4',
                'category_slug'    => 'fiction',
                'description'      => 'A dystopian social science fiction novel and cautionary tale.',
                'full_description' => 'Winston Smith lives under the totalitarian rule of the Party led by Big Brother.',
                'price'            => 39.00,
                'discount_price'   => null,
                'stock'            => 50,
                'is_featured'      => true,
                'tag_slugs'        => ['classic', 'award-winner'],
            ],
            [
                'title'            => 'Sapiens',
                'slug'             => 'sapiens',
                'author'           => 'Yuval Noah Harari',
                'isbn'             => '978-0-06-231609-7',
                'category_slug'    => 'non-fiction',
                'description'      => 'A brief history of humankind.',
                'full_description' => 'Explores how Homo sapiens came to dominate the world.',
                'price'            => 55.00,
                'discount_price'   => 49.00,
                'stock'            => 18,
                'is_featured'      => false,
                'tag_slugs'        => ['bestseller', 'uae-pick'],
            ],
            [
                'title'            => 'The Very Hungry Caterpillar',
                'slug'             => 'the-very-hungry-caterpillar',
                'author'           => 'Eric Carle',
                'isbn'             => '978-0-3992-3090-7',
                'category_slug'    => 'children',
                'description'      => 'A beloved picture book about a caterpillar\'s transformation.',
                'full_description' => 'Follows a caterpillar as it eats its way through the week.',
                'price'            => 28.00,
                'discount_price'   => null,
                'stock'            => 40,
                'is_featured'      => true,
                'tag_slugs'        => ['classic', 'new-arrival'],
            ],
            [
                'title'            => 'Atomic Habits',
                'slug'             => 'atomic-habits',
                'author'           => 'James Clear',
                'isbn'             => '978-0-7352-1129-2',
                'category_slug'    => 'business',
                'description'      => 'Tiny changes, remarkable results.',
                'full_description' => 'Practical strategies for forming good habits and breaking bad ones.',
                'price'            => 52.00,
                'discount_price'   => 42.00,
                'stock'            => 30,
                'is_featured'      => true,
                'tag_slugs'        => ['bestseller', 'uae-pick'],
            ],
            [
                'title'            => 'Dune',
                'slug'             => 'dune',
                'author'           => 'Frank Herbert',
                'isbn'             => '978-0-4411-7271-9',
                'category_slug'    => 'fiction',
                'description'      => 'Epic science fiction set on the desert planet Arrakis.',
                'full_description' => 'Paul Atreides leads a rebellion on the planet Dune.',
                'price'            => 48.00,
                'discount_price'   => null,
                'stock'            => 15,
                'is_featured'      => false,
                'tag_slugs'        => ['classic', 'award-winner'],
            ],
            [
                'title'            => 'Educated',
                'slug'             => 'educated',
                'author'           => 'Tara Westover',
                'isbn'             => '978-0-3995-9050-9',
                'category_slug'    => 'non-fiction',
                'description'      => 'A memoir about leaving survivalism for education.',
                'full_description' => 'The author\'s journey from isolation to Cambridge and Harvard.',
                'price'            => 44.00,
                'discount_price'   => 38.00,
                'stock'            => 22,
                'is_featured'      => false,
                'tag_slugs'        => ['bestseller'],
            ],
            [
                'title'            => 'Goodnight Moon',
                'slug'             => 'goodnight-moon',
                'author'           => 'Margaret Wise Brown',
                'isbn'             => '978-0-06-443017-9',
                'category_slug'    => 'children',
                'description'      => 'A gentle bedtime story for little ones.',
                'full_description' => 'A bunny says goodnight to everything around the room.',
                'price'            => 22.00,
                'discount_price'   => null,
                'stock'            => 60,
                'is_featured'      => false,
                'tag_slugs'        => ['classic'],
            ],
        ];

        $products = collect();

        foreach ($books as $row) {
            $category = $categories->firstWhere('slug', $row['category_slug']);
            $product = Product::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'category_id'       => $category->id,
                    'title'             => $row['title'],
                    'description'       => $row['description'],
                    'full_description'  => $row['full_description'],
                    'author'            => $row['author'],
                    'isbn'              => $row['isbn'],
                    'price'             => $row['price'],
                    'discount_price'    => $row['discount_price'],
                    'stock'             => $row['stock'],
                    'is_active'         => true,
                    'is_featured'       => $row['is_featured'],
                    'cover_image'       => null,
                    'meta_title'        => $row['title'].' — BookStore',
                    'meta_description'  => Str::limit($row['description'], 155),
                ]
            );

            $tagIds = $tags->whereIn('slug', $row['tag_slugs'])->pluck('id')->all();
            $product->tags()->sync($tagIds);

            $products->push($product);
        }

        $firstProduct = $products->first();
        ProductImage::query()->firstOrCreate(
            [
                'product_id' => $firstProduct->id,
                'image_path' => 'demo/gallery-1.jpg',
            ],
            [
                'alt_text'   => $firstProduct->title.' cover detail',
                'sort_order' => 0,
            ]
        );

        Cart::query()->where('user_id', $customer->id)->whereNull('session_id')->each(function (Cart $cart): void {
            $cart->items()->delete();
            $cart->delete();
        });

        $cart = Cart::query()->create(['user_id' => $customer->id, 'session_id' => null]);
        $p1 = $products->firstWhere('slug', 'atomic-habits');
        $p2 = $products->firstWhere('slug', 'the-great-gatsby');
        CartItem::query()->create([
            'cart_id'    => $cart->id,
            'product_id' => $p1->id,
            'quantity'   => 1,
            'unit_price' => $p1->selling_price,
        ]);
        CartItem::query()->create([
            'cart_id'    => $cart->id,
            'product_id' => $p2->id,
            'quantity'   => 2,
            'unit_price' => $p2->selling_price,
        ]);

        $order1 = Order::query()->updateOrCreate(
            ['order_number' => 'BS-DEMO-0001'],
            [
                'user_id'          => $customer->id,
                'status'           => Order::STATUS_DELIVERED,
                'payment_method'   => 'cod',
                'payment_status'   => 'paid',
                'coupon_code'      => null,
                'discount_amount'  => 0,
                'subtotal'         => 77.00,
                'shipping_cost'    => 15.00,
                'total'            => 92.00,
                'shipping_name'    => 'Demo Customer',
                'shipping_phone'   => '+971501112233',
                'shipping_email'   => $customer->email,
                'shipping_address' => 'Al Wasl Road, Villa 12',
                'shipping_city'    => 'Jumeirah',
                'shipping_state'   => 'dubai',
                'shipping_pincode' => '00000',
                'shipping_country' => 'UAE',
                'notes'            => 'Leave at reception.',
            ]
        );
        $order1->items()->delete();

        $oi1a = $products->firstWhere('slug', '1984');
        $oi1b = $products->firstWhere('slug', 'educated');
        OrderItem::query()->create([
            'order_id'       => $order1->id,
            'product_id'     => $oi1a->id,
            'product_title'  => $oi1a->title,
            'unit_price'     => $oi1a->price,
            'quantity'       => 1,
            'subtotal'       => $oi1a->price,
        ]);
        OrderItem::query()->create([
            'order_id'       => $order1->id,
            'product_id'     => $oi1b->id,
            'product_title'  => $oi1b->title,
            'unit_price'     => $oi1b->selling_price,
            'quantity'       => 1,
            'subtotal'       => $oi1b->selling_price,
        ]);

        $order2 = Order::query()->updateOrCreate(
            ['order_number' => 'BS-DEMO-0002'],
            [
                'user_id'          => $customer->id,
                'status'           => Order::STATUS_PROCESSING,
                'payment_method'   => 'cod',
                'payment_status'   => 'unpaid',
                'coupon_code'      => null,
                'discount_amount'  => 0,
                'subtotal'         => 97.00,
                'shipping_cost'    => 15.00,
                'total'            => 112.00,
                'shipping_name'    => 'Demo Customer',
                'shipping_phone'   => '+971501112233',
                'shipping_email'   => $customer->email,
                'shipping_address' => 'Business Bay, Tower A',
                'shipping_city'    => 'Business Bay',
                'shipping_state'   => 'dubai',
                'shipping_pincode' => '00000',
                'shipping_country' => 'UAE',
                'notes'            => null,
            ]
        );
        $order2->items()->delete();
        $oi2a = $products->firstWhere('slug', 'sapiens');
        $oi2b = $products->firstWhere('slug', 'dune');
        OrderItem::query()->create([
            'order_id'       => $order2->id,
            'product_id'     => $oi2a->id,
            'product_title'  => $oi2a->title,
            'unit_price'     => $oi2a->selling_price,
            'quantity'       => 1,
            'subtotal'       => $oi2a->selling_price,
        ]);
        OrderItem::query()->create([
            'order_id'       => $order2->id,
            'product_id'     => $oi2b->id,
            'product_title'  => $oi2b->title,
            'unit_price'     => $oi2b->price,
            'quantity'       => 1,
            'subtotal'       => $oi2b->price,
        ]);

        BlogPost::query()->firstOrCreate(
            ['slug' => 'welcome-to-bookstore-demo'],
            [
                'user_id'           => $admin->id,
                'title'             => 'Welcome to BookStore (demo post)',
                'excerpt'           => 'A quick tour of what you can test in this demo catalog.',
                'content'           => "<p>This is <strong>sample blog content</strong> for local testing. Try the storefront, cart, checkout, and admin panel.</p><p>Customer login: <code>customer@bookstore.com</code> / <code>password</code></p>",
                'cover_image'       => null,
                'meta_title'        => 'Welcome — BookStore',
                'meta_description'  => 'Demo blog post for testing.',
                'is_published'      => true,
                'published_at'      => now()->subDays(3),
            ]
        );

        BlogPost::query()->firstOrCreate(
            ['slug' => 'spring-reading-list-demo'],
            [
                'user_id'           => $admin->id,
                'title'             => 'Spring reading list ideas',
                'excerpt'           => 'Fiction and non-fiction picks for the season.',
                'content'           => '<p>Explore our <em>Fiction</em> and <em>Non-Fiction</em> categories for curated demo titles with realistic prices and stock.</p>',
                'cover_image'       => null,
                'meta_title'        => 'Spring reading list',
                'meta_description'  => 'Demo reading recommendations.',
                'is_published'      => true,
                'published_at'      => now()->subDay(),
            ]
        );

        if (Lead::query()->where('email', 'sarah.j@example.com')->doesntExist()) {
            Lead::query()->create([
                'name'    => 'Sarah Johnson',
                'email'   => 'sarah.j@example.com',
                'phone'   => '+971509998877',
                'subject' => 'Bulk order question',
                'message' => 'Do you offer discounts for orders over 50 books?',
                'is_read' => false,
            ]);
        }

        if (Lead::query()->where('email', 'm.ali@example.com')->doesntExist()) {
            Lead::query()->create([
                'name'    => 'Mohamed Ali',
                'email'   => 'm.ali@example.com',
                'phone'   => null,
                'subject' => 'Store hours',
                'message' => 'What are your delivery times to Abu Dhabi?',
                'is_read' => true,
            ]);
        }

        AbandonedCart::query()->updateOrCreate(
            ['session_id' => 'demo-abandoned-session'],
            [
                'user_id'          => null,
                'email'            => 'guest@example.com',
                'cart_data'        => [
                    ['title' => '1984', 'qty' => 1, 'price' => 39.00],
                    ['title' => 'Sapiens', 'qty' => 1, 'price' => 49.00],
                ],
                'cart_total'       => 88.00,
                'item_count'       => 2,
                'last_activity_at' => now()->subHours(6),
            ]
        );

        $this->command->newLine();
        $this->command->info('Demo catalog: '.$products->count().' books, 4 categories, '.$tags->count().' tags.');
        $this->command->info('Demo user: customer@bookstore.com / password (cart + 2 sample orders)');
        $this->command->info('Admin remains: admin@bookstore.com / password');
    }
}
