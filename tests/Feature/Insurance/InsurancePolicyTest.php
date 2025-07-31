<?php

use App\Enums\RoleEnum;
use App\Models\InsurancePolicy;
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

test('can view insurance policies', function () {
    InsurancePolicy::factory()->count(3)->create();

    get(route('admin.insurance-policies.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/Policies/Index')
            ->has('insurancePolicies.data', 3)
        );
});

test('can create an insurance policy', function () {
    $attributes = InsurancePolicy::factory()->raw();

    post(route('admin.insurance-policies.store'), $attributes)
        ->assertRedirect(route('admin.insurance-policies.index'));

    $this->assertDatabaseHas('insurance_policies', $attributes);
});

test('can view a single insurance policy', function () {
    $insurancePolicy = InsurancePolicy::factory()->create();

    get(route('admin.insurance-policies.show', $insurancePolicy))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/Policies/Show')
            ->has('insurancePolicy', fn (Assert $page) => $page
                ->where('id', $insurancePolicy->id)
                ->etc()
            )
        );
});

test('can update an insurance policy', function () {
    $insurancePolicy = InsurancePolicy::factory()->create();
    $updatedAttributes = [
        'service_type' => 'Updated Service Type',
    ];

    put(route('admin.insurance-policies.update', $insurancePolicy), $updatedAttributes)
        ->assertRedirect(route('admin.insurance-policies.index'));

    $this->assertDatabaseHas('insurance_policies', $updatedAttributes);
});

test('can delete an insurance policy', function () {
    $insurancePolicy = InsurancePolicy::factory()->create();

    delete(route('admin.insurance-policies.destroy', $insurancePolicy))
        ->assertRedirect(route('admin.insurance-policies.index'));

    $this->assertDatabaseMissing('insurance_policies', ['id' => $insurancePolicy->id]);
});
