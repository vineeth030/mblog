<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    private const USERNAME = 'admin';
    private const PASSWORD = 'password123';

    public function showLogin(): Response|RedirectResponse
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('Admin/Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (
            $request->username !== self::USERNAME ||
            $request->password !== self::PASSWORD
        ) {
            return back()->withErrors(['username' => 'Invalid credentials.'])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        return redirect()->intended(route('admin.blog-posts.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->regenerate();

        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }
}
