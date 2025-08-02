# Frontend Template Cleanup Plan

## Current State Analysis
- **50+ individual Blade templates** in `/resources/views/pdf/`
- Each template ~800-2000 bytes of mostly duplicated code
- Total: ~60KB of template files with massive duplication

## Proposed Universal Template System

### Templates We Keep (2 total):
1. `pdf/universal-report.blade.php` - For all list/tabular reports
2. `pdf/universal-single-record.blade.php` - For individual record details

### Templates We Can Delete (50+ files):
All individual model templates become unnecessary:

#### Assignment Templates (3 files → 0):
- assignment-single.blade.php
- assignments-all.blade.php  
- assignments.blade.php

#### Campaign Templates (5 files → 0):
- campaign-contents.blade.php
- marketing/campaigns.blade.php
- marketing/campaigns-current.blade.php
- marketing/campaigns-single.blade.php
- marketing/campaign_performance_all.blade.php

#### Corporate Client Templates (2 files → 0):
- corporate_client_single.blade.php
- corporate_clients.blade.php

#### Event Templates (8 files → 0):
- event-broadcast-single.blade.php
- event-broadcasts.blade.php
- event-participant-single.blade.php
- event-participants.blade.php
- event-recommendation-single.blade.php
- event-recommendations.blade.php
- event-single.blade.php
- events.blade.php

#### Insurance Templates (6 files → 0):
- insurance_claim_single.blade.php
- insurance_claims.blade.php
- insurance_companies.blade.php
- insurance_company_single.blade.php
- insurance_policies.blade.php
- insurance_policy_single.blade.php

#### Inventory Templates (8 files → 0):
- inventory-alerts.blade.php
- inventory-items.blade.php
- inventory-maintenance-records.blade.php
- inventory-requests.blade.php
- inventory-transactions.blade.php
- inventory_items_single_pdf.blade.php
- inventory_requests.blade.php (duplicate)
- inventory_requests_pdf.blade.php

#### Marketing Templates (6 files → 0):
- marketing-budgets.blade.php
- marketing-lead-single.blade.php
- marketing-leads.blade.php
- marketing-platform-single.blade.php
- marketing-platforms.blade.php
- marketing-tasks.blade.php

#### Staff & Patient Templates (4 files → 0):
- patient-single.blade.php
- patient_single.blade.php (duplicate)
- patients.blade.php
- staff.blade.php

#### Other Templates (10+ files → 0):
- eligibility-criteria-single.blade.php
- eligibility-criteria.blade.php
- ethiopian_calendar_day_single.blade.php
- ethiopian_calendar_days.blade.php
- exchange_rate_single.blade.php
- exchange_rates.blade.php
- landing-pages.blade.php
- lead-source-single.blade.php
- lead-sources.blade.php
- suppliers.blade.php
- task_delegations.blade.php

## Benefits of Universal Template System

### 🎯 Code Reduction:
- **From:** 50+ templates (~60KB)
- **To:** 2 templates (~2KB)
- **Reduction:** ~97% file reduction

### 🚀 Maintenance Benefits:
- **No new templates** needed for new models
- **Single point of styling** changes
- **Consistent behavior** across all PDFs
- **Easier testing** and debugging

### 🔧 How It Works:
1. Backend generates data and config
2. Universal template receives config + data
3. Single template renders any model type
4. All styling/branding centralized

## Implementation Steps:
1. ✅ Create universal templates
2. ✅ Update ExportableTrait to use universal templates  
3. 🔄 Test with existing models
4. 🔄 Delete all individual templates
5. 🔄 Update any direct template references

## Result:
**50+ templates → 2 templates = 97% reduction + infinite maintainability improvement**
