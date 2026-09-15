@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto space-y-5">
<h1 class="text-2xl font-bold">Delivery Challans</h1>
<form class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search challan or customer" class="w-full max-w-md rounded border px-3 py-2"><button class="rounded border px-4 py-2">Search</button></form>
<div class="overflow-x-auto rounded border bg-white"><table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">Challan</th><th class="p-3 text-left">Date</th><th class="p-3 text-left">Customer</th><th class="p-3 text-left">Invoice</th><th class="p-3 text-left">Status</th><th></th></tr></thead><tbody>
@forelse($challans as $challan)<tr class="border-t"><td class="p-3 font-medium">{{ $challan->challan_number }}</td><td class="p-3">{{ $challan->challan_date?->format('d-m-Y') }}</td><td class="p-3">{{ $challan->customer->company_name }}</td><td class="p-3">{{ $challan->invoice?->invoice_number ?? '—' }}</td><td class="p-3">{{ ucfirst($challan->status) }}</td><td class="p-3 text-right"><a class="underline" href="{{ route('delivery_challans.show',$challan) }}">View</a></td></tr>@empty<tr><td colspan="6" class="p-8 text-center text-gray-500">No delivery challans yet.</td></tr>@endforelse
</tbody></table></div>{{ $challans->links() }}
</div>
@endsection
