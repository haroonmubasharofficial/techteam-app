<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private AuditLogService $audit) {}

    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required','email'], 'password' => ['required','string']]);
        $user = User::where('email', $credentials['email'])->where('is_active', true)->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $this->audit->record('auth.login', $user);
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) $this->audit->record('auth.logout', $user);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
