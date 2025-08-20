<?php

namespace App\Console\Commands;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Console\Command;

class LinkStaffUsers extends Command
{
    protected $signature = 'staff:link-users {--auto : Automatically link by exact email match without prompts}';

    protected $description = 'Safely link Users to Staff records by setting staff.user_id = users.id';

    public function handle(): int
    {
        $this->info('Scanning for Staff without user linkage and Users without Staff...');

        $unlinkedStaff = Staff::whereNull('user_id')->get();
        $userIdsWithStaff = Staff::whereNotNull('user_id')->pluck('user_id')->filter()->unique();
        $usersWithoutStaff = User::whereNotIn('id', $userIdsWithStaff)->get();

        $this->line("Unlinked Staff: {$unlinkedStaff->count()} | Users without Staff: {$usersWithoutStaff->count()}");

        if ($unlinkedStaff->isEmpty() || $usersWithoutStaff->isEmpty()) {
            $this->info('Nothing to link. Exiting.');
            return self::SUCCESS;
        }

        $auto = (bool) $this->option('auto');

        $linked = 0;
        foreach ($unlinkedStaff as $staff) {
            if (!$staff->email) {
                $this->warn("Skipping staff ID {$staff->id} (no email)");
                continue;
            }

            $user = $usersWithoutStaff->firstWhere('email', $staff->email);
            if (!$user) {
                $this->line("No user email match for staff ID {$staff->id} <{$staff->email}>");
                continue;
            }

            if (!$auto) {
                $confirm = $this->confirm("Link staff #{$staff->id} ({$staff->first_name} {$staff->last_name} <{$staff->email}>) to user #{$user->id} ({$user->name} <{$user->email}>)?", true);
                if (!$confirm) {
                    $this->line('Skipped.');
                    continue;
                }
            }

            $staff->user_id = $user->id;
            $staff->save();
            $linked++;
            $this->info("Linked staff #{$staff->id} -> user #{$user->id}");
            // Remove from the candidate list to avoid double-linking
            $usersWithoutStaff = $usersWithoutStaff->where('id', '!=', $user->id);
        }

        $this->info("Completed. Linked {$linked} staff records.");
        return self::SUCCESS;
    }
}
