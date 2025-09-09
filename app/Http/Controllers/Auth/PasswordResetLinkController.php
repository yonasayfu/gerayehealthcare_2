<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create()
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => request()->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Manual validation since we don't have a specific request class
            $validated = $request->validate([
                'email' => 'required|email',
            ]);

            $status = Password::sendResetLink(
                ['email' => $validated['email']]
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', __('A reset link has been sent to your email address.'));
            }

            return back()->with('status', __('A reset link will be sent if the account exists.'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => __('Unable to send reset link. Please try again.'),
            ]);
        }
    }
}
