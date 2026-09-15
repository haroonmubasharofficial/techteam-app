@extends('layouts.app')
@section('content')
<div class="mb-5"><h1 class="text-2xl font-bold">New User</h1><p class="text-sm text-gray-500">Create a staff or administrator account.</p></div>
@if($errors->any())<div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('users.store') }}" class="max-w-2xl space-y-4 rounded-xl border bg-white p-5">@csrf
<div class="grid gap-4 md:grid-cols-2"><div><label class="text-sm font-medium">Name</label><input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border px-3 py-2"></div><div><label class="text-sm font-medium">Email</label><input name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border px-3 py-2"></div><div><label class="text-sm font-medium">Role</label><select name="role" class="mt-1 w-full rounded-lg border px-3 py-2"><option value="staff">Staff</option><option value="admin" @selected(old('role')==='admin')>Admin</option></select></div><div><label class="text-sm font-medium">Password</label><input name="password" type="password" required minlength="8" class="mt-1 w-full rounded-lg border px-3 py-2"></div><div><label class="text-sm font-medium">Confirm Password</label><input name="password_confirmation" type="password" required minlength="8" class="mt-1 w-full rounded-lg border px-3 py-2"></div></div>
<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Active</label>
<div class="flex gap-2"><button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Create User</button><a href="{{ route('users.index') }}" class="rounded-lg border px-4 py-2 text-sm">Cancel</a></div></form>
@endsection
