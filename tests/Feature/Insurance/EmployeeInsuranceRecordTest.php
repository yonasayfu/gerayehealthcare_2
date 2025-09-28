<?php

use App\Enums\RoleEnum;
use App\Models\EmployeeInsuranceRecord;
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

test('can view employee insurance records', function () {
    EmployeeInsuranceRecord::factory()->count(3)->create();

    get(route('admin.employee-insurance-records.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/EmployeeInsuranceRecords/Index')
            ->has('employeeInsuranceRecords.data', 3)
        );
});

test('can create an employee insurance record', function () {
    $attributes = EmployeeInsuranceRecord::factory()->raw();

    post(route('admin.employee-insurance-records.store'), $attributes)
        ->assertRedirect(route('admin.employee-insurance-records.index'));

    $this->assertDatabaseHas('employee_insurance_records', $attributes);
});

test('can view a single employee insurance record', function () {
    $employeeInsuranceRecord = EmployeeInsuranceRecord::factory()->create();

    get(route('admin.employee-insurance-records.show', $employeeInsuranceRecord))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Insurance/EmployeeInsuranceRecords/Show')
            ->has('employeeInsuranceRecord', fn (Assert $page) => $page
                ->where('id', $employeeInsuranceRecord->id)
                ->etc()
            )
        );
});

test('can update an employee insurance record', function () {
    $employeeInsuranceRecord = EmployeeInsuranceRecord::factory()->create();
    $updatedAttributes = [
        'kebele_id' => 'Updated Kebele ID',
    ];

    put(route('admin.employee-insurance-records.update', $employeeInsuranceRecord), $updatedAttributes)
        ->assertRedirect(route('admin.employee-insurance-records.index'));

    $this->assertDatabaseHas('employee_insurance_records', $updatedAttributes);
});

test('can delete an employee insurance record', function () {
    $employeeInsuranceRecord = EmployeeInsuranceRecord::factory()->create();

    delete(route('admin.employee-insurance-records.destroy', $employeeInsuranceRecord))
        ->assertRedirect(route('admin.employee-insurance-records.index'));

    $this->assertDatabaseMissing('employee_insurance_records', ['id' => $employeeInsuranceRecord->id]);
});
