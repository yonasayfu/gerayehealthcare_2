<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\QuoteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');

    // Quotes management
    Route::get('settings/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::post('settings/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::patch('settings/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('settings/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::post('settings/quotes/{id}/restore', [QuoteController::class, 'restore'])->name('quotes.restore');
});
