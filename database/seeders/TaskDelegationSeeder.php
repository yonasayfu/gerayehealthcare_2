<?php

namespace Database\Seeders;

use App\Models\TaskDelegation;
use Illuminate\Database\Seeder;

class TaskDelegationSeeder extends Seeder
{
    public function run(): void
    {
        TaskDelegation::factory()->count(6)->create();
    }
}
