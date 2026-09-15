# Build status — 2026-09-15

## Completed
- Laravel 13 source skeleton
- PHP ^8.3 compatibility; target PHP 8.5 on production
- MySQL migration for first milestone
- Customer, product, quotation models
- Quotation controller with server-side validation
- 15-item maximum
- Live estimated cost/profit/margin UI
- Keyboard shortcuts: F2, Ctrl+K, Ctrl+S
- Responsive Blade UI
- Tailwind CSS latest via npm `latest` + Vite plugin
- Seed data based on the supplied Tech Team examples
- Standalone browser preview under `preview/index.html`

## Not yet completed
- Authentication/roles
- Invoice posting
- Purchases/stock posting
- Payments/ledgers
- SLA
- PDF templates matching the supplied documents
- FBR integration boundary implementation

## Environment limitation
The build environment has PHP 8.4 and Node 22 but no Composer and no outbound package-network access, so vendor dependencies were not installed. The source is prepared for `composer install` and `npm install` on a machine with package-network access.
