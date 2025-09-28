<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $path = storage_path('logs/laravel.log');
        $query = (string) $request->input('q', '');
        $level = (string) $request->input('level', ''); // e.g., error|warning|info

        $lines = [];
        if (file_exists($path)) {
            $content = @file_get_contents($path);
            if ($content !== false) {
                // Limit to last ~500 lines to avoid large payloads
                $rawLines = array_slice(preg_split('/\r?\n/', $content), -500);
                foreach ($rawLines as $line) {
                    if ($line === '') continue;
                    if ($level && stripos($line, strtolower($level)) === false) continue;
                    if ($query && stripos($line, $query) === false) continue;
                    $lines[] = $line;
                }
            }
        }

        return Inertia::render('Admin/Reports/Audit/Index', [
            'entries' => $lines,
            'filters' => [
                'q' => $query,
                'level' => $level,
            ],
        ]);
    }
}

