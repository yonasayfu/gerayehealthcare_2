<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Partner;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PartnerTest extends TestCase
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

        // Ensure there's at least one staff for account_manager_id
        Staff::factory()->create();
    }

    /** @test */
    public function a_user_can_view_partners()
    {
        Partner::factory()->count(3)->create();
        $response = $this->get(route('admin.partners.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/Partners/Index'));
    }

    /** @test */
    public function a_partner_can_be_created()
    {
        $partnerData = Partner::factory()->make()->toArray();
        $response = $this->post(route('admin.partners.store'), $partnerData);
        $response->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseHas('partners', ['name' => $partnerData['name']]);
    }

    /** @test */
    public function a_user_can_view_a_single_partner()
    {
        $partner = Partner::factory()->create();
        $response = $this->get(route('admin.partners.show', $partner->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/Partners/Show'));
    }

    /** @test */
    public function a_partner_can_be_updated()
    {
        $partner = Partner::factory()->create();
        $updatedData = ['name' => 'Updated Partner Name', 'type' => 'NGO'];
        $response = $this->put(route('admin.partners.update', $partner->id), $updatedData);
        $response->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseHas('partners', ['id' => $partner->id, 'name' => 'Updated Partner Name', 'type' => 'NGO']);
    }

    /** @test */
    public function a_partner_can_be_deleted()
    {
        $partner = Partner::factory()->create();
        $response = $this->delete(route('admin.partners.destroy', $partner->id));
        $response->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseMissing('partners', ['id' => $partner->id]);
    }
}
