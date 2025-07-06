<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PlaceholderController extends Controller
{
    public function index()
    {
        return Inertia::render('Placeholder/Index');
    }
}