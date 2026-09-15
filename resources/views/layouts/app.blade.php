<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tech Team Business System') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="no-print border-b bg-white">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3">
        <a href="{{ route('dashboard') }}" class="text-lg font-extrabold">Tech Team</a>
        <nav class="hidden gap-3 text-sm lg:flex lg:items-center">
            <a href="{{ route('dashboard') }}" class="hover:text-green-700">Dashboard</a>
            <a href="{{ route('quotations.index') }}" class="hover:text-green-700">Quotations</a>
            <a href="{{ route('invoices.index') }}" class="hover:text-green-700">Invoices</a>
            <a href="{{ route('delivery_challans.index') }}" class="hover:text-green-700">Challans</a>
            <a href="{{ route('purchases.index') }}" class="hover:text-green-700">Purchases</a>
            <a href="{{ route('customer_payments.index') }}" class="hover:text-green-700">Receipts</a>
            <a href="{{ route('supplier_payments.index') }}" class="hover:text-green-700">Supplier Payments</a>
            <a href="{{ route('receivables.index') }}" class="hover:text-green-700">Receivables</a>
            <a href="{{ route('payables.index') }}" class="hover:text-green-700">Payables</a>
        </nav>
        <div class="ml-auto"><input data-global-search placeholder="Ctrl+K Search" class="w-36 rounded-lg border px-3 py-2 text-sm md:w-56"></div>
    </div>
</header>
<main class="mx-auto max-w-7xl px-4 py-6">
@if(session('success')) <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div> @endif
@yield('content')
</main>
</body>
</html>
