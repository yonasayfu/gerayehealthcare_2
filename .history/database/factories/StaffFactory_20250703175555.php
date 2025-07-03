<?php
use Illuminate\Support\Str;

public function definition(): array
{
    return [
        'first_name' => $this->faker->firstName,
        'last_name' => $this->faker->lastName,
        'email' => $this->faker->unique()->safeEmail,
        'phone' => $this->->faker->phoneNumber,
        'position' => $this->faker->jobTitle,
        'department' => $this->faker->randomElement(['HR', 'Finance', 'Medical', 'Operations']),
        'status' => $this->faker->randomElement(['Active', 'Inactive']),
        'hire_date' => $this->faker->date(),
        'photo' => 'images/staff/placeholder.jpg', // reused placeholder
        'user_id' => null,
    ];
}
