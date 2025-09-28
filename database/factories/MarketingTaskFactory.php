<?php

namespace Database\Factories;

use App\Models\CampaignContent;
use App\Models\MarketingCampaign;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingTask>
 */
class MarketingTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => MarketingCampaign::factory(),
            'assigned_to_staff_id' => Staff::factory(),
            'related_content_id' => CampaignContent::factory(),
            'doctor_id' => Staff::factory(),
            'task_type' => $this->faker->randomElement(['Email Campaign', 'Social Media Post', 'Ad Creation', 'Content Writing', 'SEO Optimization']),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'scheduled_at' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'completed_at' => $this->faker->boolean(30) ? $this->faker->dateTimeBetween('-1 week', '+1 day') : null,
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Completed', 'Cancelled']),
            'notes' => $this->faker->boolean(50) ? $this->faker->sentence : null,
        ];
    }
}
