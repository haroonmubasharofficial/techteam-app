@extends('layouts.app')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div><h1 class="text-2xl font-bold">Users</h1><p class="text-sm text-gray-500">Manage system access and roles.</p></div>
    <a href="{{ route('users.create') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">+ New User</a>
</div>
@if($errors->any())<div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
<div class="overflow-x-auto rounded-xl border bg-white"><table class="w-full text-sm"><thead class="border-b bg-gray-50 text-left"><tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Role</th><th class="p-3">Status</th><th class="p-3">Actions</th></tr></thead><tbody class="divide-y">
@forelse($users as $user)
<tr><td class="p-3 font-medium">{{ $user->name }}</td><td class="p-3">{{ $user->email }}</td><td class="p-3 uppercase">{{ $user->role }}</td><td class="p-3">{{ $user->is_active ? 'Active' : 'Inactive' }}</td><td class="p-3"><div class="flex flex-wrap gap-2"><a class="rounded border px-3 py-1.5" href="{{ route('users.edit',$user) }}">Edit</a>@if(auth()->id() !== $user->id)<form method="POST" action="{{ route('users.toggle',$user) }}" class="inline">@csrf @method('PATCH')<button class="rounded border px-3 py-1.5">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button></form>@endif</div></td></tr>
@empty<tr><td colspan="5" class="p-6 text-center text-gray-500">No users found.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
