# Tech Team Business Management System — Database Design v1

## Technology target
- Laravel 13
- PHP 8.3+
- MySQL 8.0+
- cPanel/Linux shared hosting
- Responsive web/PWA interface
- PKR as default currency

Laravel 13 was released March 17, 2026 and requires PHP 8.3+, with security fixes scheduled through March 17, 2028. The application should therefore target PHP 8.3+ on the hosting account. The Laravel web-facing directory must be the configured web/document root; sensitive application files should not be publicly exposed.

## Design principles
1. Keep the user interface small and keyboard-first.
2. Printed documents stay close to the supplied manual quotation, invoice and delivery challan.
3. Internal costing is richer than customer-facing documents.
4. Quotation costs are snapshots and must never change when a product's current cost changes.
5. Invoice actual cost is stored separately from quotation estimated cost.
6. Products and services are separate by type; services normally do not affect stock.
7. One warehouse is seeded initially, but the schema permits future expansion.
8. FBR fields are reserved now; integration is a later module.
9. Customer/supplier balances are generated from posted transactions and ledger entries.
10. Every important mutation is auditable.

## Core business flow

Customer → Quotation → optional Purchase → Delivery Challan → Invoice → Customer Payment

Purchase → Stock Increase → Supplier Payable → Supplier Payment

Quotation → Estimated Cost/Profit
Invoice → Actual Cost/Profit

## Key costing rules

### Quotation
For every line:
- Purchase cost per unit
- Delivery cost per unit
- Other cost per unit
- Selling price per unit
- Discount
- Tax rate/amount
- Estimated total cost
- Estimated profit
- Estimated margin %

Quotation totals are stored so the historical quote remains reproducible even if the product master changes later.

### Invoice
For every line:
- Actual cost per unit
- Selling price per unit
- Discount
- Tax
- Actual cost total
- Actual profit
- Actual margin %

`purchase_item_id` is optional. When a sale is directly tied to a specific purchase, it can identify the actual purchase cost. Otherwise the application can use the configured costing method (initial implementation: explicit stored actual cost; later enhancement: weighted-average costing if required).

## Customer ledger
Invoices create customer debit entries. Customer receipts create customer credit entries. Opening balances can be implemented as a ledger entry with `reference_type = opening_balance`.

## Supplier ledger
Purchases create supplier credit entries. Supplier payments create supplier debit entries. Opening balances use the same ledger mechanism.

## Stock
Stock is transaction-based. The application should never simply overwrite a product's stock number without creating a stock transaction.

Examples:
- Purchase → quantity_in
- Sale → quantity_out
- Return in → quantity_in
- Return out → quantity_out
- Adjustment → quantity_in or quantity_out
- Opening stock → quantity_in

Current stock is calculated from the sum of stock transactions for the product/warehouse.

## Printed documents

### Quotation
Customer-facing fields remain close to the supplied document:
- Logo/company header
- Date
- Quotation/serial number
- Customer
- Address
- Reference
- Summary
- Description
- Unit price
- Quantity
- Amount
- Total PKR
- Terms & Conditions
- Contact/footer

Internal purchase cost, delivery cost and profit are never printed unless a future internal quotation print option is explicitly added.

### Invoice
Customer-facing fields remain close to the supplied document:
- Date
- Invoice number
- Customer/contact
- Address
- Summary/reference
- Description
- Quantity
- Unit price
- Total price
- Total PKR
- Terms & Conditions
- Authorized signature

### Delivery Challan
Preserve the supplied operational structure:
- Delivery Challan For
- Shipping To
- Address/phone/email
- Challan number
- Delivery date
- Shipped date
- Sr. No.
- Item name
- Serial # / Service Tag
- Quantity
- Unit
- Received By / Name / Comment / Date / Signature
- Delivered By / Name / Comment / Date / Signature
- Terms & Conditions

## FBR preparation
Invoices include reserved fields for:
- FBR status
- FBR invoice number
- FBR UUID
- submission timestamp
- response payload
- QR data

Tax data is stored at line and invoice level rather than hard-coded to GST so the later FBR integration can support the applicable tax model.

## SLA
The first version keeps SLA intentionally lightweight:
- Customer
- SLA name
- Start/end dates
- Response target
- Resolution target
- Amount
- Services included
- Status

A full helpdesk/ticketing system is intentionally outside v1.

## Suggested implementation services
- `QuotationService`: calculation, validation, conversion
- `InvoiceService`: posting, payment balance, actual profit
- `PurchaseService`: supplier posting and stock receipt
- `DeliveryChallanService`: delivery document and serial/service-tag handling
- `StockService`: stock transaction posting
- `CustomerLedgerService`: customer ledger posting
- `SupplierLedgerService`: supplier ledger posting
- `DocumentNumberService`: sequential document numbers
- `ProfitService`: estimated vs actual profit calculations
- `FbrService`: placeholder interface only in v1

## First development milestone
1. Authentication
2. Company settings
3. Customers
4. Suppliers
5. Products/services
6. Dashboard
7. Quotation creation + costing
8. Quotation PDF matching supplied sample
9. Invoice creation + PDF
10. Delivery Challan creation + PDF
11. Purchases + stock
12. Customer/supplier payments and ledgers
13. Profit reports
14. SLA basics
15. FBR placeholder/integration boundary
