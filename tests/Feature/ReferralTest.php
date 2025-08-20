<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Partner;
use App\Models\PartnerAgreement;
use App\Models\Patient;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->user = User::factory()->create();
        $this->user->assignRole(RoleEnum::ADMIN);
        $this->actingAs($this->user);

        // Ensure there's at least one partner, partner agreement, and patient for foreign keys
        Partner::factory()->create();
        PartnerAgreement::factory()->create();
        Patient::factory()->create();
    }

    /** @test */
    public function a_user_can_view_referrals()
    {
        Referral::factory()->count(3)->create();
        $response = $this->get(route('admin.referrals.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/Referrals/Index'));
    }

    /** @test */
    public function a_referral_can_be_created()
    {
        $partner = Partner::factory()->create();
        $agreement = PartnerAgreement::factory()->create();
        $patient = Patient::factory()->create();

        $referralData = Referral::factory()->make([
            'partner_id' => $partner->id,
            'agreement_id' => $agreement->id,
            'referred_patient_id' => $patient->id,
        ])->toArray();

        $response = $this->post(route('admin.referrals.store'), $referralData);
        $response->assertRedirect(route('admin.referrals.index'));
        $this->assertDatabaseHas('referrals', ['referred_patient_id' => $referralData['referred_patient_id']]);
    }

    /** @test */
    public function a_user_can_view_a_single_referral()
    {
        $referral = Referral::factory()->create();
        $response = $this->get(route('admin.referrals.show', $referral->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/Referrals/Show'));
    }

    /** @test */
    public function a_referral_can_be_updated()
    {
        $referral = Referral::factory()->create();
        $updatedData = ['status' => 'Converted', 'notes' => 'Converted successfully.'];
        $response = $this->put(route('admin.referrals.update', $referral->id), $updatedData);
        $response->assertRedirect(route('admin.referrals.index'));
        $this->assertDatabaseHas('referrals', ['id' => $referral->id, 'status' => 'Converted']);
    }

    /** @test */
    public function a_referral_can_be_deleted()
    {
        $referral = Referral::factory()->create();
        $response = $this->delete(route('admin.referrals.destroy', $referral->id));
        $response->assertRedirect(route('admin.referrals.index'));
        $this->assertDatabaseMissing('referrals', ['id' => $referral->id]);
    }
}
