<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Inertia;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('RolesAndPermissionsSeeder');
    }

    public function test_admin_can_view_events_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        Event::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get(route('admin.events.index'));

        $response->assertOk();
        $response->assertInertia(fn (Inertia $page) => $page
            ->component('Admin/Events/Index')
            ->has('events.data', 5)
            ->has('events.links')
            ->has('events.meta')
        );
    }

    public function test_non_admin_cannot_view_events_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.events.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_event(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        $eventData = Event::factory()->make()->toArray();

        $response = $this->actingAs($admin)->post(route('admin.events.store'), $eventData);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', ['title' => $eventData['title']]);
    }

    public function test_non_admin_cannot_create_event(): void
    {
        $user = User::factory()->create();
        $eventData = Event::factory()->make()->toArray();

        $response = $this->actingAs($user)->post(route('admin.events.store'), $eventData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('events', ['title' => $eventData['title']]);
    }

    public function test_admin_can_view_single_event(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        $event = Event::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.events.show', $event));

        $response->assertOk();
        $response->assertInertia(fn (Inertia $page) => $page
            ->component('Admin/Events/Show')
            ->has('event', fn (Inertia $page) => $page
                ->where('id', $event->id)
                ->where('title', $event->title)
                ->etc()
            )
        );
    }

    public function test_non_admin_cannot_view_single_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.events.show', $event));

        $response->assertForbidden();
    }

    public function test_admin_can_update_event(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        $event = Event::factory()->create();
        $updatedData = [
            'title' => 'Updated Event Title',
            'description' => 'Updated event description.',
            'event_date' => '2025-10-01',
            'is_free_service' => false,
            'broadcast_status' => 'Archived',
        ];

        $response = $this->actingAs($admin)->put(route('admin.events.update', $event), $updatedData);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated Event Title']);
    }

    public function test_non_admin_cannot_update_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $updatedData = [
            'title' => 'Updated Event Title',
            'description' => 'Updated event description.',
            'event_date' => '2025-10-01',
            'is_free_service' => false,
            'broadcast_status' => 'Archived',
        ];

        $response = $this->actingAs($user)->put(route('admin.events.update', $event), $updatedData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('events', ['title' => 'Updated Event Title']);
    }

    public function test_admin_can_delete_event(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        $event = Event::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.events.destroy', $event));

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_non_admin_cannot_delete_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.events.destroy', $event));

        $response->assertForbidden();
        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }
}
