<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        Staff::factory()->count(20)->create()->each(function ($staff) {
            $user = User::factory()->create([
                'name' => $staff->first_name . ' ' . $staff->last_name,
                'email' => $staff->email,
                'password' => bcrypt('password'),
            ]);

            $user->assignRole(RoleEnum::STAFF);

            $staff->user_id = $user->id;
            $staff->save();
        });
    }
}