<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends BaseController
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Use our base controller success handling
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->fulfill()) {
            // Use our base controller success handling
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        // Use our base controller error handling
        return redirect()->intended(route('dashboard', absolute: false))->with('error', __('Failed to verify email'));
    }
}
