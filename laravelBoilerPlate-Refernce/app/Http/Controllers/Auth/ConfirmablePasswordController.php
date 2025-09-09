<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmablePasswordController extends BaseController
{
    /**
     * Show the confirm password page.
     */
    public function show(): Response
    {
        // Use our base controller success handling
        return Inertia::render('auth/ConfirmPassword');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            // Use our base controller error handling
            return back()->withErrors([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        // Use our base controller success handling
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
