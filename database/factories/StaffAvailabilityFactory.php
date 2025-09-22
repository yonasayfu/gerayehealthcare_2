<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\StaffAvailability;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffAvailabilityFactory extends Factory
{
    protected $model = StaffAvailability::class;

    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'start_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_time' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'status' => 'Available',
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Available',
            ];
        });
    }

    public function unavailable()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Unavailable',
            ];
        });
    }
}
