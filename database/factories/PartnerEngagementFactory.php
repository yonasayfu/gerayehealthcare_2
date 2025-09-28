<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerEngagement>
 */
class PartnerEngagementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::inRandomOrder()->first()->id ?? null,
            'staff_id' => Staff::inRandomOrder()->first()->id ?? null,
            'engagement_type' => $this->faker->randomElement(['Meeting', 'Call', 'Email', 'Event']),
            'summary' => $this->faker->sentence,
            'engagement_date' => $this->faker->dateTimeThisYear(),
            'follow_up_date' => $this->faker->optional()->date(),
        ];
    }
}
