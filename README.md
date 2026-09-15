# Tech Team Business Management System

Local development target: Laravel 13, PHP 8.5 target, MySQL LTS, Tailwind CSS 4.3+, Vite.

## Current milestone
- Dashboard shell
- Keyboard shortcuts
- Responsive layout
- Customer/product foundations
- Quotation creation
- 15-line limit
- Live estimated cost/profit/margin
- Database migration and seed data

## Important
This repository is a source build. Composer/npm dependencies are intentionally not vendored. The build environment used to prepare this package does not have external package-network access.

## Setup when Composer/npm are available
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

For cPanel production, use PHP 8.5 if offered by the host, MySQL LTS, HTTPS, OPcache, cron and a document root pointing at `/public`.

## Next development milestone
Invoices + purchase posting + stock transactions + customer/supplier payments + PDF templates matching supplied manual documents.
