<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $isProfileEdit = true;
        
        // Safety check for Spatie Roles
        $roles = (class_exists(Role::class) && $user->hasRole('admin')) ? Role::all() : [];
        
        // As requested: using the admin.users.edit view
        return view('admin.users.edit', compact('user', 'roles', 'isProfileEdit'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);

        // 1. Handle Password
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // 2. Handle Profile Picture
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = basename($path);
        }

        // 3. Update Basic Info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        // Redirect back to the profile edit route
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}