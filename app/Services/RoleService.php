<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService extends BaseService
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function create(array $data): Role
    {
        $role = parent::create(['name' => $data['name']]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->getById($id);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        return $role;
    }

    public function delete(int $id): void
    {
        $role = $this->getById($id);

        if (in_array($role->name, ['Super Admin', 'Admin', 'Staff'])) {
            // In a real application, you might want to throw an exception here
            // and handle it in the controller to show an error message.
            return;
        }

        parent::delete($id);
    }
}
