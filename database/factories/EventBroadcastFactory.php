<?php

namespace Database\Factories;

use App\Models\EventBroadcast;
use App\Models\Event;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventBroadcast>
 */
class EventBroadcastFactory extends Factory
{
    protected $model = EventBroadcast::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'channel' => $this->faker->randomElement(['email', 'sms', 'push']),
            'message' => $this->faker->sentence(12),
            'sent_by_staff_id' => Staff::factory(),
        ];
    }
}
