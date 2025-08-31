<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\MarketingBudget::class => \App\Policies\MarketingBudgetPolicy::class,
        \App\Models\CampaignContent::class => \App\Policies\CampaignContentPolicy::class,
        \App\Models\MarketingTask::class => \App\Policies\MarketingTaskPolicy::class,
        \App\Models\ReferralDocument::class => \App\Policies\ReferralDocumentPolicy::class,
        \App\Models\VisitService::class => \App\Policies\VisitServicePolicy::class,
        \App\Models\MedicalDocument::class => \App\Policies\MedicalDocumentPolicy::class,
        \App\Models\Invoice::class => \App\Policies\InvoicePolicy::class,
        \App\Models\InsuranceClaim::class => \App\Policies\InsuranceClaimPolicy::class,
        \App\Models\User::class => \App\Policies\MessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function (\App\Models\User $user, string $ability) {
            if ($user->hasRole(\App\Enums\RoleEnum::SUPER_ADMIN->value)) {
                return true;
            }
        });
    }
}
