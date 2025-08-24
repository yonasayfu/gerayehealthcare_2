<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GlobalSearchService;

class GlobalSearchController extends Controller
{
    protected $globalSearchService;

    public function __construct(GlobalSearchService $globalSearchService)
    {
        $this->globalSearchService = $globalSearchService;
    }

    public function search(Request $request)
    {
        // Just return empty results for now - UI only
        return response()->json([]);
    }
}
