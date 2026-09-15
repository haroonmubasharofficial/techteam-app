<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private AuditLogService $audit) {}

    public function index()
    {
        $users = User::query()->orderBy('name')->paginate(25);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'role' => ['required', Rule::in(['admin','staff'])],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $user = User::create($data);
        $this->audit->record('user.created', $user, [], $user->only(['name','email','role','is_active']));
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            'role' => ['required', Rule::in(['admin','staff'])],
            'password' => ['nullable','string','min:8','confirmed'],
            'is_active' => ['nullable','boolean'],
        ]);
        if ($user->is($request->user()) && (!$request->boolean('is_active') || $data['role'] !== 'admin')) {
            return back()->withErrors(['user' => 'You cannot deactivate or remove admin access from your own account.'])->withInput();
        }
        $old = $user->only(['name','email','role','is_active']);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->is_active = $request->boolean('is_active');
        if (!empty($data['password'])) $user->password = $data['password'];
        $user->save();
        $this->audit->record('user.updated', $user, $old, $user->only(['name','email','role','is_active']));
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function toggleActive(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }
        $old = ['is_active' => $user->is_active];
        $user->is_active = !$user->is_active;
        $user->save();
        $this->audit->record('user.status_changed', $user, $old, ['is_active' => $user->is_active]);
        return back()->with('success', $user->is_active ? 'User activated.' : 'User deactivated.');
    }
}
