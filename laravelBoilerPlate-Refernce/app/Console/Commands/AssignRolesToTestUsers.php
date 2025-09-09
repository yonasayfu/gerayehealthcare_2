<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRolesToTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-users:assign-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign appropriate roles to test users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Assigning roles to test users...');

        // Define user-role mappings
        $userRoles = [
            'superadmin@test.com' => 'super-admin',
            'admin@test.com' => 'admin',
            'ceo@test.com' => 'admin', // CEO gets admin role for now
            'coo@test.com' => 'admin', // COO gets admin role for now
            'john.doe@test.com' => 'user',
            'jane.smith@test.com' => 'staff',
        ];

        foreach ($userRoles as $email => $roleName) {
            // Find the user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->warn("User with email {$email} not found.");
                continue;
            }

            // Check if role exists
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->warn("Role {$roleName} not found.");
                continue;
            }

            // Assign role to user
            $user->syncRoles([$roleName]);

            $this->info("Role {$roleName} assigned to user {$email} successfully.");
        }

        $this->info('✅ All test users have been assigned roles!');
        return 0;
    }
}
