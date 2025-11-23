<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user's profile form
     */
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verify current password if trying to change password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password_hash)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password_hash = Hash::make($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->first_name = $validated['first_name'];
        $user->surname = $validated['surname'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'];
        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }
}
