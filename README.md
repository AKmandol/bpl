# E-commerce CRUD

A small Laravel storefront and admin area for managing product categories, subcategories, and products.

## Features

- Storefront at `/` with products grouped by category and subcategory navigation.
- Product listing pagination (12 products per page) and slug-based category, subcategory, and product detail URLs.
- Admin area at `/admin`, with paginated CRUD screens for categories, subcategories, and products (10 rows per page).
- Create and edit forms in Bootstrap modals, with delete confirmation modals.
- Product image upload, replacement, and cleanup when a product is deleted.
- Server-side validation and database foreign keys to protect catalog relationships.

## Requirements

- PHP 8.3 or newer in the 8.x series, with Composer.
- Node.js and npm (a current LTS release is recommended).
- MySQL

The project uses Laravel Framework 13 and Bootstrap 5 for the admin modals. The storefront styles use the Tailwind setup already included in the Laravel starter app.

## Project structure

- `app/Http/Controllers` — storefront and category, subcategory, and product request handling.
- `app/Models` — `Category`, `Subcategory`, and `Product` Eloquent relationships.
- `database/migrations` — catalog table definitions, keys, and constraints.
- `database/seeders` — repeatable sample categories, subcategories, and products.
- `resources/views/shop` — storefront listing and product detail pages.
- `resources/views/admin` — catalog tables and CRUD modal forms.
- `resources/views/layouts` — shared storefront and admin layouts.
- `routes/web.php` — storefront and admin web routes.
- `resources/css` and `resources/js` — Vite assets; Bootstrap modal behavior is isolated to the admin assets.

## Install and run locally

### 1. Install dependencies

Run these commands from the project root:

```powershell
composer install
npm install
```

### 2. Create the local environment file

```powershell
Copy-Item .env.example .env
```

### 3. Configure the database

Create an empty database in HeidiSQL or phpMyAdmin, then set these values in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dokan_market
DB_USERNAME=root
DB_PASSWORD=
```

Use the database name and credentials configured on your machine.

### 4. Generate the app key, create tables, and load sample data

For a new, empty database:

```powershell
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

The seeders add sample Electronics and Clothing categories, their subcategories, and four products. The default seeder also creates a development test-user record; this application does not include a login screen.

If you are restoring the submitted database export described below, import it into an empty database instead of running `migrate --seed` again. The export already contains the schema and seeded data.

### 5. Build assets and start the app

```powershell
npm run build
php artisan serve
```

## How the application is organized

### Storefront

- `/` displays the product catalog and category sidebar.
- `/categories/{slug}` filters products to a category.
- `/subcategories/{slug}` filters products to a subcategory.
- `/products/{slug}` displays product details.
- The sidebar and product pagination use normal server-rendered links; no custom JavaScript is needed for browsing.

### Admin

- `/admin` redirects to `/admin/categories`.
- `/admin/categories` manages categories.
- `/admin/subcategories` manages subcategories and their parent categories.
- `/admin/products` manages product details, prices, categories, subcategories, and images.
- The Bootstrap JavaScript bundle powers modal open/close behavior. A small admin script reopens the relevant form after a validation error so the user can correct it.

Admin routes are not protected by authentication because login was outside the stated assessment requirements. Keep this demo on a local or otherwise private environment; add authentication before exposing admin tools publicly.

## Data model and relationships

```text
Category 1 ─── many Subcategories 1 ─── many Products
Category 1 ─── many Products
```

- `Category` has many `Subcategory` and `Product` records.
- `Subcategory` belongs to one category and has many products.
- `Product` belongs to one category and one subcategory.
- Products store both `category_id` and `subcategory_id`. A composite foreign key ensures the product's selected subcategory belongs to its selected category.
- Category names and slugs are unique. Subcategory names are unique within their category; subcategory slugs are unique. Product slugs are unique and generated from product names.
- Categories cannot be deleted while subcategories or products refer to them. Subcategories cannot be deleted while products refer to them. Products can be deleted; their uploaded image is removed as well.

The migrations are in `database/migrations`, models and Eloquent relationships are in `app/Models`, and catalog sample data is in `database/seeders`.

## Suggested walkthrough

1. Open the storefront and browse the full catalog.
2. Choose a category, then a subcategory, and open a product detail page.
3. Follow **Go to admin**; create a category, then a subcategory under it, then a product.
4. Edit each record from its modal and confirm the updated details appear in the table or shop.
5. Try to delete a category or subcategory that still has related records; the app should explain why it is blocked. Delete a product to confirm its confirmation modal appears.

## Validation and image handling

- Category and subcategory names are required, limited to 255 characters, and checked for the appropriate uniqueness rule.
- A product requires a category, a matching subcategory, a name, a description, and a new price.
- Prices must be non-negative and use up to two decimal places. The old price is optional; if provided, it must be at least the new price.
- Product images are optional. Accepted formats are JPG, PNG, and WebP, up to 2 MB.
- Uploaded images are stored under `storage/app/public/products`; `php artisan storage:link` exposes them through `public/storage`.

