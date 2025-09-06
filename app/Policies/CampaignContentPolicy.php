<?php

namespace App\Policies;

use App\Models\CampaignContent;
use App\Models\User;

class CampaignContentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_campaign_contents');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CampaignContent $campaignContent): bool
    {
        return $user->hasPermissionTo('view_campaign_contents');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_campaign_contents');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CampaignContent $campaignContent): bool
    {
        return $user->hasPermissionTo('update_campaign_contents');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CampaignContent $campaignContent): bool
    {
        return $user->hasPermissionTo('delete_campaign_contents');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CampaignContent $campaignContent): bool
    {
        return $user->hasPermissionTo('restore_campaign_contents');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CampaignContent $campaignContent): bool
    {
        return $user->hasPermissionTo('force_delete_campaign_contents');
    }
}
