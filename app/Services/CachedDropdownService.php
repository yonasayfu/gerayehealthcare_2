<?php

namespace App\Services;

use App\Models\Event;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Support\Facades\Cache;

class CachedDropdownService
{
    /**
     * Get cached active staff for dropdowns
     */
    public static function getActiveStaff()
    {
        return Cache::remember('dropdown_active_staff', 300, function () {
            return Staff::select('id', 'first_name', 'last_name', 'position', 'department')
                ->where('status', 'Active')
                ->orderBy('first_name')
                ->limit(500)
                ->get();
        });
    }

    /**
     * Get cached patients for dropdowns
     */
    public static function getPatients()
    {
        return Cache::remember('dropdown_patients', 300, function () {
            return Patient::select('id', 'full_name', 'phone_number', 'patient_code')
                ->orderBy('full_name')
                ->limit(1000)
                ->get();
        });
    }

    /**
     * Get cached insurance companies
     */
    public static function getInsuranceCompanies()
    {
        return Cache::remember('dropdown_insurance_companies', 900, function () {
            return InsuranceCompany::select('id', 'name')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached insurance policies
     */
    public static function getInsurancePolicies()
    {
        return Cache::remember('dropdown_insurance_policies', 900, function () {
            return InsurancePolicy::select('id', 'service_type', 'coverage_percentage')
                ->orderBy('service_type')
                ->get();
        });
    }

    /**
     * Get cached services
     */
    public static function getServices()
    {
        return Cache::remember('dropdown_services', 900, function () {
            return Service::select('id', 'name', 'price')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached events
     */
    public static function getEvents()
    {
        return Cache::remember('dropdown_events', 600, function () {
            return Event::select('id', 'title', 'event_date')
                ->orderBy('event_date', 'desc')
                ->limit(100)
                ->get();
        });
    }

    /**
     * Get cached marketing campaigns
     */
    public static function getMarketingCampaigns()
    {
        return Cache::remember('dropdown_marketing_campaigns', 600, function () {
            return \App\Models\MarketingCampaign::select('id', 'campaign_name')
                ->orderBy('campaign_name')
                ->limit(200)
                ->get();
        });
    }

    /**
     * Get cached campaign content
     */
    public static function getCampaignContent()
    {
        return Cache::remember('dropdown_campaign_content', 600, function () {
            return \App\Models\CampaignContent::select('id', 'title')
                ->orderBy('title')
                ->limit(500)
                ->get();
        });
    }

    /**
     * Get cached staff with user names (for marketing tasks)
     */
    public static function getStaffWithUsers()
    {
        return Cache::remember('dropdown_staff_with_users', 300, function () {
            return Staff::with('user:id,name')
                ->select('id', 'user_id')
                ->orderBy('id', 'asc')
                ->limit(500)
                ->get();
        });
    }

    /**
     * Get cached partners
     */
    public static function getPartners()
    {
        return Cache::remember('dropdown_partners', 600, function () {
            return \App\Models\Partner::select('id', 'name')
                ->orderBy('name')
                ->limit(200)
                ->get();
        });
    }

    /**
     * Get cached partner agreements
     */
    public static function getPartnerAgreements()
    {
        return Cache::remember('dropdown_partner_agreements', 600, function () {
            return \App\Models\PartnerAgreement::select('id', 'agreement_title')
                ->orderBy('agreement_title')
                ->limit(200)
                ->get();
        });
    }

    /**
     * Get cached referrals
     */
    public static function getReferrals()
    {
        return Cache::remember('dropdown_referrals', 300, function () {
            return \App\Models\Referral::select('id', 'referral_date')
                ->orderBy('referral_date', 'desc')
                ->limit(500)
                ->get();
        });
    }

    /**
     * Get cached invoices for dropdowns
     */
    public static function getInvoices()
    {
        return Cache::remember('dropdown_invoices', 300, function () {
            return \App\Models\Invoice::select('id', 'invoice_number')
                ->orderBy('invoice_number')
                ->limit(1000)
                ->get();
        });
    }

    /**
     * Clear all dropdown caches
     */
    public static function clearAll()
    {
        $keys = [
            'dropdown_active_staff',
            'dropdown_patients',
            'dropdown_insurance_companies',
            'dropdown_insurance_policies',
            'dropdown_services',
            'dropdown_events',
            'dropdown_marketing_campaigns',
            'dropdown_campaign_content',
            'dropdown_staff_with_users',
            'dropdown_partners',
            'dropdown_partner_agreements',
            'dropdown_referrals',
            'dropdown_invoices',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Refresh all dropdown caches
     */
    public static function refreshAll()
    {
        self::clearAll();

        // Pre-load all caches
        self::getActiveStaff();
        self::getPatients();
        self::getInsuranceCompanies();
        self::getInsurancePolicies();
        self::getServices();
        self::getEvents();
        self::getMarketingCampaigns();
        self::getCampaignContent();
        self::getStaffWithUsers();
        self::getPartners();
        self::getPartnerAgreements();
        self::getReferrals();
        self::getInvoices();
    }

    /**
     * Get custom dropdown data with caching
     *
     * @return mixed
     */
    public static function getCustom(string $key, callable $callback, int $ttl = 300)
    {
        $cacheKey = 'dropdown_custom_'.$key;

        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Forget custom dropdown data
     */
    public static function forgetCustom(string $key): bool
    {
        return Cache::forget('dropdown_custom_'.$key);
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        $cacheKeys = [
            'dropdown_active_staff', 'dropdown_patients', 'dropdown_insurance_companies',
            'dropdown_services', 'dropdown_events', 'dropdown_marketing_campaigns',
            'dropdown_campaign_content', 'dropdown_staff_with_users', 'dropdown_partners',
            'dropdown_partner_agreements', 'dropdown_referrals', 'dropdown_invoices',
        ];

        $stats = [];
        foreach ($cacheKeys as $key) {
            $stats[$key] = Cache::has($key) ? 'cached' : 'not_cached';
        }

        return $stats;
    }

    /**
     * Refresh specific cache
     */
    public static function refresh(string $type): void
    {
        $cacheKey = 'dropdown_'.strtolower($type);
        Cache::forget($cacheKey);

        $method = 'get'.ucfirst($type);
        if (method_exists(self::class, $method)) {
            self::$method();
        }
    }

    /**
     * Get memory usage statistics
     */
    public static function getMemoryStats(): array
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'cache_stats' => self::getCacheStats(),
        ];
    }
}
