<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $challan->challan_number }}</title>
    <style>
        @page { size: A4; margin: 14mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; background: #fff; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.45; }
        .sheet { max-width: 794px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; padding-bottom: 16px; border-bottom: 2px solid #111827; }
        .brand { font-size: 18px; font-weight: 700; letter-spacing: .2px; }
        .subtitle { margin-top: 3px; color: #4b5563; font-size: 11px; }
        .title { text-align: right; }
        .title h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .number { margin-top: 4px; font-weight: 700; font-size: 13px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 22px; margin-top: 18px; }
        .meta-card { min-height: 42px; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 4px; }
        .label { display: block; margin-bottom: 2px; color: #6b7280; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; }
        .value { font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #9ca3af; padding: 8px 9px; vertical-align: top; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; letter-spacing: .35px; }
        td:first-child, th:first-child { width: 7%; text-align: center; }
        td:nth-child(3), th:nth-child(3) { width: 25%; }
        td:nth-child(4), th:nth-child(4) { width: 11%; text-align: right; }
        td:nth-child(5), th:nth-child(5) { width: 10%; text-align: center; }
        .terms { margin-top: 20px; padding-top: 12px; border-top: 1px solid #d1d5db; }
        .terms h2 { margin: 0 0 6px; font-size: 12px; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 55px; margin-top: 70px; }
        .signature { padding-top: 28px; border-top: 1px solid #374151; }
        .signature strong { display: block; font-size: 11px; }
        .signature span { display: block; margin-top: 3px; color: #6b7280; font-size: 10px; }
        .actions { margin-top: 28px; text-align: center; }
        button { padding: 9px 16px; border: 0; border-radius: 5px; background: #111827; color: #fff; cursor: pointer; }
        @media (max-width: 640px) {
            body { padding: 16px; }
            .header, .meta, .signatures { grid-template-columns: 1fr; display: grid; }
            .title { text-align: left; }
            .signatures { gap: 42px; }
            table { font-size: 11px; }
            th, td { padding: 6px; }
        }
        @media print {
            .no-print { display: none !important; }
            .sheet { max-width: none; }
            a { color: inherit; text-decoration: none; }
        }
    </style>
</head>
<body>
<div class="sheet">
    <header class="header">
        <div>
            <div class="brand">IT Supplies, Services &amp; Support</div>
            <div class="subtitle">Delivery Challan</div>
        </div>
        <div class="title">
            <h1>DELIVERY CHALLAN</h1>
            <div class="number">{{ $challan->challan_number }}</div>
        </div>
    </header>

    <section class="meta">
        <div class="meta-card"><span class="label">Delivery Challan For</span><span class="value">{{ $challan->delivery_challan_for ?: '—' }}</span></div>
        <div class="meta-card"><span class="label">Shipping To</span><span class="value">{{ $challan->shipping_to ?: '—' }}</span></div>
        <div class="meta-card"><span class="label">Customer</span><span class="value">{{ $challan->customer->company_name }}</span></div>
        <div class="meta-card"><span class="label">Delivery Date</span><span class="value">{{ $challan->delivery_date?->format('d-M-Y') ?? '—' }}</span></div>
        <div class="meta-card"><span class="label">Invoice</span><span class="value">{{ $challan->invoice?->invoice_number ?? '—' }}</span></div>
        <div class="meta-card"><span class="label">Shipped Date</span><span class="value">{{ $challan->shipped_date?->format('d-M-Y') ?? '—' }}</span></div>
    </section>

    <table>
        <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Item</th>
            <th>Serial / Service Tag</th>
            <th>Qty</th>
            <th>Unit</th>
        </tr>
        </thead>
        <tbody>
        @foreach($challan->items as $item)
            <tr>
                <td>{{ $item->line_no }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->serial_number ?: '—' }}</td>
                <td>{{ rtrim(rtrim(number_format($item->quantity, 4, '.', ''), '0'), '.') }}</td>
                <td>{{ $item->unit }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($challan->terms)
        <section class="terms">
            <h2>Terms &amp; Conditions</h2>
            <div>{!! nl2br(e($challan->terms)) !!}</div>
        </section>
    @endif

    <section class="signatures">
        <div class="signature"><strong>Delivered By</strong><span>Name / Date / Signature</span></div>
        <div class="signature"><strong>Received By</strong><span>Name / Date / Signature</span></div>
    </section>

    <div class="actions no-print"><button type="button" onclick="window.print()">Print</button></div>
</div>
</body>
</html>
