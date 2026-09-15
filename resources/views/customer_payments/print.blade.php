<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $payment->receipt_number }}</title>
    <style>
        @page { size: A4; margin: 16mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; background: #fff; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.45; }
        .sheet { max-width: 794px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; padding-bottom: 16px; border-bottom: 2px solid #111827; }
        .brand { font-size: 18px; font-weight: 700; }
        .subtitle { margin-top: 3px; color: #6b7280; font-size: 11px; }
        .title { text-align: right; }
        .title h1 { margin: 0; font-size: 23px; letter-spacing: 1px; }
        .number { margin-top: 4px; font-weight: 700; font-size: 13px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 22px; margin-top: 18px; }
        .meta-card { min-height: 42px; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 4px; }
        .label { display: block; margin-bottom: 2px; color: #6b7280; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; }
        .value { font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 22px; }
        th, td { border: 1px solid #9ca3af; padding: 8px 9px; vertical-align: top; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; letter-spacing: .35px; }
        .right { text-align: right; }
        tfoot td { border-top: 2px solid #111827; }
        .total-label, .total-value { font-size: 15px; font-weight: 700; }
        .notes { margin-top: 20px; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 4px; }
        .notes strong { display: block; margin-bottom: 3px; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 55px; margin-top: 90px; }
        .signature { padding-top: 28px; border-top: 1px solid #374151; }
        .signature strong { display: block; font-size: 11px; }
        .signature span { display: block; margin-top: 3px; color: #6b7280; font-size: 10px; }
        .actions { margin-top: 28px; text-align: center; }
        button { padding: 9px 16px; border: 0; border-radius: 5px; background: #111827; color: #fff; cursor: pointer; }
        @media (max-width: 640px) {
            body { padding: 16px; }
            .header, .meta, .signatures { display: grid; grid-template-columns: 1fr; }
            .title { text-align: left; }
            .signatures { gap: 42px; }
        }
        @media print {
            .no-print { display: none !important; }
            .sheet { max-width: none; }
        }
    </style>
</head>
<body>
<div class="sheet">
    <header class="header">
        <div>
            <div class="brand">IT Supplies, Services &amp; Support</div>
            <div class="subtitle">Customer Payment Receipt</div>
        </div>
        <div class="title">
            <h1>PAYMENT RECEIPT</h1>
            <div class="number">{{ $payment->receipt_number }}</div>
        </div>
    </header>

    <section class="meta">
        <div class="meta-card"><span class="label">Date</span><span class="value">{{ $payment->payment_date->format('d-M-Y') }}</span></div>
        <div class="meta-card"><span class="label">Customer</span><span class="value">{{ $payment->customer->company_name }}</span></div>
        <div class="meta-card"><span class="label">Payment Method</span><span class="value">{{ $payment->payment_method }}</span></div>
        <div class="meta-card"><span class="label">Reference</span><span class="value">{{ $payment->reference ?: '—' }}</span></div>
    </section>

    <table>
        <thead>
        <tr>
            <th>Invoice</th>
            <th class="right">Amount Allocated</th>
        </tr>
        </thead>
        <tbody>
        @forelse($payment->allocations as $allocation)
            <tr>
                <td>{{ $allocation->invoice->invoice_number }}</td>
                <td class="right">PKR {{ number_format($allocation->amount, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">Unallocated customer advance / receipt</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <td class="total-label">Total Received</td>
            <td class="right total-value">PKR {{ number_format($payment->amount, 2) }}</td>
        </tr>
        </tfoot>
    </table>

    @if($payment->notes)
        <div class="notes"><strong>Notes</strong>{{ $payment->notes }}</div>
    @endif

    <section class="signatures">
        <div class="signature"><strong>Received By</strong><span>Name / Date / Signature</span></div>
        <div class="signature"><strong>Authorized Signature</strong><span>Name / Date / Signature</span></div>
    </section>

    <div class="actions no-print"><button type="button" onclick="window.print()">Print</button></div>
</div>
<script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
