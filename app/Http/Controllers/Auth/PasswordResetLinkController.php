<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'secret_code' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if the secret code matches the admin secret code
        if ($request->secret_code !== config('auth.admin_secret_code')) {
            return back()->withInput($request->only('username'))
                ->withErrors(['secret_code' => 'Invalid admin secret code.']);
        }

        // Find the user by username
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'No user found with that username.']);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Password has been reset successfully.');
    }
}
