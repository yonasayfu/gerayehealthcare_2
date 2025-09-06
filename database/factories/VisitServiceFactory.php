<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisitService>
 */
class VisitServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheduled = $this->faker->dateTimeBetween('-12 months', '+1 month');
        $checkIn = $this->faker->optional(0.7)->dateTimeBetween($scheduled, (clone $scheduled)->modify('+30 minutes'));

        $serviceId = Service::query()->inRandomOrder()->value('id');
        if (! $serviceId) {
            $service = Service::query()->create([
                'name' => 'General Care',
                'description' => 'Auto-created service for seeding',
                'category' => 'General',
                'price' => 500,
                'duration' => 60,
                'is_active' => true,
            ]);
            $serviceId = $service->id;
        }

        return [
            'patient_id' => Patient::factory(),
            'staff_id' => Staff::factory(),
            'service_id' => $serviceId,
            'event_id' => $this->faker->optional(0.15)->passthrough(Event::factory()),
            'scheduled_at' => $scheduled,
            'check_in_time' => $checkIn,
            'check_out_time' => $checkIn ? $this->faker->optional(0.9)->dateTimeBetween($checkIn, (clone $checkIn)->modify('+2 hours')) : null,
            'visit_notes' => $this->faker->optional()->paragraph,
            'service_description' => $this->faker->randomElement([
                'Physiotherapy Session',
                'Home Nursing Visit',
                'Wound Care Follow-up',
                'Medication Administration',
            ]),
            'cost' => $this->faker->randomFloat(2, 200, 2000),
            'status' => $this->faker->randomElement(['Pending', 'Completed', 'Cancelled']),
        ];
    }
}
