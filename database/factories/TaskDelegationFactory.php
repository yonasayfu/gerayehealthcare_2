<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskDelegationFactory extends Factory
{
    protected $model = TaskDelegation::class;

    public function definition()
    {
        $staffIds = Staff::pluck('id');
        $userIds = User::pluck('id');

        return [
            'title' => $this->faker->sentence(4),
            'assigned_to' => $this->faker->randomElement($staffIds),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Completed']),
            'notes' => $this->faker->optional()->paragraph(),
            'created_by' => $this->faker->randomElement($userIds),
        ];
    }
}
