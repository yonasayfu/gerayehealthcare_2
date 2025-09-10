<?php

namespace Database\Factories;

use App\Models\PersonalTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalTaskFactory extends Factory
{
    protected $model = PersonalTask::class;

    public function definition()
    {
        $userIds = User::pluck('id');

        return [
            'user_id' => $this->faker->randomElement($userIds),
            'title' => $this->faker->sentence(4),
            'notes' => $this->faker->optional()->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'is_completed' => $this->faker->boolean(30), // 30% chance of being completed
            'is_important' => $this->faker->boolean(20), // 20% chance of being important
            'reminder_at' => $this->faker->optional()->dateTimeBetween('now', '+1 week'),
            'reminded_at' => null,
            'my_day_for' => $this->faker->optional()->date(),
            'recurrence_type' => 'none', // Default to 'none' instead of null
            'recurrence_weekdays' => null,
            'task_date' => $this->faker->date(),
            'start_time' => $this->faker->optional()->time(),
            'end_time' => $this->faker->optional()->time(),
            'estimated_duration_minutes' => $this->faker->optional()->numberBetween(15, 240),
            'daily_notes' => $this->faker->optional()->sentence(),
            'task_category' => $this->faker->optional()->randomElement(['Work', 'Personal', 'Health', 'Finance', 'Learning']),
            'priority_level' => $this->faker->numberBetween(1, 5),
            'is_billable' => $this->faker->boolean(10), // 10% chance of being billable
        ];
    }

    public function important()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_important' => true,
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_completed' => true,
            ];
        });
    }

    public function withReminder()
    {
        return $this->state(function (array $attributes) {
            return [
                'reminder_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            ];
        });
    }

    public function recurring()
    {
        return $this->state(function (array $attributes) {
            return [
                'recurrence_type' => 'weekly',
                'recurrence_weekdays' => json_encode(['monday', 'wednesday', 'friday']),
            ];
        });
    }
}