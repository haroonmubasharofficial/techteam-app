<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tech Team Business System') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="no-print border-b bg-white">
    <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3">
        <a href="{{ route('dashboard') }}" class="text-lg font-extrabold">Tech Team</a>
        <nav class="hidden gap-4 text-sm md:flex">
            <a href="{{ route('dashboard') }}" class="hover:text-green-700">Dashboard</a>
            <a href="{{ route('quotations.index') }}" class="hover:text-green-700">Quotations</a>
            <a href="{{ route('invoices.index') }}" class="hover:text-green-700">Invoices</a>
            <span class="text-gray-400">Purchase</span><span class="text-gray-400">Stock</span><span class="text-gray-400">Parties</span><span class="text-gray-400">Reports</span>
        </nav>
        <div class="ml-auto"><input data-global-search placeholder="Ctrl+K Search" class="w-40 rounded-lg border px-3 py-2 text-sm md:w-56"></div>
    </div>
</header>
<main class="mx-auto max-w-7xl px-4 py-6">
@if(session('success')) <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div> @endif
@yield('content')
</main>
</body>
</html>
