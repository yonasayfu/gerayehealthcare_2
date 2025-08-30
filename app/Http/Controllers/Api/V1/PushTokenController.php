<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PushToken;
use Illuminate\Http\Request;

class PushTokenController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'platform' => ['nullable', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        $pt = PushToken::updateOrCreate(
            ['token' => $validated['token']],
            ['user_id' => $request->user()->id, 'platform' => $validated['platform'] ?? null, 'device_name' => $validated['device_name'] ?? null]
        );
        return response()->json(['data' => $pt]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['token' => ['required', 'string']]);
        PushToken::where('token', $request->input('token'))->where('user_id', $request->user()->id)->delete();
        return response()->noContent();
    }
}

