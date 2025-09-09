<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends BaseController
{
    /**
     * Show the email verification prompt page.
     */
    public function __invoke(Request $request): RedirectResponse | Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Use our base controller success handling
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Use our base controller success handling
        return Inertia::render('auth/VerifyEmail', ['status' => $request->session()->get('status')]);
    }
}
