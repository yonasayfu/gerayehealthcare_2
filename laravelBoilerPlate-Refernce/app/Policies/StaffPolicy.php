
<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;

class StaffPolicy extends BasePolicy
{
    /**
     * The model name for generating permissions.
     *
     * @var string
     */
    protected $modelName = 'staff';

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('staff-view-any');
    }
}
