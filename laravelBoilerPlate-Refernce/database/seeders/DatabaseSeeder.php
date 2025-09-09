<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users for mobile app testing
        $testUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@test.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@test.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'CEO User',
                'email' => 'ceo@test.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'COO User',
                'email' => 'coo@test.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@test.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($testUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Create additional random users for testing
        User::factory(15)->create();

        // RBAC: roles/permissions + role assignments for known users
        $this->call([
            RbacSeeder::class,
            QuoteSeeder::class,
        ]);
    }
}
