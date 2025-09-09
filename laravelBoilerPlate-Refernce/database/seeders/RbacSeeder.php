<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Ensure roles and permissions exist
        /** @var RoleService $roleService */
        $roleService = app(RoleService::class);
        $roleService->initializeDefaultRoles();

        // 2) Map known users to roles (idempotent)
        $assign = [
            'superadmin@test.com' => RoleEnum::SUPER_ADMIN->value,
            'admin@test.com' => RoleEnum::ADMIN->value,
            'ceo@test.com' => RoleEnum::ADMIN->value,
            'coo@test.com' => RoleEnum::ADMIN->value,
            'jane.smith@test.com' => RoleEnum::STAFF->value,
            'john.doe@test.com' => RoleEnum::USER->value,
            'test@example.com' => RoleEnum::USER->value,
        ];

        foreach ($assign as $email => $role) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            // Ensure the role exists (defensive; RoleService should have created these)
            Role::firstOrCreate(['name' => $role]);
            $user->syncRoles([$role]);
        }
    }
}

