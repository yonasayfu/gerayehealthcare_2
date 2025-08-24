<?php

namespace Database\Factories;

use App\Models\Patient;
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
        $invoiceDate = $this->faker->dateTimeBetween('-12 months', 'now');
        $dueDate = (clone $invoiceDate)->modify('+'. $this->faker->numberBetween(7, 45) .' days');
        $subtotal = $this->faker->randomFloat(2, 100, 2000);
        $tax = round($subtotal * $this->faker->randomFloat(3, 0.00, 0.15), 2);
        $grandTotal = $subtotal + $tax;
        $isPaid = $this->faker->boolean(65);
        $received = $isPaid
            ? $grandTotal
            : $this->faker->randomFloat(2, 0, max(0.0, $grandTotal - 50));
        $paidAt = $isPaid ? $this->faker->dateTimeBetween($invoiceDate, '+2 months') : null;

        return [
            'patient_id' => Patient::factory(),
            'amount' => $received, // total received so far
            'status' => $isPaid ? 'Paid' : 'Pending',
            'paid_at' => $paidAt,
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'grand_total' => $grandTotal,
        ];
    }
}
