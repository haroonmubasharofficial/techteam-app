# Tech Team Business Management System

Local development target: Laravel 13, PHP 8.5 target, MySQL LTS, Tailwind CSS 4.3+, Vite.

## Implemented
- Responsive dashboard with live sales, purchase, receivable, payable and stock metrics
- Keyboard-friendly responsive layout
- Customer and supplier master creation/listing
- Product/service master creation and stock quantity view
- Quotation creation/editing with maximum 15 lines
- Live estimated cost, profit and margin
- Invoice creation from quotation with actual cost/profit
- Customer receivables, payment receipts, invoice allocation and customer statements
- Purchase receiving with automatic stock-in transactions and supplier payable ledger
- Supplier payables, supplier payments, purchase allocation and supplier statements
- Delivery Challans with stock-out transactions, quantity validation and partial delivery support
- Customer-facing print templates for quotation, invoice, delivery challan and payment receipt
- FBR invoice integration placeholders for later implementation

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

## Still to harden before production
- Full automated feature/unit test suite and runtime validation with installed Composer/npm dependencies
- Robust concurrency-safe document numbering/sequences
- Customer/supplier editing and deactivation workflows
- Product editing, category/unit management and stock adjustments
- Stock valuation and inventory reporting
- Sales/purchase reporting and profit reports
- Authentication, authorization, audit logging and user roles
- Final PDF generation and exact branding based on the supplied company documents
- FBR integration once credentials/API requirements are available
- SLA/service contract module
