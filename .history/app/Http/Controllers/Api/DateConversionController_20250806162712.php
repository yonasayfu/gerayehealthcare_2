<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Andegna\DateTimeFactory;
use Illuminate\Support\Facades\Log; // Import Log facade

class DateConversionController extends Controller
{
    public function convertToEthiopian(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        try {
            $gregorianDate = new \DateTime($request->date);
            $ethiopicDate = DateTimeFactory::fromDateTime($gregorianDate);

            return response()->json([
                'ethiopian_date' => $ethiopicDate->format('Y-m-d'),
            ]);
        } catch (\Exception $e) {
            Log::error("Error converting Gregorian to Ethiopian date: " . $e->getMessage());
            return response()->json(['error' => 'Invalid Gregorian date for conversion.'], 400);
        }
    }

    // Removed convertToGregorian as it will be handled on frontend
}
