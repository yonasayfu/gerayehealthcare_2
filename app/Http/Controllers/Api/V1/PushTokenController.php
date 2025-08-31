<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PushToken;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\StorePushTokenRequest;
use App\Http\Requests\Api\V1\DeletePushTokenRequest;

class PushTokenController extends Controller
{
    public function store(StorePushTokenRequest $request)
    {
        $pt = PushToken::updateOrCreate(
            ['token' => $request->validated()['token']],
            ['user_id' => $request->user()->id, 'platform' => $request->validated()['platform'] ?? null, 'device_name' => $request->validated()['device_name'] ?? null]
        );
        return response()->json(['data' => $pt]);
    }

    public function destroy(DeletePushTokenRequest $request)
    {
        PushToken::where('token', $request->validated()['token'])->where('user_id', $request->user()->id)->delete();
        return response()->noContent();
    }
}
