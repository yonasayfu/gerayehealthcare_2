<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a known user exists
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $samples = [
            ['text' => 'Quality is not an act, it is a habit.', 'author' => 'Aristotle'],
            ['text' => 'Simplicity is the soul of efficiency.', 'author' => 'Austin Freeman'],
            ['text' => 'First, solve the problem. Then, write the code.', 'author' => 'John Johnson'],
            ['text' => 'Make it work, make it right, make it fast.', 'author' => 'Kent Beck'],
            ['text' => 'Small gains, consistently.', 'author' => 'Geraye Principles'],
        ];

        foreach ($samples as $i => $q) {
            Quote::firstOrCreate(
                ['user_id' => $user->id, 'text' => $q['text']],
                ['author' => $q['author'], 'language' => 'en', 'priority' => $i]
            );
        }

        // Optionally create a few random quotes for other users
        Quote::factory()->count(5)->create(['user_id' => $user->id]);
    }
}
