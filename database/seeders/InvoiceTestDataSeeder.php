<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Models\VisitService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InvoiceTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a User and associated Staff member
        $user = User::updateOrCreate(
            ['email' => 'invoice.staff@example.com'],
            [
                'name' => 'Invoice Staff',
                'password' => Hash::make('password'),
            ]
        );

        $staff = Staff::updateOrCreate(
            ['email' => $user->email],
            [
                'user_id' => $user->id,
                'first_name' => 'Invoice',
                'last_name' => 'Staff',
                'phone' => '0911223344',
                'position' => 'Nurse',
                'department' => 'Medical',
                'hire_date' => now(),
            ]
        );

        // 2. Create a Patient
        $patient = Patient::updateOrCreate(
            ['email' => 'invoice.patient@example.com'],
            [
                'full_name' => 'Invoice Patient',
                'phone_number' => '0988776655',
                'registered_by_staff_id' => $staff->id,
            ]
        );

        // 3. Create a Service
        $service = Service::factory()->create([
            'name' => 'Consultation',
            'description' => 'General medical consultation',
            'category' => 'Medical',
            'price' => 500.00,
            'duration' => 30,
            'is_active' => true,
        ]);

        // 4. Create a VisitService
        $visitService = VisitService::factory()->create([
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id, // Link to the created service
            'scheduled_at' => now()->subDays(5),
            'check_in_time' => now()->subDays(5)->addHours(8),
            'check_out_time' => now()->subDays(5)->addHours(9),
            'visit_notes' => 'Patient presented with mild fever.',
            'service_description' => $service->name, // Use service name for description
            'status' => 'Completed',
            'cost' => $service->price,
            'is_paid_to_staff' => false,
        ]);

        // 5. Create an Invoice
        $invoice = Invoice::factory()->create([
            'patient_id' => $patient->id,
            'invoice_date' => now()->subDays(3),
            'due_date' => now()->addDays(7),
            'status' => 'Issued',
            'subtotal' => $visitService->cost,
            'grand_total' => $visitService->cost,
            'amount' => $visitService->cost,
        ]);

        // 6. Create an InvoiceItem linking the Invoice to the VisitService
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'visit_service_id' => $visitService->id,
            'description' => $visitService->service_description,
            'cost' => $visitService->cost,
            'quantity' => 1,
        ]);

        $this->command->info('Invoice test data seeded successfully.');
    }
}
