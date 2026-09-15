@extends('layouts.app')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl font-bold">Dashboard</h1><p class="text-sm text-gray-500">Daily sales, purchasing, stock and receivables at a glance.</p></div>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
<a href="{{ route('quotations.create') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:ring-green-600"><div class="text-sm text-gray-500">Sales</div><div class="mt-2 text-xl font-bold">New Quotation</div><div class="mt-2 text-xs text-gray-500">F2</div></a>
<a href="{{ route('purchases.create') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:ring-green-600"><div class="text-sm text-gray-500">Purchase</div><div class="mt-2 text-xl font-bold">Receive Purchase</div></a>
<a href="{{ route('customer_payments.create') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:ring-green-600"><div class="text-sm text-gray-500">Receivables</div><div class="mt-2 text-xl font-bold">New Receipt</div></a>
<a href="{{ route('supplier_payments.create') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:ring-green-600"><div class="text-sm text-gray-500">Payables</div><div class="mt-2 text-xl font-bold">Pay Supplier</div></a>
</div>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
<div class="rounded-xl border bg-white p-5"><div class="text-sm text-gray-500">Sales This Month</div><div class="mt-2 text-2xl font-bold">PKR {{ number_format($sales,2) }}</div></div>
<div class="rounded-xl border bg-white p-5"><div class="text-sm text-gray-500">Purchases This Month</div><div class="mt-2 text-2xl font-bold">PKR {{ number_format($purchases,2) }}</div></div>
<div class="rounded-xl border bg-white p-5"><div class="text-sm text-gray-500">Receivables</div><div class="mt-2 text-2xl font-bold">PKR {{ number_format($receivables,2) }}</div><a class="mt-2 inline-block text-sm underline" href="{{ route('receivables.index') }}">View</a></div>
<div class="rounded-xl border bg-white p-5"><div class="text-sm text-gray-500">Payables</div><div class="mt-2 text-2xl font-bold">PKR {{ number_format($payables,2) }}</div><a class="mt-2 inline-block text-sm underline" href="{{ route('payables.index') }}">View</a></div>
</div>
<div class="grid gap-4 md:grid-cols-3">
<div class="rounded-xl border bg-white p-5"><div class="text-sm text-gray-500">Products In Stock</div><div class="mt-2 text-2xl font-bold">{{ $stockProducts }}</div></div>
<a href="{{ route('invoices.index') }}" class="rounded-xl border bg-white p-5 hover:ring-1 hover:ring-green-600"><div class="text-sm text-gray-500">Sales Documents</div><div class="mt-2 font-bold">Invoices & Delivery Challans →</div></a>
<a href="{{ route('supplier_payments.index') }}" class="rounded-xl border bg-white p-5 hover:ring-1 hover:ring-green-600"><div class="text-sm text-gray-500">Supplier Accounts</div><div class="mt-2 font-bold">Payments & Statements →</div></a>
</div>
</div>
@endsection
