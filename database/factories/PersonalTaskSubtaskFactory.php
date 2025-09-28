<?php

namespace Database\Factories;

use App\Models\PersonalTask;
use App\Models\PersonalTaskSubtask;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalTaskSubtaskFactory extends Factory
{
    protected $model = PersonalTaskSubtask::class;

    public function definition()
    {
        $personalTaskIds = PersonalTask::pluck('id');

        return [
            'personal_task_id' => $this->faker->randomElement($personalTaskIds),
            'title' => $this->faker->sentence(3),
            'is_completed' => $this->faker->boolean(30), // 30% chance of being completed
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_completed' => true,
            ];
        });
    }
}
