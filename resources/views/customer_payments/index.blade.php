@extends('layouts.app')
@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div><h1 class="text-2xl font-bold">Customer Payments</h1><p class="text-sm text-gray-500">Receipts posted against customer accounts.</p></div>
    <div class="flex gap-2"><a href="{{ route('receivables.index') }}" class="rounded-lg border px-4 py-2 text-sm">Receivables</a><a href="{{ route('customer_payments.create') }}" class="rounded-lg bg-black px-4 py-2 text-sm text-white">New Receipt</a></div>
</div>
<form class="my-4"><input name="q" value="{{ request('q') }}" placeholder="Search receipt or customer" class="w-full rounded-lg border px-3 py-2"></form>
<div class="overflow-x-auto rounded-xl border bg-white"><table class="min-w-full text-sm"><thead class="border-b bg-gray-50"><tr><th class="p-3 text-left">Receipt</th><th class="p-3 text-left">Date</th><th class="p-3 text-left">Customer</th><th class="p-3 text-left">Method</th><th class="p-3 text-right">Amount</th><th></th></tr></thead><tbody>
@forelse($payments as $payment)<tr class="border-b last:border-0"><td class="p-3"><a class="font-semibold underline" href="{{ route('customer_payments.show',$payment) }}">{{ $payment->receipt_number }}</a></td><td class="p-3">{{ $payment->payment_date->format('d-M-Y') }}</td><td class="p-3">{{ $payment->customer->company_name }}</td><td class="p-3">{{ $payment->payment_method }}</td><td class="p-3 text-right">PKR {{ number_format($payment->amount,2) }}</td><td class="p-3 text-right"><a class="underline" href="{{ route('customer_payments.print',$payment) }}">Print</a></td></tr>@empty<tr><td colspan="6" class="p-8 text-center text-gray-500">No customer payments found.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
