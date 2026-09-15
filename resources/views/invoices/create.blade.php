@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold">Create Invoice</h1><p class="text-sm text-gray-500">From quotation {{ $quotation->quotation_number }}</p></div><a href="{{ route('quotations.show',$quotation) }}" class="text-sm underline">Back to quotation</a></div>
    @if($errors->any())<div class="rounded bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('invoices.storeFromQuotation',$quotation) }}" class="space-y-6">@csrf
        <div class="grid gap-4 rounded border bg-white p-5 md:grid-cols-3">
            <div><label class="block text-sm font-medium">Invoice Date</label><input name="invoice_date" type="date" value="{{ old('invoice_date',now()->toDateString()) }}" class="mt-1 w-full rounded border px-3 py-2"></div>
            <div><label class="block text-sm font-medium">Reference</label><input name="reference" value="{{ old('reference',$quotation->reference) }}" class="mt-1 w-full rounded border px-3 py-2"></div>
            <div><label class="block text-sm font-medium">Summary</label><input name="summary" value="{{ old('summary',$quotation->summary) }}" class="mt-1 w-full rounded border px-3 py-2"></div>
        </div>
        <div class="overflow-x-auto rounded border bg-white"><table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">#</th><th class="p-3 text-left">Description</th><th class="p-3 text-left">Qty</th><th class="p-3 text-left">Selling Price</th><th class="p-3 text-left">Actual Cost / Unit</th><th class="p-3 text-left">Tax</th></tr></thead><tbody>
        @foreach($quotation->items as $i=>$item)<tr class="border-t"><td class="p-3">{{ $i+1 }}</td><td class="p-3">{{ $item->description }}</td><td class="p-3">{{ rtrim(rtrim(number_format($item->quantity,4,'.',''), '0'), '.') }} {{ $item->unit }}</td><td class="p-3">PKR {{ number_format($item->selling_price,2) }}</td><td class="p-3"><input required min="0" step="0.01" type="number" name="items[{{ $i }}][actual_cost_unit]" value="{{ old("items.$i.actual_cost_unit", $item->purchase_cost + $item->delivery_cost + $item->other_cost) }}" class="w-36 rounded border px-2 py-2"></td><td class="p-3">{{ number_format($item->tax_rate,3) }}%</td></tr>@endforeach
        </tbody></table></div>
        <div class="rounded border bg-white p-5"><label class="block text-sm font-medium">Terms & Conditions</label><textarea name="terms" rows="5" class="mt-1 w-full rounded border px-3 py-2">{{ old('terms',$quotation->terms) }}</textarea></div>
        <div class="flex justify-end"><button class="rounded bg-gray-900 px-5 py-2.5 text-white">Create Invoice</button></div>
    </form>
</div>
@endsection
