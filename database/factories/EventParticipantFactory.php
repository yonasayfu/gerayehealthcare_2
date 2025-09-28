<?php

namespace Database\Factories;

use App\Models\EventParticipant;
use App\Models\Event;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventParticipant>
 */
class EventParticipantFactory extends Factory
{
    protected $model = EventParticipant::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::inRandomOrder()->value('id') ?? Event::factory(),
            'patient_id' => Patient::inRandomOrder()->value('id') ?? Patient::factory(),
            'status' => $this->faker->randomElement(['Registered', 'Attended', 'Cancelled']),
        ];
    }
}
