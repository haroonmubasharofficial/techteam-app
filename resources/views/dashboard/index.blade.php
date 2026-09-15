@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-sm text-gray-500">Quick access to your daily sales and support work.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('quotations.create') }}" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:ring-green-600">
            <div class="text-sm text-gray-500">Sales</div><div class="mt-2 text-xl font-bold">New Quotation</div><div class="mt-2 text-xs text-gray-500">F2</div>
        </a>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200"><div class="text-sm text-gray-500">Purchase</div><div class="mt-2 text-xl font-bold">Coming next</div></div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200"><div class="text-sm text-gray-500">Stock</div><div class="mt-2 text-xl font-bold">Coming next</div></div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200"><div class="text-sm text-gray-500">Reports</div><div class="mt-2 text-xl font-bold">Coming next</div></div>
    </div>
</div>
@endsection
