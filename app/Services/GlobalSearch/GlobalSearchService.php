<?php

namespace App\Services\GlobalSearch;

use App\Models\Event;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\MarketingCampaign;
use App\Models\MarketingLead;
use App\Models\Partner;
use App\Models\Patient;
use App\Models\Referral;
use App\Models\Service;
use App\Models\SharedInvoice;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\VisitService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GlobalSearchService
{
    /**
     * Perform global search across multiple entities
     */
    public function search(string $query): array
    {
        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        $results = collect();

        // Search across all entities, handling exceptions gracefully
        try {
            $results = $results->merge($this->searchPatients($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for patients: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchStaff($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for staff: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchVisitServices($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for visit services: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchInvoices($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for invoices: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchSharedInvoices($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for shared invoices: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchMarketingCampaigns($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for marketing campaigns: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchMarketingLeads($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for marketing leads: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchInventoryItems($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for inventory items: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchSuppliers($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for suppliers: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchInsurancePolicies($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for insurance policies: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchInsuranceClaims($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for insurance claims: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchEvents($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for events: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchServices($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for services: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchReferrals($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for referrals: ' . $e->getMessage());
        }

        try {
            $results = $results->merge($this->searchPartners($query));
        } catch (\Exception $e) {
            Log::warning('Global search failed for partners: ' . $e->getMessage());
        }

        // Sort by relevance and limit results
        return $results->sortByDesc('relevance')
            ->take(50)
            ->values()
            ->toArray();
    }

    // Helper method to determine the appropriate LIKE operator
    private function getLikeOperator(): string
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        // PostgreSQL uses ILIKE for case-insensitive search
        if ($driver === 'pgsql') {
            return 'ILIKE';
        }

        // MySQL and SQLite use LIKE with case-insensitive collation
        return 'LIKE';
    }

    // Search methods for various entities
    private function searchPatients(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $patients = Patient::select('id', 'full_name', 'patient_code', 'phone_number', 'email')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('full_name', $likeOperator, '%' . $query . '%')
                    ->orWhere('patient_code', $likeOperator, '%' . $query . '%')
                    ->orWhere('phone_number', $likeOperator, '%' . $query . '%')
                    ->orWhere('email', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $patients->map(function ($patient) {
            return [
                'type' => 'Patient',
                'category' => 'Healthcare',
                'title' => $patient->full_name,
                'description' => 'Code: ' . ($patient->patient_code ?? 'N/A') . ' • ' . ($patient->phone_number ?? 'No phone'),
                'url' => route('admin.patients.show', $patient->id),
                'relevance' => 100,
                'icon' => 'user',
            ];
        });
    }

    private function searchStaff(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $staff = Staff::select('id', 'first_name', 'last_name', 'email', 'position', 'role')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('first_name', $likeOperator, '%' . $query . '%')
                    ->orWhere('last_name', $likeOperator, '%' . $query . '%')
                    ->orWhere('email', $likeOperator, '%' . $query . '%')
                    ->orWhere('position', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $staff->map(function ($s) {
            $fullName = trim($s->first_name . ' ' . $s->last_name);

            return [
                'type' => 'Staff',
                'category' => 'Healthcare',
                'title' => $fullName ?: 'Staff Member',
                'description' => ($s->position ?? $s->role ?? 'Staff') . ' • ' . ($s->email ?? 'No email'),
                'url' => route('admin.staff.show', $s->id),
                'relevance' => 90,
                'icon' => 'user-check',
            ];
        });
    }

    private function searchVisitServices(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $visitServices = VisitService::select('id', 'service_type', 'status', 'scheduled_date_time')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('service_type', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $visitServices->map(function ($visit) {
            return [
                'type' => 'Visit Service',
                'category' => 'Healthcare',
                'title' => $visit->service_type ?? 'Visit Service',
                'description' => 'Status: ' . ($visit->status ?? 'N/A') . ' • ' . ($visit->scheduled_date_time ? date('M j, Y g:i A', strtotime($visit->scheduled_date_time)) : 'No date'),
                'url' => route('admin.visit-services.show', $visit->id),
                'relevance' => 85,
                'icon' => 'calendar',
            ];
        });
    }

    private function searchInvoices(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $invoices = Invoice::select('id', 'invoice_number', 'total_amount', 'status', 'due_date')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('invoice_number', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $invoices->map(function ($invoice) {
            return [
                'type' => 'Invoice',
                'category' => 'Financial',
                'title' => 'Invoice #' . $invoice->invoice_number,
                'description' => 'Amount: ' . ($invoice->total_amount ?? '0.00') . ' • Status: ' . ($invoice->status ?? 'N/A'),
                'url' => route('admin.invoices.show', $invoice->id),
                'relevance' => 80,
                'icon' => 'file-text',
            ];
        });
    }

    private function searchSharedInvoices(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $sharedInvoices = SharedInvoice::select('id', 'invoice_number', 'total_amount', 'status')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('invoice_number', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $sharedInvoices->map(function ($invoice) {
            return [
                'type' => 'Shared Invoice',
                'category' => 'Financial',
                'title' => 'Shared Invoice #' . $invoice->invoice_number,
                'description' => 'Amount: ' . ($invoice->total_amount ?? '0.00') . ' • Status: ' . ($invoice->status ?? 'N/A'),
                'url' => route('admin.shared-invoices.show', $invoice->id),
                'relevance' => 80,
                'icon' => 'file-text',
            ];
        });
    }

    private function searchMarketingCampaigns(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $campaigns = MarketingCampaign::select('id', 'name', 'status', 'start_date', 'end_date')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('name', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $campaigns->map(function ($campaign) {
            return [
                'type' => 'Marketing Campaign',
                'category' => 'Marketing',
                'title' => $campaign->name,
                'description' => 'Status: ' . ($campaign->status ?? 'N/A') . ' • ' . ($campaign->start_date ? date('M j, Y', strtotime($campaign->start_date)) : 'No start date'),
                'url' => route('admin.marketing-campaigns.show', $campaign->id),
                'relevance' => 75,
                'icon' => 'megaphone',
            ];
        });
    }

    private function searchMarketingLeads(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $leads = MarketingLead::select('id', 'name', 'email', 'phone', 'status')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('name', $likeOperator, '%' . $query . '%')
                    ->orWhere('email', $likeOperator, '%' . $query . '%')
                    ->orWhere('phone', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $leads->map(function ($lead) {
            return [
                'type' => 'Marketing Lead',
                'category' => 'Marketing',
                'title' => $lead->name,
                'description' => ($lead->email ?? 'No email') . ' • ' . ($lead->phone ?? 'No phone') . ' • Status: ' . ($lead->status ?? 'N/A'),
                'url' => route('admin.marketing-leads.show', $lead->id),
                'relevance' => 75,
                'icon' => 'target',
            ];
        });
    }

    private function searchInventoryItems(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $items = InventoryItem::select('id', 'name', 'sku', 'quantity', 'category')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('name', $likeOperator, '%' . $query . '%')
                    ->orWhere('sku', $likeOperator, '%' . $query . '%')
                    ->orWhere('category', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $items->map(function ($item) {
            return [
                'type' => 'Inventory Item',
                'category' => 'Inventory',
                'title' => $item->name,
                'description' => 'SKU: ' . ($item->sku ?? 'N/A') . ' • Qty: ' . ($item->quantity ?? '0') . ' • Category: ' . ($item->category ?? 'N/A'),
                'url' => route('admin.inventory-items.show', $item->id),
                'relevance' => 70,
                'icon' => 'package',
            ];
        });
    }

    private function searchSuppliers(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $suppliers = Supplier::select('id', 'name', 'contact_person', 'email', 'phone')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('name', $likeOperator, '%' . $query . '%')
                    ->orWhere('contact_person', $likeOperator, '%' . $query . '%')
                    ->orWhere('email', $likeOperator, '%' . $query . '%')
                    ->orWhere('phone', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $suppliers->map(function ($supplier) {
            return [
                'type' => 'Supplier',
                'category' => 'Inventory',
                'title' => $supplier->name,
                'description' => ($supplier->contact_person ?? 'No contact') . ' • ' . ($supplier->email ?? 'No email'),
                'url' => route('admin.suppliers.show', $supplier->id),
                'relevance' => 70,
                'icon' => 'truck',
            ];
        });
    }

    private function searchInsurancePolicies(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $policies = InsurancePolicy::select('id', 'policy_number', 'insurance_company', 'policy_holder_name', 'status')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('policy_number', $likeOperator, '%' . $query . '%')
                    ->orWhere('insurance_company', $likeOperator, '%' . $query . '%')
                    ->orWhere('policy_holder_name', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $policies->map(function ($policy) {
            return [
                'type' => 'Insurance Policy',
                'category' => 'Financial',
                'title' => 'Policy #' . $policy->policy_number,
                'description' => ($policy->insurance_company ?? 'N/A') . ' • Holder: ' . ($policy->policy_holder_name ?? 'N/A'),
                'url' => route('admin.insurance-policies.show', $policy->id),
                'relevance' => 75,
                'icon' => 'shield',
            ];
        });
    }

    private function searchInsuranceClaims(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $claims = InsuranceClaim::select('id', 'claim_number', 'status', 'total_amount')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('claim_number', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $claims->map(function ($claim) {
            return [
                'type' => 'Insurance Claim',
                'category' => 'Financial',
                'title' => 'Claim #' . $claim->claim_number,
                'description' => 'Amount: ' . ($claim->total_amount ?? '0.00') . ' • Status: ' . ($claim->status ?? 'N/A'),
                'url' => route('admin.insurance-claims.show', $claim->id),
                'relevance' => 75,
                'icon' => 'shield-check',
            ];
        });
    }

    private function searchEvents(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $events = Event::select('id', 'title', 'description', 'start_time', 'end_time')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('title', $likeOperator, '%' . $query . '%')
                    ->orWhere('description', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $events->map(function ($event) {
            return [
                'type' => 'Event',
                'category' => 'Events',
                'title' => $event->title,
                'description' => $event->description ?? 'No description',
                'url' => route('admin.events.show', $event->id),
                'relevance' => 70,
                'icon' => 'calendar-days',
            ];
        });
    }

    private function searchServices(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $services = Service::select('id', 'name', 'description', 'category')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('name', $likeOperator, '%' . $query . '%')
                    ->orWhere('description', $likeOperator, '%' . $query . '%')
                    ->orWhere('category', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $services->map(function ($service) {
            return [
                'type' => 'Service',
                'category' => 'Services',
                'title' => $service->name,
                'description' => ($service->description ?? 'No description') . ' • Category: ' . ($service->category ?? 'N/A'),
                'url' => route('admin.services.show', $service->id),
                'relevance' => 70,
                'icon' => 'activity',
            ];
        });
    }

    private function searchReferrals(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $referrals = Referral::select('id', 'referral_code', 'status', 'source', 'destination')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('referral_code', $likeOperator, '%' . $query . '%')
                    ->orWhere('status', $likeOperator, '%' . $query . '%')
                    ->orWhere('source', $likeOperator, '%' . $query . '%')
                    ->orWhere('destination', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $referrals->map(function ($referral) {
            return [
                'type' => 'Referral',
                'category' => 'Healthcare',
                'title' => 'Referral #' . $referral->referral_code,
                'description' => 'Status: ' . ($referral->status ?? 'N/A') . ' • From: ' . ($referral->source ?? 'N/A'),
                'url' => route('admin.referrals.show', $referral->id),
                'relevance' => 70,
                'icon' => 'users',
            ];
        });
    }

    private function searchPartners(string $query): Collection
    {
        $likeOperator = $this->getLikeOperator();
        $partners = Partner::select('id', 'name', 'contact_person', 'email', 'phone')
            ->where(function ($q) use ($query, $likeOperator) {
                $q->where('name', $likeOperator, '%' . $query . '%')
                    ->orWhere('contact_person', $likeOperator, '%' . $query . '%')
                    ->orWhere('email', $likeOperator, '%' . $query . '%')
                    ->orWhere('phone', $likeOperator, '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $partners->map(function ($partner) {
            return [
                'type' => 'Partner',
                'category' => 'Marketing',
                'title' => $partner->name,
                'description' => ($partner->contact_person ?? 'No contact') . ' • ' . ($partner->email ?? 'No email'),
                'url' => route('admin.partners.show', $partner->id),
                'relevance' => 70,
                'icon' => 'users',
            ];
        });
    }
}
