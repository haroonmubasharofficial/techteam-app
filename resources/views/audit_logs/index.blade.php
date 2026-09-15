@extends('layouts.app')
@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
    <div><h1 class="text-2xl font-bold">Audit Log</h1><p class="text-sm text-gray-600">Administrative history of important system actions.</p></div>
</div>
<form method="GET" class="mb-5 grid gap-3 rounded-xl border bg-white p-4 md:grid-cols-5">
    <input name="q" value="{{ request('q') }}" placeholder="Action / route" class="rounded-lg border px-3 py-2 text-sm">
    <select name="user_id" class="rounded-lg border px-3 py-2 text-sm"><option value="">All users</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>@endforeach</select>
    <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border px-3 py-2 text-sm">
    <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border px-3 py-2 text-sm">
    <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
</form>
<div class="overflow-x-auto rounded-xl border bg-white"><table class="min-w-full text-sm"><thead class="border-b bg-gray-50 text-left"><tr><th class="px-4 py-3">Date/Time</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Action</th><th class="px-4 py-3">Record</th><th class="px-4 py-3">IP</th><th class="px-4 py-3"></th></tr></thead><tbody class="divide-y">
@forelse($logs as $log)<tr><td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i:s') }}</td><td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td><td class="px-4 py-3 font-medium">{{ $log->action }}</td><td class="px-4 py-3">{{ class_basename($log->auditable_type ?? '') }}{{ $log->auditable_id ? ' #'.$log->auditable_id : '' }}</td><td class="px-4 py-3">{{ $log->ip_address }}</td><td class="px-4 py-3 text-right"><a href="{{ route('audit_logs.show',$log) }}" class="underline">View</a></td></tr>@empty<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No audit records found.</td></tr>@endforelse
</tbody></table></div><div class="mt-4">{{ $logs->links() }}</div>
@endsection
