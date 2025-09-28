<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        \App\Models\Referral::class => \App\Policies\ReferralPolicy::class,
        \App\Models\VisitService::class => \App\Policies\VisitServicePolicy::class,
        \App\Models\MedicalDocument::class => \App\Policies\MedicalDocumentPolicy::class,
        \App\Models\Invoice::class => \App\Policies\InvoicePolicy::class,
        \App\Models\InsuranceClaim::class => \App\Policies\InsuranceClaimPolicy::class,
        \App\Models\Message::class => \App\Policies\MessagePolicy::class,
        \App\Models\Patient::class => \App\Policies\PatientPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\InventoryItem::class => \App\Policies\InventoryItemPolicy::class,
        \App\Models\InventoryRequest::class => \App\Policies\InventoryRequestPolicy::class,
        \App\Models\InventoryTransaction::class => \App\Policies\InventoryTransactionPolicy::class,
        \App\Models\InventoryMaintenanceRecord::class => \App\Policies\InventoryMaintenanceRecordPolicy::class,
        \App\Models\InventoryAlert::class => \App\Policies\InventoryAlertPolicy::class,
        \App\Models\Supplier::class => \App\Policies\SupplierPolicy::class,
        \Spatie\Permission\Models\Role::class => \App\Policies\RolePolicy::class,
        \App\Models\TaskDelegation::class => \App\Policies\TaskDelegationPolicy::class,
        // High-priority additions for better security coverage
        \App\Models\Staff::class => \App\Policies\StaffPolicy::class,
        \App\Models\Partner::class => \App\Policies\PartnerPolicy::class,
        \App\Models\MarketingCampaign::class => \App\Policies\MarketingCampaignPolicy::class,
        \App\Models\InsurancePolicy::class => \App\Policies\InsurancePolicyPolicy::class,
        \App\Models\Event::class => \App\Policies\EventPolicy::class,
        \App\Models\Service::class => \App\Policies\ServicePolicy::class,
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

        // All authenticated staff can communicate (DM) with any other user except themselves
        Gate::define('communicate', function (User $user, User $other) {
            return $user->id !== $other->id;
        });
    }
}
