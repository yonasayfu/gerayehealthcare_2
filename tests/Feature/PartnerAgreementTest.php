<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Partner;
use App\Models\PartnerAgreement;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PartnerAgreementTest extends TestCase
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

        // Ensure there's at least one partner and staff for foreign keys
        Partner::factory()->create();
        Staff::factory()->create();
    }

    /** @test */
    public function a_user_can_view_partner_agreements()
    {
        PartnerAgreement::factory()->count(3)->create();
        $response = $this->get(route('admin.partner-agreements.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/PartnerAgreements/Index'));
    }

    /** @test */
    public function a_partner_agreement_can_be_created()
    {
        $partner = Partner::factory()->create();
        $staff = Staff::factory()->create();
        $agreementData = PartnerAgreement::factory()->make([
            'partner_id' => $partner->id,
            'signed_by_staff_id' => $staff->id,
        ])->toArray();

        $response = $this->post(route('admin.partner-agreements.store'), $agreementData);
        $response->assertRedirect(route('admin.partner-agreements.index'));
        $this->assertDatabaseHas('partner_agreements', ['agreement_title' => $agreementData['agreement_title']]);
    }

    /** @test */
    public function a_user_can_view_a_single_partner_agreement()
    {
        $partnerAgreement = PartnerAgreement::factory()->create();
        $response = $this->get(route('admin.partner-agreements.show', $partnerAgreement->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/PartnerAgreements/Show'));
    }

    /** @test */
    public function a_partner_agreement_can_be_updated()
    {
        $partnerAgreement = PartnerAgreement::factory()->create();
        $updatedData = ['agreement_title' => 'Updated Agreement Title', 'status' => 'Active'];
        $response = $this->put(route('admin.partner-agreements.update', $partnerAgreement->id), $updatedData);
        $response->assertRedirect(route('admin.partner-agreements.index'));
        $this->assertDatabaseHas('partner_agreements', ['id' => $partnerAgreement->id, 'agreement_title' => 'Updated Agreement Title', 'status' => 'Active']);
    }

    /** @test */
    public function a_partner_agreement_can_be_deleted()
    {
        $partnerAgreement = PartnerAgreement::factory()->create();
        $response = $this->delete(route('admin.partner-agreements.destroy', $partnerAgreement->id));
        $response->assertRedirect(route('admin.partner-agreements.index'));
        $this->assertDatabaseMissing('partner_agreements', ['id' => $partnerAgreement->id]);
    }
}
