@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-bold">New Customer</h1><p class="mb-5 text-sm text-gray-500">Create a customer before preparing quotations or invoices.</p>
@if($errors->any())<div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="post" action="{{ route('parties.customer_store') }}" class="space-y-5">@csrf
<div class="grid gap-4 rounded-xl border bg-white p-5 md:grid-cols-3">
@foreach([['company_name','Company Name',true],['contact_person','Contact Person',false],['phone','Phone',false],['mobile','Mobile',false],['email','Email',false],['city','City',false],['ntn','NTN',false],['strn','STRN',false],['payment_terms','Payment Terms',false],['credit_limit','Credit Limit',false],['tax_treatment','Tax Treatment',false]] as $field)<label>{{ $field[1] }}<input name="{{ $field[0] }}" value="{{ old($field[0]) }}" {{ $field[2]?'required':'' }} class="mt-1 w-full rounded-lg border px-3 py-2"></label>@endforeach
<label class="md:col-span-3">Address<textarea name="address" rows="2" class="mt-1 w-full rounded-lg border px-3 py-2">{{ old('address') }}</textarea></label><label class="md:col-span-3">Notes<textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border px-3 py-2">{{ old('notes') }}</textarea></label>
</div><div class="flex justify-end gap-2"><a href="{{ route('parties.customers') }}" class="rounded-lg border px-4 py-2">Cancel</a><button class="rounded-lg bg-black px-5 py-2 text-white">Save Customer</button></div></form>
@endsection
