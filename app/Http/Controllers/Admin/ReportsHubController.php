<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ReportsHubController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value]);
    }

    public function index()
    {
        // Centralized list of available reports with their export route names and supported filters
        $reports = [
            [
                'key' => 'service-volume',
                'title' => 'Service Volume',
                'description' => 'Visits and patients over time with service category breakdown.',
                'export_route' => 'admin.reports.service-volume.export',
                'filters' => ['date_from', 'date_to', 'granularity', 'service_category', 'is_event_service'],
            ],
            [
                'key' => 'revenue-ar',
                'title' => 'Revenue & AR',
                'description' => 'Billing, collections, and outstanding receivables trends.',
                'export_route' => 'admin.reports.revenue-ar.export',
                'filters' => ['date_from', 'date_to', 'granularity'],
            ],
            [
                'key' => 'marketing-roi',
                'title' => 'Marketing ROI',
                'description' => 'Campaign performance across platforms and ROI.',
                'export_route' => 'admin.reports.marketing-roi.export',
                'filters' => ['date_from', 'date_to', 'granularity', 'platform'],
            ],
        ];

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
            'defaults' => [
                'granularity' => 'quarter',
            ],
        ]);
    }
}

