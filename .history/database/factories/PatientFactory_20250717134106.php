<?php
namespace Database\Factories;

use App\Models\Staff; // Import Staff model
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        // Ensure a Staff member exists to be assigned as a registrant
        $staffId = Staff::inRandomOrder()->first()->id ?? Staff::factory()->create()->id;

        return [
            'full_name' => $this->faker->name,
            'patient_code' => 'P' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'fayda_id' => $this->faker->unique()->numerify('##############'),
            
            'date_of_birth' => $this->faker->date,
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'address' => $this->faker->address,
            'phone_number' => $this->faker->unique()->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'emergency_contact' => $this->faker->name . ' - ' . $this->faker->phoneNumber,
            'source' => $this->faker->randomElement(['TikTok', 'Website', 'Referral', 'Walk-in']),
            
            'geolocation' => $this->faker->latitude . ',' . $this->faker->longitude,
            'registered_by_staff_id' => $staffId, // Assign a random staff member
            'registered_by_caregiver_id' => null, // Set to null to avoid foreign key violation
        ];
    }
}
