<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate the form data
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Get the remember me value
        $remember = $request->boolean('remember');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Welcome back!');
        }

        // If authentication fails, redirect back with error
        return back()->withErrors([
            'email' => 'Email not found. You need to register before logging in.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }
}