<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Invoice {{ $invoice->invoice_number }}</title>
<style>
*{box-sizing:border-box}
body{font-family:"Times New Roman",Times,serif;color:#111;margin:0;background:#eee;font-size:13px;line-height:1.25}
.sheet{width:8.5in;min-height:11in;margin:20px auto;background:#fff;padding:.92in .42in .72in;position:relative}
.actions{position:fixed;top:15px;right:15px;z-index:10}.actions button{border:0;background:#111827;color:#fff;padding:9px 16px;border-radius:4px;cursor:pointer}
.doc-meta{width:180px;border-collapse:collapse;margin-bottom:38px;font-size:12px}.doc-meta td{border:1px solid #222;padding:3px 6px}.doc-meta td:first-child{font-weight:700;width:58%}
.title{text-align:center;font-size:20px;font-weight:700;margin:0 0 58px;letter-spacing:.3px}
.office{text-align:right;font-size:12px;font-weight:700;margin-top:-42px;margin-bottom:12px}
.client{width:100%;border-collapse:collapse;margin-bottom:0;font-size:13px}.client td{border-bottom:1px solid #222;padding:4px 5px;vertical-align:middle;height:25px}.client td:first-child{width:95px;font-weight:700}.client td:nth-child(2){padding-left:12px}
.items{width:100%;border-collapse:collapse;table-layout:fixed;font-size:12px}.items th,.items td{border:1px solid #222;padding:5px 6px;height:26px}.items th{font-weight:700;text-align:center}.items .sr{width:9%}.items .desc{width:53%}.items .qty{width:12%}.items .unit{width:13%}.items .total{width:17%}.right{text-align:right}.center{text-align:center}
.items tbody tr.blank td{height:25px}
.total-row td{font-weight:700;height:27px}.total-label{text-align:right}.total-value{text-align:right;white-space:nowrap}
.terms{margin-top:18px;font-size:12px}.terms-title{text-align:center;font-size:13px;font-weight:700;margin-bottom:6px}.terms ol{margin:0;padding-left:25px}.terms li{padding:1px 0}
.sign{margin-top:44px;text-align:right;font-size:12px}.sign-line{display:inline-block;min-width:170px;border-top:1px solid #222;padding-top:4px;text-align:center}
@media(max-width:650px){body{background:#fff}.sheet{width:100%;min-height:0;margin:0;padding:25px 18px}.actions{position:static;margin-bottom:15px}.title{margin-bottom:35px}.doc-meta{margin-bottom:25px}.items{font-size:11px}.items th,.items td{padding:4px}.client td:first-child{width:75px}}
@media print{body{background:#fff}.sheet{width:auto;min-height:0;margin:0;padding:.92in .42in .72in}.actions{display:none}@page{size:Letter;margin:0}}
</style>
</head>
<body>
<div class="sheet">
<div class="actions"><button onclick="window.print()">Print</button></div>
<table class="doc-meta"><tr><td>Date:</td><td>{{ $invoice->invoice_date?->format('d-m-y') }}</td></tr><tr><td>Invoice #</td><td>{{ $invoice->invoice_number }}</td></tr></table>
<h1 class="title">INVOICE</h1>
<div class="office">ETN - HO</div>
<table class="client">
<tr><td>Client Name</td><td>{{ $invoice->customer->contact_person ? $invoice->customer->contact_person.' ' : '' }}{{ $invoice->customer->company_name }}</td></tr>
<tr><td>Address</td><td>{{ $invoice->customer->address }}</td></tr>
<tr><td>Summary</td><td>{{ $invoice->summary ?: '—' }}</td></tr>
</table>
<table class="items">
<thead><tr><th class="sr">Sr. #</th><th class="desc">Description</th><th class="qty">Quantity</th><th class="unit">Unit Price</th><th class="total">Total Price</th></tr></thead>
<tbody>
@foreach($invoice->items as $item)
<tr><td class="center">{{ $item->line_no }}</td><td>{{ $item->description }}</td><td class="center">{{ rtrim(rtrim(number_format($item->quantity,4,'.',''),'0'),'.') }}</td><td class="right">{{ number_format($item->selling_price_unit,0) }}</td><td class="right">{{ number_format($item->selling_price_unit*$item->quantity-$item->discount+$item->tax_amount,0) }}</td></tr>
@endforeach
@for($i=$invoice->items->count();$i<8;$i++)<tr class="blank"><td></td><td></td><td></td><td></td><td></td></tr>@endfor
<tr class="total-row"><td colspan="4" class="total-label">Total (PKR)</td><td class="total-value">{{ number_format($invoice->total_amount,0) }}/-</td></tr>
</tbody>
</table>
@if($invoice->terms)
<div class="terms"><div class="terms-title">TERMS &amp; CONDITIONS</div><ol>@foreach(preg_split('/\r\n|\r|\n/', trim($invoice->terms)) as $term) @if(trim($term)!=='')<li>{{ preg_replace('/^\s*\(?\d+\)?[\.\-:]?\s*/','',trim($term)) }}</li>@endif @endforeach</ol></div>
@endif
<div class="sign"><span class="sign-line">On behalf of Tech Team</span></div>
</div>
</body></html>
