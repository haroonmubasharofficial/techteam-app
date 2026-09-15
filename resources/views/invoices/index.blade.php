@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    <div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold">Invoices</h1><p class="text-sm text-gray-500">Sales invoices and actual profit.</p></div></div>
    <form class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search invoice or customer" class="w-full max-w-md rounded border px-3 py-2"><button class="rounded border px-4 py-2">Search</button></form>
    <div class="overflow-x-auto rounded border bg-white"><table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">Invoice</th><th class="p-3 text-left">Date</th><th class="p-3 text-left">Customer</th><th class="p-3 text-right">Total</th><th class="p-3 text-right">Actual Profit</th><th class="p-3 text-left">Status</th><th class="p-3"></th></tr></thead><tbody>
    @forelse($invoices as $invoice)<tr class="border-t"><td class="p-3 font-medium">{{ $invoice->invoice_number }}</td><td class="p-3">{{ $invoice->invoice_date?->format('d-m-Y') }}</td><td class="p-3">{{ $invoice->customer->company_name }}</td><td class="p-3 text-right">PKR {{ number_format($invoice->total_amount,2) }}</td><td class="p-3 text-right">PKR {{ number_format($invoice->actual_profit,2) }}</td><td class="p-3">{{ ucfirst($invoice->status) }}</td><td class="p-3 text-right"><a class="underline" href="{{ route('invoices.show',$invoice) }}">View</a></td></tr>@empty<tr><td colspan="7" class="p-8 text-center text-gray-500">No invoices yet.</td></tr>@endforelse
    </tbody></table></div>
    {{ $invoices->links() }}
</div>
@endsection
