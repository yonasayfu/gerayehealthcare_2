<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CommunicationModuleSeeder extends Seeder
{
    /**
     * Seed messaging, collaboration groups, and notification preferences.
     */
    public function run(): void
    {
        $users = User::orderBy('id')->take(8)->get();
        if ($users->count() < 4) {
            $users = User::factory()->count(4)->create();
        }

        Message::factory()->count(20)->create();

        $groupTopics = [
            'Clinical Daily Stand-up',
            'Home Care Dispatch',
            'Marketing Sync',
            'Insurance Follow-ups',
            'Partner Outreach',
        ];

        foreach ($groupTopics as $index => $topic) {
            $creator = $users[$index % $users->count()];
            $group = Group::updateOrCreate(
                ['name' => $topic],
                [
                    'description' => 'Collaboration channel for '.$topic,
                    'created_by' => $creator->id,
                ]
            );

            $memberIds = Arr::shuffle($users->pluck('id')->all());
            foreach (array_slice($memberIds, 0, 4) as $memberIndex => $memberId) {
                GroupMember::updateOrCreate(
                    ['group_id' => $group->id, 'user_id' => $memberId],
                    ['role' => $memberIndex === 0 ? 'owner' : 'member']
                );
            }

            foreach (array_slice($users->all(), 0, 4) as $messageIndex => $user) {
                GroupMessage::create([
                    'group_id' => $group->id,
                    'sender_id' => $user->id,
                    'message' => Str::of($topic)->headline().' update '.($messageIndex + 1),
                    'is_pinned' => $messageIndex === 0,
                ]);
            }
        }

        foreach ($users as $user) {
            NotificationSetting::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'enable_push' => true,
                    'enable_email' => $user->email !== 'guest@gerayehealthcare.com',
                    'enable_sms' => false,
                    'channels' => [
                        'visits' => true,
                        'marketing' => $user->email === 'admin@gerayehealthcare.com',
                        'finance' => in_array($user->email, ['ceo@gerayehealthcare.com', 'coo@gerayehealthcare.com'], true),
                    ],
                ]
            );
        }
    }
}
