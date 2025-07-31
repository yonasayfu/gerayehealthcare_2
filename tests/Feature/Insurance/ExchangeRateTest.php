<?php

use App\Enums\RoleEnum;
use App\Models\ExchangeRate;
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

test('can view exchange rates', function () {
    ExchangeRate::factory()->count(3)->create();

    get(route('admin.exchange-rates.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/ExchangeRates/Index')
            ->has('exchangeRates.data', 3)
        );
});

test('can create an exchange rate', function () {
    $attributes = ExchangeRate::factory()->raw();

    post(route('admin.exchange-rates.store'), $attributes)
        ->assertRedirect(route('admin.exchange-rates.index'));

    $this->assertDatabaseHas('exchange_rates', $attributes);
});

test('can view a single exchange rate', function () {
    $exchangeRate = ExchangeRate::factory()->create();

    get(route('admin.exchange-rates.show', $exchangeRate))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/ExchangeRates/Show')
            ->has('exchangeRate', fn (Assert $page) => $page
                ->where('id', $exchangeRate->id)
                ->etc()
            )
        );
});

test('can update an exchange rate', function () {
    $exchangeRate = ExchangeRate::factory()->create();
    $updatedAttributes = [
        'rate_to_etb' => 123.4567,
    ];

    put(route('admin.exchange-rates.update', $exchangeRate), $updatedAttributes)
        ->assertRedirect(route('admin.exchange-rates.index'));

    $this->assertDatabaseHas('exchange_rates', $updatedAttributes);
});

test('can delete an exchange rate', function () {
    $exchangeRate = ExchangeRate::factory()->create();

    delete(route('admin.exchange-rates.destroy', $exchangeRate))
        ->assertRedirect(route('admin.exchange-rates.index'));

    $this->assertDatabaseMissing('exchange_rates', ['id' => $exchangeRate->id]);
});
