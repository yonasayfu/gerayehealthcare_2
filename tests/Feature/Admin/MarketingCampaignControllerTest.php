<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use App\Models\User;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Models\Staff;
use App\Enums\RoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;

class MarketingCampaignControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole(RoleEnum::ADMIN->value);

        // Create prerequisite records
        MarketingPlatform::factory()->create();
        Staff::factory()->create();
    }

    /** @test */
    public function admin_can_view_marketing_campaigns_index_page()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.marketing-campaigns.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/MarketingCampaigns/Index'));
    }

    /** @test */
    public function admin_can_view_marketing_campaign_create_page()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.marketing-campaigns.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/MarketingCampaigns/Create'));
    }

    /** @test */
    public function admin_can_create_a_new_marketing_campaign()
    {
        $platform = MarketingPlatform::first();
        $staff = Staff::first();

        $campaignData = [
            'platform_id' => $platform->id,
            'campaign_name' => 'Test Campaign',
            'campaign_type' => 'Awareness',
            'budget_allocated' => 1000,
            'start_date' => now()->toDateString(),
            'status' => 'Draft',
            'assigned_staff_id' => $staff->id,
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.marketing-campaigns.store'), $campaignData);

        $response->assertRedirect();
        $this->assertDatabaseHas('marketing_campaigns', ['campaign_name' => 'Test Campaign']);
    }

    /** @test */
    public function admin_can_view_marketing_campaign_show_page()
    {
        $campaign = MarketingCampaign::factory()->create();

        $response = $this->actingAs($this->adminUser)->get(route('admin.marketing-campaigns.show', $campaign->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/MarketingCampaigns/Show'));
    }

    /** @test */
    public function admin_can_view_marketing_campaign_edit_page()
    {
        $campaign = MarketingCampaign::factory()->create();

        $response = $this->actingAs($this->adminUser)->get(route('admin.marketing-campaigns.edit', $campaign->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/MarketingCampaigns/Edit'));
    }

    /** @test */
    public function admin_can_update_a_marketing_campaign()
    {
        $campaign = MarketingCampaign::factory()->create();

        $updatedData = [
            'campaign_name' => 'Updated Campaign Name',
            'status' => 'Active',
        ];

        $response = $this->actingAs($this->adminUser)->put(route('admin.marketing-campaigns.update', $campaign->id), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('marketing_campaigns', ['id' => $campaign->id, 'campaign_name' => 'Updated Campaign Name']);
    }

    /** @test */
    public function admin_can_delete_a_marketing_campaign()
    {
        $campaign = MarketingCampaign::factory()->create();

        $response = $this->actingAs($this->adminUser)->delete(route('admin.marketing-campaigns.destroy', $campaign->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('marketing_campaigns', ['id' => $campaign->id]);
    }
}
