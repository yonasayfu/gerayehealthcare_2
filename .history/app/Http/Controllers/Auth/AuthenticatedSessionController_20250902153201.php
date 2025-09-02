<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends BaseController
{
    public function __construct()
    {
        // Skip parent constructor since we don't need service injection for auth
        // This prevents the need to pass all those parameters
    }

    /**
     * Show the login page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => request()->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validate using LoginRequest
            $loginRequest = new LoginRequest();
            $loginRequest->merge($request->all());

            // Manually trigger validation
            $validated = $request->validate((new LoginRequest())->rules());

            // Authenticate the user
            $loginRequest->authenticate();

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->withInput(['email' => $request->input('email')]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request, $id = null): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
