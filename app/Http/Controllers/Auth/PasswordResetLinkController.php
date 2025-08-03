<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\RequestPasswordResetRequest;

class PasswordResetLinkController extends Controller
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
        $validated = $request->validated();

        Password::sendResetLink(
            ['email' => $validated['email']]
        );

        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
