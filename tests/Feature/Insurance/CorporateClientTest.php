<?php

use App\Enums\RoleEnum;
use App\Models\CorporateClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $adminRole = Role::create(['name' => RoleEnum::ADMIN->value]);
    $this->user->assignRole($adminRole);
    actingAs($this->user);
});

test('can view corporate clients', function () {
    CorporateClient::factory()->count(3)->create();

    get(route('admin.corporate-clients.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/CorporateClients/Index')
            ->has('corporateClients.data', 3)
        );
});

test('can create a corporate client', function () {
    $attributes = CorporateClient::factory()->raw();

    post(route('admin.corporate-clients.store'), $attributes)
        ->assertRedirect(route('admin.corporate-clients.index'));

    $this->assertDatabaseHas('corporate_clients', $attributes);
});

test('can view a single corporate client', function () {
    $corporateClient = CorporateClient::factory()->create();

    get(route('admin.corporate-clients.show', $corporateClient))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/CorporateClients/Show')
            ->has('corporateClient', fn (Assert $page) => $page
                ->where('id', $corporateClient->id)
                ->etc()
            )
        );
});

test('can update a corporate client', function () {
    $corporateClient = CorporateClient::factory()->create();
    $updatedAttributes = [
        'organization_name' => 'Updated Name',
        'contact_email' => 'updated@example.com',
    ];

    put(route('admin.corporate-clients.update', $corporateClient), $updatedAttributes)
        ->assertRedirect(route('admin.corporate-clients.index'));

    $this->assertDatabaseHas('corporate_clients', $updatedAttributes);
});

test('can delete a corporate client', function () {
    $corporateClient = CorporateClient::factory()->create();

    delete(route('admin.corporate-clients.destroy', $corporateClient))
        ->assertRedirect(route('admin.corporate-clients.index'));

    $this->assertDatabaseMissing('corporate_clients', ['id' => $corporateClient->id]);
});
