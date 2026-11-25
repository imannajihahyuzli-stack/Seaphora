# Seaphora

Seaphora is a beauty and skincare online shop built with Laravel for **SPG 0553 Advanced Web Programming (July 2026)**, German-Malaysian Institute.

Customers browse products, fill a cart, check out and review what they bought. Staff manage products and orders. The admin also manages brands and categories.

Repository: https://github.com/imannajihahyuzli-stack/Seaphora

## Tech stack

- Laravel 13, PHP 8.4 or newer
- Livewire 4 (reactive components) and Flux UI 2 (components)
- Laravel Fortify (login, registration, password reset, email verification, two-factor, passkeys)
- Alpine.js (confirmation modals, auto-hiding alerts, expandable rows)
- Pest 5 (automated tests)
- SQLite (default database)

## Requirements

- PHP 8.4 or newer
- Composer 2
- Node.js 22 with npm
- Laravel Herd, Laragon, or `php artisan serve`

## Setup

Run these commands in the project folder.

Windows:

```bash
composer install
copy .env.example .env
php artisan key:generate
type nul > database\database.sqlite
php artisan migrate:fresh --seed
npm install
npm run build
```

Mac / Linux:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
npm install
npm run build
```

Then open the site:

- Herd or Laragon: `http://seaphora.test` (the folder must be named `Seaphora` and sit in the Herd or Laragon sites folder)
- Otherwise run `php artisan serve` and open `http://localhost:8000`

For development with live reload, run `composer run dev`.

## Environment variables

Copy `.env.example` to `.env`. The only values you must set are:

| Variable | Value |
| --- | --- |
| `APP_KEY` | created by `php artisan key:generate` |
| `APP_URL` | your site address, for example `http://seaphora.test` |
| `DB_CONNECTION` | `sqlite` (already set) |

To use MySQL instead, create an empty database, then set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` in `.env`.

Never commit `.env`. It is listed in `.gitignore`. For a live site, set `APP_ENV=production` and `APP_DEBUG=false`.

## Default logins

Created by `php artisan db:seed`. The password for every account is `password`.

| Role | Email | Can do |
| --- | --- | --- |
| Admin | `admin@seaphora.test` | everything, including brands and categories |
| Staff | `staff@seaphora.test` | manage products and orders |
| Customer | `customer1@seaphora.test` | browse, cart, checkout, own orders, reviews |
| Customer | `customer2@seaphora.test` | same as above |
| Customer | `customer3@seaphora.test` | same as above |

The seeder also creates 5 brands, 6 categories and 25 products. It creates no orders or reviews: log in as a customer, place an order, then log in as staff and move it through `paid`, `shipped` and `completed`. After that the customer can write a review.

## Run the tests

```bash
php artisan test
```

The suite is in the `tests/` folder (63 feature tests and 34 unit tests).

## Module allocation

| Student | Role | Owned code |
| --- | --- | --- |
| Student 1: [Syarifah Nur Syakirah] | Security Lead | Fortify setup (`FortifyServiceProvider`, `config/fortify.php`, `app/Actions/Fortify`), role middleware (`EnsureUserHasRole`), route guards in `routes/web.php`, auth and settings pages, `.env` handling, auth and role tests |
| Student 2: [Iman Najihah] | Catalogue Lead | Products, Brands and Categories: Livewire components, `ProductForm`, `BrandForm`, `CategoryForm`, `StoreProductRequest`, their Create/Update/Delete Actions, Product, Brand and Category Policies, related tests |
| Student 3: [Ain Sofea] | Shop Flow Lead | Cart, Checkout, Orders and Reviews: Livewire components, Cart, Order and Review Policies, `CreateOrderAction`, `UpdateOrderStatusAction`, cart and review Actions, Alpine `confirm-modal` and `auto-alert`, related tests |

## Project structure

| Folder | Contents |
| --- | --- |
| `app/Actions/` | business logic, one class per task (16 Actions) |
| `app/Http/Requests/` | Form Request (`StoreProductRequest`) |
| `app/Livewire/` | page components and `Forms/` validation objects |
| `app/Policies/` | authorization rules for brands, carts, categories, orders, products, reviews |
| `app/Http/Middleware/` | `EnsureUserHasRole` (the `role:` route guard) |
| `database/migrations/` | the nine domain tables with foreign keys and `onDelete` rules |
| `database/seeders/`, `database/factories/` | demo data |
| `resources/views/livewire/` | Flux UI screens |
| `resources/views/components/` | Alpine components (`confirm-modal`, `auto-alert`) |
| `tests/Feature/`, `tests/Unit/` | Pest tests |

## Troubleshooting

- **`composer install` says the PHP version is too low:** switch to PHP 8.4 or newer, then open a new terminal.
- **Page looks unstyled:** run `npm run build` again.
- **Database error:** make sure `database/database.sqlite` exists, then run `php artisan migrate:fresh --seed`.
- **Download timeout during `composer install`:** run the command again, or raise `default_socket_timeout` in `php.ini`.
