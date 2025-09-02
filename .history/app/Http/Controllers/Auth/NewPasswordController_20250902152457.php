<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class NewPasswordController extends BaseController
{
    /**
     * Show the password reset page.
     */
    public function create()
    {
        return Inertia::render('auth/ResetPassword', [
            'email' => request()->email,
            'token' => request()->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Manual validation since we don't have a specific request class
            $validated = $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            // Here we will attempt to reset the user's password. If it is successful we
            // will update the password on an actual user model and persist it to the
            // database. Otherwise we will parse the error and return the response.
            $status = Password::reset(
                [
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'password_confirmation' => $validated['password_confirmation'],
                    'token' => $validated['token'],
                ],
                function ($user) use ($validated) {
                    $user->forceFill([
                        'password' => Hash::make($validated['password']),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            // If the password was successfully reset, we will redirect the user back to
            // the application's home authenticated view. If there is an error we can
            // redirect them back to where they came from with their error message.
            if ($status == Password::PASSWORD_RESET) {
                return to_route('login')->with('status', __($status));
            }

            return back()->withErrors(['email' => [__($status)]]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['email' => [__('An unexpected error occurred. Please try again.')]]);
        }
    }
}
