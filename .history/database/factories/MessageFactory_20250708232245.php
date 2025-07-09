<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get all user IDs to create conversations between them
        $userIds = User::pluck('id')->toArray();
        $senderId = Arr::random($userIds);
        $receiverId = Arr::random(array_diff($userIds, [$senderId])); // Ensure sender and receiver are different

        return [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $this->faker->sentence(),
            'read_at' => $this->faker->optional(0.5)->dateTime(),
        ];
    }
}
