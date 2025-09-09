<?php

namespace App\Providers;

use App\Enums\RoleEnum;
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
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Patient::class => \App\Policies\PatientPolicy::class,
        \App\Models\Staff::class => \App\Policies\StaffPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Super Admin bypass - Super Admins can do everything
        Gate::before(function (\App\Models\User $user, string $ability) {
            if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
                return true;
            }
        });

        $this->registerPolicies();

        // Define additional gates
        Gate::define('view-admin-dashboard', function (\App\Models\User $user) {
            return $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value]);
        });

        Gate::define('manage-users', function (\App\Models\User $user) {
            return $user->hasRole(RoleEnum::SUPER_ADMIN->value);
        });

        Gate::define('manage-roles', function (\App\Models\User $user) {
            return $user->hasRole(RoleEnum::SUPER_ADMIN->value);
        });

        Gate::define('view-reports', function (\App\Models\User $user) {
            return $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value]);
        });

        Gate::define('manage-staff', function (\App\Models\User $user) {
            return $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value]);
        });
    }
}
