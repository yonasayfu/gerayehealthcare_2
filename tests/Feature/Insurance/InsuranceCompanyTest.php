<?php

use App\Enums\RoleEnum;
use App\Models\InsuranceCompany;
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

test('can view insurance companies', function () {
    InsuranceCompany::factory()->count(3)->create();

    get(route('admin.insurance-companies.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/Companies/Index')
            ->has('insuranceCompanies.data', 3)
        );
});

test('can create an insurance company', function () {
    $attributes = InsuranceCompany::factory()->raw();

    post(route('admin.insurance-companies.store'), $attributes)
        ->assertRedirect(route('admin.insurance-companies.index'));

    $this->assertDatabaseHas('insurance_companies', $attributes);
});

test('can view a single insurance company', function () {
    $insuranceCompany = InsuranceCompany::factory()->create();

    get(route('admin.insurance-companies.show', $insuranceCompany))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/Companies/Show')
            ->has('insuranceCompany', fn (Assert $page) => $page
                ->where('id', $insuranceCompany->id)
                ->etc()
            )
        );
});

test('can update an insurance company', function () {
    $insuranceCompany = InsuranceCompany::factory()->create();
    $updatedAttributes = [
        'name' => 'Updated Name',
        'contact_email' => 'updated@example.com',
    ];

    put(route('admin.insurance-companies.update', $insuranceCompany), $updatedAttributes)
        ->assertRedirect(route('admin.insurance-companies.index'));

    $this->assertDatabaseHas('insurance_companies', $updatedAttributes);
});

test('can delete an insurance company', function () {
    $insuranceCompany = InsuranceCompany::factory()->create();

    delete(route('admin.insurance-companies.destroy', $insuranceCompany))
        ->assertRedirect(route('admin.insurance-companies.index'));

    $this->assertDatabaseMissing('insurance_companies', ['id' => $insuranceCompany->id]);
});
