# Tech Team Business Management System

Local development target: Laravel 13, PHP 8.5 target, MySQL LTS, Tailwind CSS 4.3+, Vite.

## Scope
This release focuses on the core IT supplies, services and support business workflow. The SLA/service-contract module is intentionally deferred.

## Implemented
- Responsive dashboard with live sales, purchase, receivable, payable and stock metrics
- Keyboard-friendly responsive layout with Ctrl+K search focus, F2 quotation shortcut and Ctrl+S form-save shortcut
- Customer and supplier master creation, editing, listing and deactivation
- Customer/supplier statements, receivables and payables
- Product/service master creation, editing and stock quantity view
- Product category and unit administration
- Quotation creation/editing with maximum 15 lines
- Live estimated cost, profit and margin using purchase, delivery and other costs
- Invoice creation from quotation with actual cost/profit and customer ledger posting
- Customer receipts, invoice allocation and customer advances
- Purchase receiving with automatic stock-in transactions and supplier payable ledger
- Supplier payments, purchase allocation and supplier advances
- Delivery Challans with stock-out transactions, quantity validation and partial delivery support
- Stock adjustments with stock-in/stock-out validation and Main Warehouse support
- Stock ledger/history and estimated warehouse stock valuation using weighted average inbound transaction cost
- Management reports for sales, actual cost, gross profit, purchases, receipts, supplier payments, receivables, payables and stock value
- Customer-facing print templates for quotation, invoice, delivery challan and payment receipt
- Authentication with active-user enforcement and admin/staff roles
- Admin user management
- Admin audit log with filtering and before/after details
- Audit coverage for authentication, users, quotations, invoices, purchases, customers, suppliers, products, payments, delivery challans, stock adjustments, categories and units
- FBR invoice integration placeholders for later implementation
- Concurrency-safe, year-specific document sequences for quotations, invoices, purchases, delivery challans, receipts, supplier payments and stock adjustments

## Important
This repository is a source build. Composer/npm dependencies are intentionally not vendored. The build environment used to prepare this package does not have external package-network access, so full runtime testing must be performed after dependencies are installed.

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

## Production / cPanel
- Use PHP 8.5 if offered by the host; otherwise use a supported Laravel 13 PHP version.
- Use MySQL LTS with a dedicated database/user.
- Enable HTTPS and OPcache.
- Point the domain/subdomain document root to the Laravel `public` directory.
- Keep `.env` outside public web access and set `APP_ENV=production`, `APP_DEBUG=false` and the production `APP_URL`.
- Run migrations from SSH/Artisan where available.
- Build frontend assets before deployment and deploy the generated `public/build` assets.
- Configure the Laravel writable `storage` and `bootstrap/cache` directories according to the host's permissions.
- Configure a cron job only for future scheduled/background features; the current core workflow does not require a queue worker.

## Final production checklist
- Install Composer/npm dependencies and run the full test suite.
- Run `php artisan migrate --seed` on a clean test database and exercise quotation → invoice → delivery → receipt, purchase → stock → supplier payment, and adjustment flows.
- Verify document numbering under concurrent requests.
- Confirm print output against the final company quotation, invoice and delivery challan branding.
- Add FBR credentials/API integration when the required FBR environment and credentials are available.
- Configure production backups, HTTPS, database access, mail and monitoring.

## Deferred
- SLA/service contract module
- FBR live integration until credentials/API requirements are available
