<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SharedInvoice>
 */
class SharedInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => \App\Models\Invoice::factory(),
            'partner_id' => \App\Models\Partner::factory(),
            'shared_by_staff_id' => \App\Models\Staff::factory(),
            'share_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['Shared', 'Acknowledged', 'Action-Required']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
