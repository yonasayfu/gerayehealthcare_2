<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GlobalSearchService;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    protected $globalSearchService;

    public function __construct(GlobalSearchService $globalSearchService)
    {
        $this->globalSearchService = $globalSearchService;
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $results = $this->globalSearchService->search($query);

        return response()->json($results);
    }
}
