<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VisitService::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'patient_id' => Patient::factory(),
            'staff_id' => Staff::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'check_in_time' => null,
            'check_out_time' => null,
            'visit_notes' => $this->faker->sentence,
            'status' => 'Pending',
        ];
    }
}
