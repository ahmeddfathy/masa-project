<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
  public function show()
  {
    return view('profile.show', [
      'user' => Auth::user()
    ]);
  }

  public function edit()
  {
    return view('profile.edit', [
      'user' => Auth::user()
    ]);
  }

  public function update(Request $request)
  {
    $user = Auth::user();

    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
      'phone' => ['nullable', 'string', 'max:20'],
      'address' => ['nullable', 'string', 'max:500'],
    ]);

    $user->update($validated);

    return redirect()->route('profile.show')
      ->with('success', 'Profile updated successfully.');
  }

  public function updatePassword(Request $request)
  {
    $validated = $request->validate([
      'current_password' => ['required', 'current_password'],
      'password' => ['required', 'confirmed', Password::defaults()],
    ]);

    Auth::user()->update([
      'password' => Hash::make($validated['password']),
    ]);

    return back()->with('success', 'Password updated successfully.');
  }
}
