<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class NormalizeRoleNames extends Command
{
    protected $signature = 'roles:normalize {--dry-run : Show changes without applying them}';

    protected $description = 'Normalize role names to canonical lowercase (super-admin, admin, staff, etc.) and merge duplicates safely.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $this->info('Normalizing role names...');

        // Ensure canonical roles exist
        $canonical = RoleEnum::values();
        foreach ($canonical as $name) {
            if (!Role::where('name', $name)->exists()) {
                $this->line("Ensuring role exists: {$name}");
                if (!$dry) {
                    Role::findOrCreate($name, 'web');
                }
            }
        }

        // Map common aliases to canonical
        $aliases = [
            'Super Admin' => 'super-admin',
            'Super Administrator' => 'super-admin',
            'Admin' => 'admin',
            'Administrator' => 'admin',
            'Staff' => 'staff',
            'Guest' => 'guest',
            'CEO' => 'ceo',
            'COO' => 'coo',
        ];

        $roles = Role::query()->get();
        $changes = 0;

        foreach ($roles as $role) {
            $original = $role->name;
            // Decide target name
            $target = $aliases[$original] ?? $original;
            $target = Str::of($target)->lower()->replace(' ', '-')->value();

            if ($target === $original) {
                continue;
            }

            $this->line("Role '{$original}' => '{$target}'");

            $existing = Role::where('name', $target)->first();
            if ($existing) {
                // Merge assignments into existing role
                if (!$dry) {
                    DB::table('model_has_roles')
                        ->where('role_id', $role->id)
                        ->update(['role_id' => $existing->id]);
                    $role->delete();
                }
                $this->line("  Merged into existing '{$target}'");
            } else {
                // Rename role directly
                if (!$dry) {
                    $role->name = $target;
                    $role->save();
                }
                $this->line("  Renamed to '{$target}'");
            }

            $changes++;
        }

        if (!$dry) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $this->info("Done. Changes: {$changes}. Cache cleared: " . ($dry ? 'no (dry-run)' : 'yes'));
        return self::SUCCESS;
    }
}

