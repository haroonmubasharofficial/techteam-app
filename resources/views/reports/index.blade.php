@extends('layouts.app')
@section('content')
<div class="mb-5"><h1 class="text-2xl font-bold">Reports</h1><p class="text-sm text-gray-500">Quick management view of sales, purchases, cash movement, receivables, payables and stock.</p></div>
<form class="mb-5 grid gap-2 rounded-xl border bg-white p-4 md:grid-cols-3"><div><label class="text-xs font-semibold">From</label><input type="date" name="from" value="{{ $from }}" class="mt-1 w-full rounded border px-3 py-2"></div><div><label class="text-xs font-semibold">To</label><input type="date" name="to" value="{{ $to }}" class="mt-1 w-full rounded border px-3 py-2"></div><button class="self-end rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Run Report</button></form>
<div class="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
@foreach([['Sales', $sales->total ?? 0],['Actual Cost', $sales->cost ?? 0],['Gross Profit', $sales->profit ?? 0],['Purchases', $purchases->total ?? 0],['Customer Receipts', $receipts],['Supplier Payments', $supplierPayments],['Receivables', $receivable],['Payables', $payable],['Stock Value', $stockValue]] as $card)
<div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase tracking-wide text-gray-500">{{ $card[0] }}</div><div class="mt-2 text-xl font-bold">PKR {{ number_format((float)$card[1],2) }}</div></div>
@endforeach
</div>
<div class="mt-5 rounded-xl border bg-white p-4 text-sm text-gray-600">Period: <strong>{{ $from }}</strong> to <strong>{{ $to }}</strong>. Profit is based on the actual cost captured on invoices; stock value uses weighted average inbound transaction cost.</div>
@endsection
