<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\StaffPayout;
use App\Models\User;
use App\Models\VisitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('displays staff earnings and payout history', function () {
    // Create a user with staff role
    $user = User::factory()->create();
    $user->assignRole(RoleEnum::STAFF->value);

    // Create a staff profile for the user
    $staff = Staff::factory()->create(['user_id' => $user->id]);

    // Create payout history for the staff
    StaffPayout::factory()->count(3)->create([
        'staff_id' => $staff->id,
        'status' => 'Paid',
    ]);

    // Create pending visits for the staff
    VisitService::factory()->count(2)->create([
        'staff_id' => $staff->id,
        'status' => 'Completed',
        'is_paid_to_staff' => false,
        'cost' => 100.00,
    ]);

    // Create some paid visits (should not appear in pending)
    VisitService::factory()->count(1)->create([
        'staff_id' => $staff->id,
        'status' => 'Completed',
        'is_paid_to_staff' => true,
        'cost' => 50.00,
    ]);

    // Create some incomplete visits (should not appear in pending)
    VisitService::factory()->count(1)->create([
        'staff_id' => $staff->id,
        'status' => 'Scheduled',
        'is_paid_to_staff' => false,
        'cost' => 200.00,
    ]);

    $this->actingAs($user)
        ->get(route('staff.my-earnings.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Staff/MyEarnings/Index')
            ->has('payoutHistory.data', 3)
            ->has('pendingVisits.data', 2)
            ->where('pendingTotal', 200.00) // 2 visits * 100.00 cost
        );
});

it('redirects if user does not have a staff profile', function () {
    $user = User::factory()->create(); // User without staff profile

    $this->actingAs($user)
        ->get(route('staff.my-earnings.index'))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('error', 'You do not have a staff profile.');
});
