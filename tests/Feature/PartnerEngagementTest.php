<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Partner;
use App\Models\PartnerEngagement;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PartnerEngagementTest extends TestCase
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
    public function a_user_can_view_partner_engagements()
    {
        PartnerEngagement::factory()->count(3)->create();
        $response = $this->get(route('admin.partner-engagements.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/PartnerEngagements/Index'));
    }

    /** @test */
    public function a_partner_engagement_can_be_created()
    {
        $partner = Partner::factory()->create();
        $staff = Staff::factory()->create();

        $engagementData = PartnerEngagement::factory()->make([
            'partner_id' => $partner->id,
            'staff_id' => $staff->id,
        ])->toArray();

        $response = $this->post(route('admin.partner-engagements.store'), $engagementData);
        $response->assertRedirect(route('admin.partner-engagements.index'));
        $this->assertDatabaseHas('partner_engagements', ['summary' => $engagementData['summary']]);
    }

    /** @test */
    public function a_user_can_view_a_single_partner_engagement()
    {
        $partnerEngagement = PartnerEngagement::factory()->create();
        $response = $this->get(route('admin.partner-engagements.show', $partnerEngagement->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/PartnerEngagements/Show'));
    }

    /** @test */
    public function a_partner_engagement_can_be_updated()
    {
        $partnerEngagement = PartnerEngagement::factory()->create();
        $updatedData = ['engagement_type' => 'Email', 'summary' => 'Updated summary.'];
        $response = $this->put(route('admin.partner-engagements.update', $partnerEngagement->id), $updatedData);
        $response->assertRedirect(route('admin.partner-engagements.index'));
        $this->assertDatabaseHas('partner_engagements', ['id' => $partnerEngagement->id, 'engagement_type' => 'Email']);
    }

    /** @test */
    public function a_partner_engagement_can_be_deleted()
    {
        $partnerEngagement = PartnerEngagement::factory()->create();
        $response = $this->delete(route('admin.partner-engagements.destroy', $partnerEngagement->id));
        $response->assertRedirect(route('admin.partner-engagements.index'));
        $this->assertDatabaseMissing('partner_engagements', ['id' => $partnerEngagement->id]);
    }
}
