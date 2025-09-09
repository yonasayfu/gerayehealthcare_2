
<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * The model class name. This should be overridden in child policies.
     *
     * @var string|null
     */
    protected static $model = null;

    /**
     * Get the permission string from the policy method name.
     *
     * @param  string  $ability
     * @return string
     */
    protected function getPermission(string $ability): string
    {
        // e.g., 'PatientPolicy' -> 'patient'
        $modelName = Str::kebab(str_replace('Policy', '', class_basename($this)));

        $actionMap = [
            'viewAny' => 'view-any',
            'view' => 'view',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
            'restore' => 'restore',
            'forceDelete' => 'force-delete',
        ];

        $action = $actionMap[$ability] ?? $ability;

        return "{$modelName}-{$action}";
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can($this->getPermission('viewAny'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can($this->getPermission('create'));
    }

    /**
     * Dynamically handle authorization checks for instance-based abilities.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return bool
     */
    public function __call($name, $arguments)
    {
        $user = $arguments[0];

        if (! $user instanceof User) {
            return false;
        }

        return $user->can($this->getPermission($name));
    }
}
