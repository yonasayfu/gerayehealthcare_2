<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Andegna\DateTimeFactory;
use Illuminate\Support\Facades\Log; // Import Log facade
// Removed explicit import of Andegna\DateTime to avoid potential conflict

class DateConversionController extends Controller
{
    public function convertToEthiopian(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $gregorianDate = new \DateTime($request->date);
        $ethiopicDate = DateTimeFactory::fromDateTime($gregorianDate);

        return response()->json([
            'ethiopian_date' => $ethiopicDate->format('Y-m-d'),
        ]);
    }

    // Removed convertToGregorian as it will be handled on frontend
}
