<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\ReferralDocument;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReferralDocumentTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_create_referral_document()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');

        $referral = Referral::factory()->create();
        $staff = Staff::factory()->create();

        $data = [
            'referral_id' => $referral->id,
            'uploaded_by_staff_id' => $staff->id,
            'document_name' => 'Test Document',
            'document_file' => UploadedFile::fake()->create('document.pdf'),
            'document_type' => 'Clinical Summary',
            'status' => 'Uploaded',
        ];

        $response = $this->post(route('admin.referral-documents.store'), $data);

        $response->assertRedirect(route('admin.referral-documents.index'));

        $this->assertNotNull(ReferralDocument::where('document_name', 'Test Document')->first());
    }

    public function test_admin_can_update_referral_document()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');

        $referralDocument = ReferralDocument::factory()->create();

        $data = [
            'document_name' => 'Updated Document Name',
        ];

        $response = $this->put(route('admin.referral-documents.update', $referralDocument), $data);

        $response->assertRedirect(route('admin.referral-documents.index'));

        $updatedReferralDocument = ReferralDocument::find($referralDocument->id);
        $this->assertEquals('Updated Document Name', $updatedReferralDocument->document_name);
    }

    public function test_admin_can_delete_referral_document()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');

        $referralDocument = ReferralDocument::factory()->create();

        $response = $this->delete(route('admin.referral-documents.destroy', $referralDocument));

        $response->assertRedirect(route('admin.referral-documents.index'));

        $this->assertNull(ReferralDocument::find($referralDocument->id));
    }
}
