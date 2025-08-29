<?php

namespace App\Services;

use App\Events\UserPresenceChanged;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'user',
            'is_online' => true,
        ]);
        Auth::login($user);
        broadcast(new UserPresenceChanged($user->id, 'online'));
        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->update(['is_online' => true]);
            broadcast(new UserPresenceChanged(Auth::id(), 'online'));
            return redirect()->route('dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
        if ($user) {
            $user->update(['is_online' => false]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        broadcast(new UserPresenceChanged($userId, 'offline'));
        return redirect()->route('login');
    }
}
