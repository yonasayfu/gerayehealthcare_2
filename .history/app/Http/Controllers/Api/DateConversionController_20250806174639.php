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

    public function convertGregorianToEthiopian(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'day' => 'required|integer|min:1|max:31',
        ]);

        try {
            $gregorianDate = new \DateTime("{$request->year}-{$request->month}-{$request->day}");
            $ethiopicDate = DateTimeFactory::fromDateTime($gregorianDate);

            return response()->json([
                'year' => $ethiopicDate->year,
                'month' => $ethiopicDate->month,
                'day' => $ethiopicDate->day,
            ]);
        } catch (\Exception $e) {
            Log::error("Error converting Gregorian to Ethiopian date (detailed): " . $e->getMessage());
            return response()->json(['error' => 'Invalid Gregorian date for conversion.'], 422);
        }
    }

    public function convertEthiopianToGregorian(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:13', // Ethiopian calendar has 13 months
            'day' => 'required|integer|min:1|max:30', // Max 30 days for most months, Pagume has 5 or 6
        ]);

        try {
            $ethiopicDate = null; // Initialize $ethiopicDate

            // Handle Pagume month (13th month) which can have 5 or 6 days
            if ($request->month == 13) {
                $tempEthiopicDate = DateTimeFactory::of($request->year, $request->month, $request->day);
                // Check if the day is valid for Pagume (5 or 6 days)
                if ($request->day > $tempEthiopicDate->getDaysInMonth()) {
                    return response()->json(['error' => 'Invalid day for Pagume month.'], 422);
                }
                $ethiopicDate = $tempEthiopicDate;
            } else {
                $ethiopicDate = DateTimeFactory::of($request->year, $request->month, $request->day);
            }

            $gregorianDate = $ethiopicDate->toDateTime();

            return response()->json([
                'year' => (int)$gregorianDate->format('Y'),
                'month' => (int)$gregorianDate->format('m'),
                'day' => (int)$gregorianDate->format('d'),
            ]);
        } catch (\Exception $e) {
            Log::error("Error converting Ethiopian to Gregorian date: " . $e->getMessage());
            return response()->json(['error' => 'Invalid Ethiopian date for conversion.'], 422);
        }
    }
}
