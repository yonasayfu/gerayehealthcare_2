<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\InsuranceClaim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfDay();

        // Ensure we have some services
        if (Service::count() < 3) {
            Service::query()->create(['name' => 'General Consultation', 'price' => 300, 'duration' => 45, 'category' => 'General', 'is_active' => true]);
            Service::query()->create(['name' => 'Home Nursing Visit', 'price' => 500, 'duration' => 60, 'category' => 'Nursing', 'is_active' => true]);
            Service::query()->create(['name' => 'Physiotherapy Session', 'price' => 400, 'duration' => 45, 'category' => 'Therapy', 'is_active' => true]);
        }

        // Ensure some staff exists (DatabaseSeeder already seeds staff, but keep safe)
        if (Staff::count() < 3) {
            Staff::factory()->count(3)->create();
        }

        // 1) Add additional patients across the current month to make the chart meaningful
        $days = $end->diffInDays($startOfMonth) + 1;
        for ($d = 0; $d < $days; $d++) {
            $day = $startOfMonth->copy()->addDays($d);
            $count = random_int(0, 4); // 0–4 registrations per day
            for ($i = 0; $i < $count; $i++) {
                $p = Patient::factory()->create();
                $created = $day->copy()->setTime(random_int(8, 17), random_int(0, 59));
                $p->created_at = $created;
                $p->updated_at = $created;
                $p->save();
            }
        }

        // 2) Create visits throughout the month
        $patients = Patient::pluck('id');
        $staff = Staff::pluck('id');
        $services = Service::pluck('id');

        $visitCount = 60;
        for ($i = 0; $i < $visitCount; $i++) {
            $ts = Carbon::createFromTimestamp(random_int($startOfMonth->timestamp, $end->timestamp));
            $scheduled = $ts->copy()->setTime(random_int(8, 18), random_int(0, 59));
            $statusPool = ['Pending', 'Confirmed', 'Completed'];
            $status = $statusPool[array_rand($statusPool)];

            VisitService::factory()->create([
                'patient_id' => $patients->random(),
                'staff_id' => $staff->random(),
                'service_id' => $services->random(),
                'scheduled_at' => $scheduled,
                'status' => $status,
                'cost' => random_int(200, 1200),
            ]);
        }

        // Guarantee some visits today and next 24h so the table is not empty
        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();
        for ($i = 0; $i < 6; $i++) {
            VisitService::create([
                'patient_id' => $patients->random(),
                'staff_id' => $staff->random(),
                'service_id' => $services->random(),
                'scheduled_at' => Carbon::createFromTimestamp(random_int($todayStart->timestamp, $todayEnd->timestamp)),
                'status' => 'Confirmed',
                'cost' => random_int(200, 1200),
            ]);
        }
        $nextDayEnd = Carbon::now()->addDay()->endOfDay();
        for ($i = 0; $i < 6; $i++) {
            VisitService::create([
                'patient_id' => $patients->random(),
                'staff_id' => $staff->random(),
                'service_id' => $services->random(),
                'scheduled_at' => Carbon::createFromTimestamp(random_int($todayEnd->timestamp + 1, $nextDayEnd->timestamp)),
                'status' => 'Pending',
                'cost' => random_int(200, 1200),
            ]);
        }

        // 3) Create invoices for a subset of visits (some paid, some unpaid)
        $visits = VisitService::inRandomOrder()->limit(30)->get();
        foreach ($visits as $v) {
            $paid = (bool) random_int(0, 1);
            $grand = $v->cost ?: random_int(200, 1200);
            $invoice = Invoice::factory()->create([
                'patient_id' => $v->patient_id,
                'amount' => $grand,
                'grand_total' => $grand,
                'status' => $paid ? 'paid' : 'unpaid',
                'paid_at' => $paid ? Carbon::now()->subDays(random_int(0, 10)) : null,
            ]);
        }

        // 4) Create some insurance claims (some pending)
        InsuranceClaim::factory()->count(12)->create();
    }
}
