<?php

// Change this namespace
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller; // Add this line
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MyAvailabilityController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->staff;

        // Update the render path to the new frontend location
        return Inertia::render('Staff/MyAvailability/Index', [
            'staff' => $staff,
        ]);
    }
}