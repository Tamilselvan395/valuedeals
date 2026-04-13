# 📚 BookStore — Laravel 11 eCommerce Project

A full-featured, production-ready Laravel 11 book store with Filament v3 admin panel, AJAX cart, PDF invoices, blog, abandoned cart tracking, and more.

---

## Tech Stack

- **Laravel 11** + PHP 8.3+
- **Blade** + **Tailwind CSS**
- **Laravel Breeze** (authentication)
- **Filament v3** (admin panel)
- **MySQL**
- **barryvdh/laravel-dompdf** (PDF invoices)
- Service Layer + Clean Architecture

---

## Features

### Frontend (User)
- Home page with hero, featured books, categories, new arrivals, blog
- Shop page with filters (category, price, tags), search, sorting, pagination
- Product detail page with image gallery, stock status, quantity selector
- AJAX Add-to-Cart (no page reload)
- Cart page with live quantity update and item removal
- Checkout page (Cash on Delivery)
- Order history with status tracker
- PDF Invoice download
- Blog listing and detail pages
- Contact / Lead form
- Cart merges on login (guest → user)
- Free shipping above configurable threshold

### Admin (Filament v3)
- Dashboard with revenue chart, order stats, product stats, customer stats
- Recent orders table widget
- Product CRUD (cover image, gallery, SEO, tags, category, stock)
- Category CRUD
- Tag CRUD
- Order management (view items, update status, bulk actions)
- Lead management (contact submissions, mark read/unread)
- Blog post CRUD (rich editor, scheduled publish)
- Abandoned cart viewer
- Navigation badges (pending orders, unread leads, out-of-stock products)

---

## Installation

### 1. Create Laravel project
```bash
composer create-project laravel/laravel bookstore
cd bookstore
```

### 2. Copy all source files into the project
Copy the contents of this zip into your Laravel project root, **merging** folders (do not replace the entire project — just the files from this zip).

### 3. Install PHP packages
```bash
composer require filament/filament:"^3.0" laravel/breeze barryvdh/laravel-dompdf
```

### 4. Install Breeze (Blade stack)
```bash
php artisan breeze:install blade
```

### 5. Install Filament
```bash
php artisan filament:install --panels
```
When asked for panel ID, type: `admin`

### 6. Install Node packages
```bash
npm install
npm install -D @tailwindcss/typography
```

### 7. Configure .env
```env
APP_NAME="BookStore"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookstore
DB_USERNAME=root
DB_PASSWORD=your_password

FREE_SHIPPING_THRESHOLD=500
FLAT_SHIPPING_RATE=60
STORE_PHONE="+91 9999999999"
STORE_ADDRESS="Chennai, Tamil Nadu, India"
```

### 8. Create database
```sql
CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 9. Run migrations and seed admin
```bash
php artisan key:generate
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan storage:link
```

### 10. Build assets
```bash
npm run build
# Or for development:
npm run dev
```

### 11. Start server
```bash
php artisan serve
```

---

## Access

| URL | Description |
|-----|-------------|
| http://localhost:8000 | Frontend (shop) |
| http://localhost:8000/admin | Admin panel |
| http://localhost:8000/register | User registration |

**Admin login:**
- Email: `admin@bookstore.com`
- Password: `password`

---

## Scheduled Commands

Add to your server crontab to enable abandoned cart detection:
```bash
* * * * * cd /path/to/bookstore && php artisan schedule:run >> /dev/null 2>&1
```

Or run manually:
```bash
php artisan bookstore:store-abandoned-carts
```

---

## Project Structure

```
app/
├── Console/Commands/StoreAbandonedCarts.php
├── Filament/
│   ├── Resources/          # 7 Filament resources
│   └── Widgets/            # Stats + Recent Orders
├── Http/
│   ├── Controllers/        # 8 controllers
│   └── Requests/           # CheckoutRequest, LeadRequest
├── Listeners/MergeCartOnLogin.php
├── Models/                 # 11 Eloquent models
├── Policies/OrderPolicy.php
├── Providers/
│   ├── AppServiceProvider.php
│   └── Filament/AdminPanelProvider.php
└── Services/               # CartService, OrderService, InvoiceService

config/bookstore.php
database/
├── migrations/             # 13 migration files
└── seeders/AdminSeeder.php
resources/
├── css/app.css
├── js/
│   ├── app.js
│   └── cart.js             # AJAX cart logic
└── views/
    ├── layouts/app.blade.php
    ├── partials/product-card.blade.php
    ├── home.blade.php
    ├── shop/               # index + show
    ├── cart/               # index
    ├── checkout/           # index
    ├── orders/             # index + show + invoice (PDF)
    ├── blog/               # index + show
    └── contact.blade.php
routes/web.php
tailwind.config.js
```

---

## Configuration

Edit `config/bookstore.php` to customize:
```php
'free_shipping_threshold' => 500,   // Free shipping above ₹500
'flat_shipping_rate'      => 60,    // Flat ₹60 shipping
'currency_symbol'         => '₹',
```

---

## Notes

- All orders use **Cash on Delivery** (COD)
- Invoices are generated as PDF using DomPDF
- Cart is session-based for guests, DB-persisted for logged-in users
- Guest cart automatically merges with user cart on login
- Abandoned carts are detected after 1 hour of inactivity
- Products support multiple gallery images, tags (many-to-many), SEO fields
