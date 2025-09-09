<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index()
    {
        $emails = Email::latest()->get();

        return view('emails.inbox', compact('emails'));
    }

    public function show(Email $email)
    {
        return view('emails.show', compact('email'));
    }
}