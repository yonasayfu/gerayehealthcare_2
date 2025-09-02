<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializeRbacCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:initialize {--force : Force initialization even if roles exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize RBAC system with default roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing RBAC system...');

        // Check if roles already exist
        if (\Spatie\Permission\Models\Role::count() > 0 && ! $this->option('force')) {
            if (! $this->confirm('Roles already exist. Do you want to continue?')) {
                $this->info('RBAC initialization cancelled.');

                return 0;
            }
        }

        try {
            $roleService = app(\App\Services\RoleService::class);
            $roleService->initializeDefaultRoles();

            $this->info('✅ Default roles and permissions created successfully!');

            // Display created roles
            $roles = \Spatie\Permission\Models\Role::with('permissions')->get();

            $this->table(
                ['Role', 'Permissions Count', 'Permissions'],
                $roles->map(function ($role) {
                    return [
                        $role->name,
                        $role->permissions->count(),
                        $role->permissions->pluck('name')->join(', '),
                    ];
                })
            );

            $this->info('🎉 RBAC system initialized successfully!');
            $this->info('You can now assign roles to users and manage permissions.');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to initialize RBAC system: '.$e->getMessage());

            return 1;
        }
    }
}
