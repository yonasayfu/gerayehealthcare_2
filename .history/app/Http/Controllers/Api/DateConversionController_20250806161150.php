<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Andegna\DateTimeFactory;

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

    public function convertToGregorian(Request $request)
    {
        $request->validate([
            'date' => 'required|string',
        ]);

        try {
            $parts = explode('-', $request->date);
            if (count($parts) !== 3) {
                throw new \Exception('Invalid date format');
            }
            if (count($parts) !== 3) {
                throw new \Exception('Invalid date format');
            }
            Log::info('Ethiopian date parts for conversion:', ['parts' => $parts]);
            $ethiopicDate = new \Andegna\DateTime(intval($parts[0]), intval($parts[1]), intval($parts[2]));
            $gregorianDate = $ethiopicDate->toGregorian();

            return response()->json([
                'gregorian_date' => $gregorianDate->format('Y-m-d'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid Ethiopian date'], 400);
        }
    }
}
