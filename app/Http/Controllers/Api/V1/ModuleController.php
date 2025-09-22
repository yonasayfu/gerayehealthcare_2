<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\ModuleAccess;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json([
            'data' => ModuleAccess::forUser($request->user()),
        ]);
    }
}
