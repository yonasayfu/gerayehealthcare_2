<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\EventBroadcast;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Inertia;
use Tests\TestCase;

class EventBroadcastControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('RolesAndPermissionsSeeder');
    }

    public function test_admin_can_view_broadcasts_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        EventBroadcast::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.event-broadcasts.index'));

        $response->assertOk();
        $response->assertInertia(fn (Inertia $page) => $page
            ->component('Admin/EventBroadcasts/Index')
            ->has('broadcasts.data')
            ->has('broadcasts.links')
        );
    }

    public function test_non_admin_cannot_view_broadcasts_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.event-broadcasts.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_create_broadcast(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        $event = Event::factory()->create();
        $staff = Staff::factory()->create();

        $payload = [
            'event_id' => $event->id,
            'channel' => 'sms',
            'message' => 'Test broadcast message',
            'sent_by_staff_id' => $staff->id,
        ];

        $response = $this->actingAs($admin)->post(route('admin.event-broadcasts.store'), $payload);

        $response->assertRedirect(route('admin.event-broadcasts.index'));
        $this->assertDatabaseHas('event_broadcasts', [
            'event_id' => $event->id,
            'channel' => 'sms',
            'message' => 'Test broadcast message',
            'sent_by_staff_id' => $staff->id,
        ]);
    }

    public function test_non_admin_cannot_create_broadcast(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $staff = Staff::factory()->create();

        $payload = [
            'event_id' => $event->id,
            'channel' => 'sms',
            'message' => 'Test broadcast message',
            'sent_by_staff_id' => $staff->id,
        ];

        $response = $this->actingAs($user)->post(route('admin.event-broadcasts.store'), $payload);
        $response->assertForbidden();
        $this->assertDatabaseMissing('event_broadcasts', ['message' => 'Test broadcast message']);
    }

    public function test_print_current_route_accessible_for_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        EventBroadcast::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.event-broadcasts.printCurrent', [
            'search' => '', 'direction' => 'asc', 'per_page' => 5,
        ]));

        $response->assertOk();
        $this->assertTrue(str_contains(strtolower($response->headers->get('content-type') ?? ''), 'pdf'));
    }
}
