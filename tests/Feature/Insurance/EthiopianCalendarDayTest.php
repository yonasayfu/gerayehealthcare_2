<?php

use App\Enums\RoleEnum;
use App\Models\EthiopianCalendarDay;
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

test('can view ethiopian calendar days', function () {
    EthiopianCalendarDay::factory()->count(3)->create();

    get(route('admin.ethiopian-calendar-days.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/EthiopianCalendarDays/Index')
            ->has('ethiopianCalendarDays.data', 3)
        );
});

test('can create an ethiopian calendar day', function () {
    $attributes = EthiopianCalendarDay::factory()->raw();

    post(route('admin.ethiopian-calendar-days.store'), $attributes)
        ->assertRedirect(route('admin.ethiopian-calendar-days.index'));

    $this->assertDatabaseHas('ethiopian_calendar_days', $attributes);
});

test('can view a single ethiopian calendar day', function () {
    $ethiopianCalendarDay = EthiopianCalendarDay::factory()->create();

    get(route('admin.ethiopian-calendar-days.show', $ethiopianCalendarDay))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/EthiopianCalendarDays/Show')
            ->has('ethiopianCalendarDay', fn (Assert $page) => $page
                ->where('id', $ethiopianCalendarDay->id)
                ->etc()
            )
        );
});

test('can update an ethiopian calendar day', function () {
    $ethiopianCalendarDay = EthiopianCalendarDay::factory()->create();
    $updatedAttributes = [
        'description' => 'Updated Description',
    ];

    put(route('admin.ethiopian-calendar-days.update', $ethiopianCalendarDay), $updatedAttributes)
        ->assertRedirect(route('admin.ethiopian-calendar-days.index'));

    $this->assertDatabaseHas('ethiopian_calendar_days', $updatedAttributes);
});

test('can delete an ethiopian calendar day', function () {
    $ethiopianCalendarDay = EthiopianCalendarDay::factory()->create();

    delete(route('admin.ethiopian-calendar-days.destroy', $ethiopianCalendarDay))
        ->assertRedirect(route('admin.ethiopian-calendar-days.index'));

    $this->assertDatabaseMissing('ethiopian_calendar_days', ['id' => $ethiopianCalendarDay->id]);
});
