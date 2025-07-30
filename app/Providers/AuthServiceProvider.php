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
