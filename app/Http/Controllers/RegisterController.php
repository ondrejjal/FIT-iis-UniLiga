<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle the registration submission
     */
    public function register(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);


        $user = User::create([
            'username' => $validated['username'],
            'first_name' => $validated['first_name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful! Welcome to UniLiga.');

    }
}
