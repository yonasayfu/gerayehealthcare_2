<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\VisitService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['Pending', 'Paid']),
            'paid_at' => $this->faker->dateTime,
            'invoice_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'subtotal' => $this->faker->randomFloat(2, 100, 1000),
            'tax_amount' => $this->faker->randomFloat(2, 0, 100),
            'grand_total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
