<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\PartnerAgreement;
use App\Models\PartnerCommission;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PartnerCommissionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutMiddleware;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Ensure there's at least one partner agreement, referral, and invoice for foreign keys
        PartnerAgreement::factory()->create();
        Referral::factory()->create();
        Invoice::factory()->create();
    }

    /** @test */
    public function a_user_can_view_partner_commissions()
    {
        PartnerCommission::factory()->count(3)->create();
        $response = $this->get(route('admin.partner-commissions.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/PartnerCommissions/Index'));
    }

    /** @test */
    public function a_partner_commission_can_be_created()
    {
        $agreement = PartnerAgreement::factory()->create();
        $referral = Referral::factory()->create();
        $invoice = Invoice::factory()->create();

        $commissionData = PartnerCommission::factory()->make([
            'agreement_id' => $agreement->id,
            'referral_id' => $referral->id,
            'invoice_id' => $invoice->id,
        ])->toArray();

        $response = $this->post(route('admin.partner-commissions.store'), $commissionData);
        $response->assertRedirect(route('admin.partner-commissions.index'));
        $this->assertDatabaseHas('partner_commissions', ['commission_amount' => $commissionData['commission_amount']]);
    }

    /** @test */
    public function a_user_can_view_a_single_partner_commission()
    {
        $partnerCommission = PartnerCommission::factory()->create();
        $response = $this->get(route('admin.partner-commissions.show', $partnerCommission->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/PartnerCommissions/Show'));
    }

    /** @test */
    public function a_partner_commission_can_be_updated()
    {
        $partnerCommission = PartnerCommission::factory()->create();
        $updatedData = ['status' => 'Paid', 'payout_date' => $this->faker->date()];
        $response = $this->put(route('admin.partner-commissions.update', $partnerCommission->id), $updatedData);
        $response->assertRedirect(route('admin.partner-commissions.index'));
        $this->assertDatabaseHas('partner_commissions', ['id' => $partnerCommission->id, 'status' => 'Paid']);
    }

    /** @test */
    public function a_partner_commission_can_be_deleted()
    {
        $partnerCommission = PartnerCommission::factory()->create();
        $response = $this->delete(route('admin.partner-commissions.destroy', $partnerCommission->id));
        $response->assertRedirect(route('admin.partner-commissions.index'));
        $this->assertDatabaseMissing('partner_commissions', ['id' => $partnerCommission->id]);
    }
}
