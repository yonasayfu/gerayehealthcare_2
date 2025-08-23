<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheExpensiveQueries
{
    /**
     * Handle an incoming request and cache expensive data
     */
    public function handle(Request $request, Closure $next)
    {
        // Pre-cache common dropdown data that's used across multiple pages
        $this->preCacheCommonData();

        $response = $next($request);

        return $response;
    }

    /**
     * Pre-cache common data used in dropdowns
     */
    private function preCacheCommonData()
    {
        // Cache active staff for dropdowns (5 minutes)
        Cache::remember('dropdown_active_staff', 300, function () {
            return \App\Models\Staff::select('id', 'first_name', 'last_name', 'position')
                ->where('status', 'Active')
                ->orderBy('first_name')
                ->limit(500)
                ->get();
        });

        // Cache patients for dropdowns (5 minutes)
        Cache::remember('dropdown_patients', 300, function () {
            return \App\Models\Patient::select('id', 'full_name', 'phone_number')
                ->orderBy('full_name')
                ->limit(1000)
                ->get();
        });

        // Cache insurance companies (15 minutes - changes less frequently)
        Cache::remember('dropdown_insurance_companies', 900, function () {
            return \App\Models\InsuranceCompany::select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        // Cache insurance policies (15 minutes)
        Cache::remember('dropdown_insurance_policies', 900, function () {
            return \App\Models\InsurancePolicy::select('id', 'service_type', 'coverage_percentage')
                ->orderBy('service_type')
                ->get();
        });

        // Cache services for dropdowns (15 minutes)
        Cache::remember('dropdown_services', 900, function () {
            return \App\Models\Service::select('id', 'name', 'price')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        // Cache events for dropdowns (10 minutes)
        Cache::remember('dropdown_events', 600, function () {
            return \App\Models\Event::select('id', 'title', 'event_date')
                ->orderBy('event_date', 'desc')
                ->limit(100)
                ->get();
        });
    }
}
