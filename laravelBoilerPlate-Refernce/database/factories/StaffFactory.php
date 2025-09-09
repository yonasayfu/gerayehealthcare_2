<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $email = strtolower($firstName . '.' . $lastName . '@company.com');
        
        return [
            'employee_id' => $this->generateEmployeeId(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone_number' => $this->faker->phoneNumber(),
            'position' => $this->faker->randomElement([
                'Software Engineer',
                'Senior Developer',
                'Project Manager',
                'Business Analyst',
                'QA Engineer',
                'DevOps Engineer',
                'UI/UX Designer',
                'Product Manager',
                'Data Analyst',
                'Marketing Specialist',
                'Sales Representative',
                'HR Specialist',
                'Accountant',
                'Customer Support',
                'Operations Manager'
            ]),
            'department' => $this->faker->randomElement([
                'Engineering',
                'Product',
                'Marketing',
                'Sales',
                'Human Resources',
                'Finance',
                'Operations',
                'Customer Support',
                'Quality Assurance',
                'Design'
            ]),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'salary' => $this->faker->numberBetween(40000, 150000),
            'address' => $this->faker->address(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'employment_type' => $this->faker->randomElement(['full-time', 'part-time', 'contract', 'intern']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'on-leave']),
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
            'notes' => $this->faker->optional(0.3)->sentence(), // 30% chance of having notes
        ];
    }

    /**
     * Indicate that the staff member is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the staff member is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the staff member is on leave.
     */
    public function onLeave(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'on-leave',
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the staff member is terminated.
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terminated',
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the staff member is full-time.
     */
    public function fullTime(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_type' => 'full-time',
        ]);
    }

    /**
     * Indicate that the staff member is part-time.
     */
    public function partTime(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_type' => 'part-time',
        ]);
    }

    /**
     * Indicate that the staff member is a contractor.
     */
    public function contract(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_type' => 'contract',
        ]);
    }

    /**
     * Indicate that the staff member is an intern.
     */
    public function intern(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_type' => 'intern',
            'salary' => $this->faker->numberBetween(20000, 40000),
        ]);
    }

    /**
     * Indicate that the staff member is in a specific department.
     */
    public function inDepartment(string $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department' => $department,
        ]);
    }

    /**
     * Indicate that the staff member has a specific position.
     */
    public function withPosition(string $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }

    /**
     * Indicate that the staff member has an associated user account.
     */
    public function withUser(): static
    {
        return $this->afterCreating(function (Staff $staff) {
            $user = User::factory()->create([
                'name' => $staff->full_name,
                'email' => $staff->email,
                'phone_number' => $staff->phone_number,
                'is_active' => $staff->is_active,
            ]);
            
            $user->assignRole('staff');
            
            $staff->update(['user_id' => $user->id]);
        });
    }

    /**
     * Generate a unique employee ID.
     */
    private function generateEmployeeId(): string
    {
        $prefix = 'EMP';
        $year = date('Y');
        
        do {
            $number = $this->faker->numberBetween(1, 9999);
            $employeeId = $prefix . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
        } while (Staff::where('employee_id', $employeeId)->exists());
        
        return $employeeId;
    }
}
