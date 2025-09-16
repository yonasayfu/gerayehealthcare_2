<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Inertia\Inertia;

class EventRecommendationPublicController extends Controller
{
    /**
     * Show a public form for submitting event recommendations
     */
    public function create()
    {
        // Show only upcoming or recently created events for selection
        $today = Carbon::today()->toDateString();
        $events = Event::select('id', 'title', 'event_date')
            ->whereDate('event_date', '>=', $today)
            ->orderBy('event_date')
            ->limit(100)
            ->get();

        return Inertia::render('Public/EventRecommendationForm', [
            'events' => $events,
        ]);
    }
}

