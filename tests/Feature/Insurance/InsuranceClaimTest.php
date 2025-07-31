<?php

use App\Enums\RoleEnum;
use App\Models\InsuranceClaim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $adminRole = Role::create(['name' => RoleEnum::ADMIN->value]);
    $this->user->assignRole($adminRole);
    actingAs($this->user);
});

test('can view insurance claims', function () {
    InsuranceClaim::factory()->count(3)->create();

    get(route('admin.insurance-claims.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/Claims/Index')
            ->has('insuranceClaims.data', 3)
        );
});

test('can create an insurance claim', function () {
    $attributes = InsuranceClaim::factory()->raw();

    post(route('admin.insurance-claims.store'), $attributes)
        ->assertRedirect(route('admin.insurance-claims.index'));

    $this->assertDatabaseHas('insurance_claims', $attributes);
});

test('can view a single insurance claim', function () {
    $insuranceClaim = InsuranceClaim::factory()->create();

    get(route('admin.insurance-claims.show', $insuranceClaim))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/Claims/Show')
            ->has('insuranceClaim', fn (Assert $page) => $page
                ->where('id', $insuranceClaim->id)
                ->etc()
            )
        );
});

test('can update an insurance claim', function () {
    $insuranceClaim = InsuranceClaim::factory()->create();
    $updatedAttributes = [
        'claim_status' => 'Updated Status',
    ];

    put(route('admin.insurance-claims.update', $insuranceClaim), $updatedAttributes)
        ->assertRedirect(route('admin.insurance-claims.index'));

    $this->assertDatabaseHas('insurance_claims', $updatedAttributes);
});

test('can delete an insurance claim', function () {
    $insuranceClaim = InsuranceClaim::factory()->create();

    delete(route('admin.insurance-claims.destroy', $insuranceClaim))
        ->assertRedirect(route('admin.insurance-claims.index'));

    $this->assertDatabaseMissing('insurance_claims', ['id' => $insuranceClaim->id]);
});
