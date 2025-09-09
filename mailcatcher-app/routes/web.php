<?php

use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EmailController::class, 'index'])->name('inbox');
Route::get('/emails/{email}', [EmailController::class, 'show'])->name('emails.show');