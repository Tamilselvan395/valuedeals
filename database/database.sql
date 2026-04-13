-- Readable SQL snapshot (SQLite dialect) from the former local SQLite file — for reference only.
-- The running app uses MySQL (see .env). Apply schema with: php artisan migrate
-- For a MySQL text dump, install mysqldump (MySQL client) and run: php artisan schema:dump --path=database/database.sql
PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;

CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);

CREATE TABLE sqlite_sequence(name,seq);

CREATE TABLE "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "is_admin" tinyint(1) not null default '0');

CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));

CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);

CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));

CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);

CREATE TABLE "categories" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar not null, "description" text, "image" varchar, "is_active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime);

CREATE TABLE "tags" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "products" ("id" integer primary key autoincrement not null, "category_id" integer not null, "title" varchar not null, "slug" varchar not null, "description" text, "full_description" text, "author" varchar, "isbn" varchar, "price" numeric not null, "discount_price" numeric, "stock" integer not null default '0', "cover_image" varchar, "meta_title" varchar, "meta_description" text, "is_active" tinyint(1) not null default '1', "is_featured" tinyint(1) not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("category_id") references "categories"("id") on delete cascade);

CREATE TABLE "product_images" ("id" integer primary key autoincrement not null, "product_id" integer not null, "image_path" varchar not null, "alt_text" varchar, "sort_order" integer not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("product_id") references "products"("id") on delete cascade);

CREATE TABLE "product_tag" ("product_id" integer not null, "tag_id" integer not null, foreign key("product_id") references "products"("id") on delete cascade, foreign key("tag_id") references "tags"("id") on delete cascade, primary key ("product_id", "tag_id"));

CREATE TABLE "carts" ("id" integer primary key autoincrement not null, "user_id" integer, "session_id" varchar, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete set null);

CREATE TABLE "cart_items" ("id" integer primary key autoincrement not null, "cart_id" integer not null, "product_id" integer not null, "quantity" integer not null default '1', "unit_price" numeric not null, "created_at" datetime, "updated_at" datetime, foreign key("cart_id") references "carts"("id") on delete cascade, foreign key("product_id") references "products"("id") on delete cascade);

CREATE TABLE "orders" ("id" integer primary key autoincrement not null, "user_id" integer not null, "order_number" varchar not null, "status" varchar check ("status" in ('pending', 'processing', 'shipped', 'delivered', 'cancelled')) not null default 'pending', "payment_method" varchar check ("payment_method" in ('cod', 'stripe')) not null default 'cod', "payment_status" varchar check ("payment_status" in ('unpaid', 'paid', 'failed')) not null default 'unpaid', "stripe_session_id" varchar, "stripe_payment_intent_id" varchar, "subtotal" numeric not null, "shipping_cost" numeric not null default '0', "total" numeric not null, "shipping_name" varchar not null, "shipping_phone" varchar not null, "shipping_email" varchar not null, "shipping_address" text not null, "shipping_city" varchar not null, "shipping_state" varchar, "shipping_pincode" varchar not null, "shipping_country" varchar not null default 'UAE', "notes" text, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade);

CREATE TABLE "order_items" ("id" integer primary key autoincrement not null, "order_id" integer not null, "product_id" integer not null, "product_title" varchar not null, "unit_price" numeric not null, "quantity" integer not null, "subtotal" numeric not null, "created_at" datetime, "updated_at" datetime, foreign key("order_id") references "orders"("id") on delete cascade, foreign key("product_id") references "products"("id") on delete restrict);

CREATE TABLE "leads" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "phone" varchar, "subject" varchar, "message" text not null, "is_read" tinyint(1) not null default '0', "created_at" datetime, "updated_at" datetime);

CREATE TABLE "blog_posts" ("id" integer primary key autoincrement not null, "user_id" integer, "title" varchar not null, "slug" varchar not null, "excerpt" text, "content" text not null, "cover_image" varchar, "meta_title" varchar, "meta_description" text, "is_published" tinyint(1) not null default '0', "published_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete set null);

CREATE TABLE "abandoned_carts" ("id" integer primary key autoincrement not null, "user_id" integer, "session_id" varchar, "email" varchar, "cart_data" text not null, "cart_total" numeric not null default '0', "item_count" integer not null default '0', "last_activity_at" datetime not null, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete set null);

CREATE UNIQUE INDEX "users_email_unique" on "users" ("email");

CREATE INDEX "sessions_user_id_index" on "sessions" ("user_id");

CREATE INDEX "sessions_last_activity_index" on "sessions" ("last_activity");

CREATE INDEX "jobs_queue_index" on "jobs" ("queue");

CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs" ("uuid");

CREATE UNIQUE INDEX "categories_slug_unique" on "categories" ("slug");

CREATE UNIQUE INDEX "tags_slug_unique" on "tags" ("slug");

CREATE UNIQUE INDEX "products_slug_unique" on "products" ("slug");

CREATE INDEX "carts_session_id_index" on "carts" ("session_id");

CREATE UNIQUE INDEX "orders_order_number_unique" on "orders" ("order_number");

CREATE UNIQUE INDEX "blog_posts_slug_unique" on "blog_posts" ("slug");

INSERT INTO "cache" ("key","value","expiration") VALUES ('bookstore_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1775741023;', '1775741023');
INSERT INTO "cache" ("key","value","expiration") VALUES ('bookstore_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', '1775741023');
INSERT INTO "cache" ("key","value","expiration") VALUES ('bookstore_cache_356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1775741301;', '1775741301');
INSERT INTO "cache" ("key","value","expiration") VALUES ('bookstore_cache_356a192b7913b04c54574d18c28d46e6395428ab', 'i:2;', '1775741301');

INSERT INTO "carts" ("id","user_id","session_id","created_at","updated_at") VALUES ('1', NULL, '9rF4Zhiz85HZUuuIftRQ2QXKuefbqRZwrr6vikri', '2026-04-09 13:20:50', '2026-04-09 13:20:50');
INSERT INTO "carts" ("id","user_id","session_id","created_at","updated_at") VALUES ('2', '1', NULL, '2026-04-09 13:22:43', '2026-04-09 13:22:43');

INSERT INTO "categories" ("id","name","slug","description","image","is_active","created_at","updated_at") VALUES ('1', 'test', 'test', 'asdfasd', 'categories/01KNS6RJFQMZHWBQGSN729TDJ7.png', '1', '2026-04-09 13:26:32', '2026-04-09 13:26:32');

INSERT INTO "migrations" ("id","migration","batch") VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('4', '2024_01_01_000000_add_is_admin_to_users_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('5', '2024_01_01_000001_create_categories_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('6', '2024_01_01_000002_create_tags_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('7', '2024_01_01_000003_create_products_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('8', '2024_01_01_000004_create_product_images_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('9', '2024_01_01_000005_create_product_tag_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('10', '2024_01_01_000006_create_carts_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('11', '2024_01_01_000007_create_cart_items_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('12', '2024_01_01_000008_create_orders_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('13', '2024_01_01_000009_create_order_items_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('14', '2024_01_01_000010_create_leads_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('15', '2024_01_01_000011_create_blog_posts_table', '2');
INSERT INTO "migrations" ("id","migration","batch") VALUES ('16', '2024_01_01_000012_create_abandoned_carts_table', '2');

INSERT INTO "order_items" ("id","order_id","product_id","product_title","unit_price","quantity","subtotal","created_at","updated_at") VALUES ('1', '1', '1', 'book site ', '100', '1', '100', '2026-04-09 13:28:45', '2026-04-09 13:28:45');

INSERT INTO "orders" ("id","user_id","order_number","status","payment_method","payment_status","stripe_session_id","stripe_payment_intent_id","subtotal","shipping_cost","total","shipping_name","shipping_phone","shipping_email","shipping_address","shipping_city","shipping_state","shipping_pincode","shipping_country","notes","created_at","updated_at") VALUES ('1', '1', 'BS-69D7A98D9EE61', 'pending', 'cod', 'unpaid', NULL, NULL, '100', '60', '160', 'BookStore Admin', '9344620346', 'admin@bookstore.com', 'main road , Anthamangalam', 'kumbakoma,', 'Tamil Nadu', '612101', 'UAE', 'asdf', '2026-04-09 13:28:45', '2026-04-09 13:28:45');

INSERT INTO "product_images" ("id","product_id","image_path","alt_text","sort_order","created_at","updated_at") VALUES ('1', '1', 'products/gallery/01KNS6TYMAQ013E2SS0DWA8JRB.png', NULL, '0', '2026-04-09 13:27:50', '2026-04-09 13:27:50');

INSERT INTO "products" ("id","category_id","title","slug","description","full_description","author","isbn","price","discount_price","stock","cover_image","meta_title","meta_description","is_active","is_featured","created_at","updated_at") VALUES ('1', '1', 'book site ', 'book-site', NULL, '<p>asdfasd</p>', NULL, NULL, '120', '100', '49', 'products/covers/01KNS6TYM4XQPKKBBX6AVPN328.jpeg', NULL, NULL, '1', '1', '2026-04-09 13:27:50', '2026-04-09 13:28:45');

INSERT INTO "users" ("id","name","email","email_verified_at","password","remember_token","created_at","updated_at","is_admin") VALUES ('1', 'BookStore Admin', 'admin@bookstore.com', '2026-04-09 13:17:44', '$2y$12$jRD6PPSlt6.O4v9s6r82RuRy0NSU4O3IMAnnIdoQDmuPLixRM.CmW', NULL, '2026-04-09 13:17:44', '2026-04-09 13:17:44', '1');

COMMIT;
