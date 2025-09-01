<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\RequestPasswordResetRequest;

class PasswordResetLinkController extends BaseController
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RequestPasswordResetRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $status = Password::sendResetLink(
                ['email' => $validated['email']]
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', __('A reset link has been sent to your email address.'));
            }

            return back()->with('status', __('A reset link will be sent if the account exists.'));
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => __('Unable to send reset link. Please try again.'),
            ]);
        }
    }
}
