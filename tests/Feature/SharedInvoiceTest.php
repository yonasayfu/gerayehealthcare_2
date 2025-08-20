<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Partner;
use App\Models\SharedInvoice;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SharedInvoiceTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_create_shared_invoice()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $invoice = Invoice::factory()->create();
        $partner = Partner::factory()->create();
        $staff = Staff::factory()->create();

        $data = [
            'invoice_id' => $invoice->id,
            'partner_id' => $partner->id,
            'shared_by_staff_id' => $staff->id,
            'share_date' => '2025-01-01',
            'status' => 'Shared',
            'notes' => 'Test notes',
        ];

        $response = $this->post(route('admin.shared-invoices.store'), $data);

        $response->assertRedirect(route('admin.shared-invoices.index'));

        $this->assertNotNull(SharedInvoice::where('notes', 'Test notes')->first());
    }

    public function test_admin_can_update_shared_invoice()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $sharedInvoice = SharedInvoice::factory()->create();

        $data = [
            'notes' => 'Updated notes',
        ];

        $response = $this->put(route('admin.shared-invoices.update', $sharedInvoice), $data);

        $response->assertRedirect(route('admin.shared-invoices.index'));

        $updatedSharedInvoice = SharedInvoice::find($sharedInvoice->id);
        $this->assertEquals('Updated notes', $updatedSharedInvoice->notes);
    }

    public function test_admin_can_delete_shared_invoice()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $sharedInvoice = SharedInvoice::factory()->create();

        $response = $this->delete(route('admin.shared-invoices.destroy', $sharedInvoice));

        $response->assertRedirect(route('admin.shared-invoices.index'));

        $this->assertNull(SharedInvoice::find($sharedInvoice->id));
    }
}
