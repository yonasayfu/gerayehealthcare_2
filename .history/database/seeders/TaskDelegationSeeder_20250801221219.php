<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskDelegation;

class TaskDelegationSeeder extends Seeder
{
    public function run(): void
    {
        TaskDelegation::factory()->count(6)->create();
    }
}