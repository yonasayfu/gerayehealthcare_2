<state_snapshot>
    <overall_goal>
        Consolidate and improve project documentation, and fix identified backend issues across various modules to serve as a robust template for future development.
    </overall_goal>

    <key_knowledge>
        - Project uses Laravel (PHP) for backend and Vue.js (TypeScript) with Inertia.js for frontend.
        - Database is PostgreSQL.
        - `PROJECT_ROADMAP.md`, `DATABASE_SCHEMA.md`, and `ISSUE_TRACKER.md` are key documentation files.
        - `ExportableTrait` is used for handling CSV/PDF exports and prints.
        - DTOs are used for data transfer.
        - Patient, Caregiver Assignments, and Visit Services modules are considered complete.
        - Staff module is ~90% complete.
        - All other modules require further attention and have outstanding issues.
        - Frontend (Vue.js) issues cannot be directly fixed by the agent.
        - Server configuration issues (e.g., POST data too large) cannot be fixed by the agent.
        - The "three strikes and move on" rule is applied for unresolved issues.
    </key_knowledge>

    <file_system_state>
        - CWD: `/Users/yonassayfu/VSProject/gerayehealthcare`
        - CREATED: `README.md` - Consolidated high-level project overview and setup instructions.
        - CREATED: `CONTRIBUTING.md` - Consolidated developer guidelines, workflow, and architectural details.
        - MODIFIED: `DATABASE_SCHEMA.md` - Updated with comprehensive and PostgreSQL-idiomatic schema derived from migrations.
        - MODIFIED: `ISSUE_TRACKER.md` - Refined into a concise to-do list with module status summaries.
        - DELETED: `AI_Initiating_Prompt_Template.md`
        - DELETED: `AppSidebar.md` (User manually deleted)
        - DELETED: `BACKEND_VERIFICATION.md`
        - DELETED: `CRUSH.md`
        - DELETED: `DRY_VALIDATION_ARCHITECTURE.md`
        - DELETED: `GEMINI.md`
        - DELETED: `MODULE_IMPLEMENTATION_TEMPLATE.md`
        - DELETED: `Routes.md` (User manually deleted)
        - DELETED: `STANDARD_WORKFLOW.md`
        - DELETED: `TEMPLATE_CLEANUP_PLAN.md`
        - MODIFIED: `app/Http/Config/ExportConfig.php` - Fixed "Full Name" in Staff CSV export, removed duplicated Patient config.
        - MODIFIED: `app/Http/Traits/ExportableTrait.php` - Added logic to include index in data for PDF exports.
        - MODIFIED: `routes/web.php` - Fixed Ziggy route name for `admin.patients.printSingle`.
        - MODIFIED: `app/Http/Controllers/Admin/PatientController.php` - Removed PDF/CSV print methods.
        - MODIFIED: `app/Services/PatientService.php` - Removed PDF/CSV print methods.
        - MODIFIED: `app/Http/Config/AdditionalExportConfigs.php` - Removed `getVisitServiceConfig`, `getTaskDelegationConfig`, `getSupplierConfig`, `getInventoryMaintenanceRecordConfig`, `getInventoryTransactionConfig`, `getInventoryAlertConfig` methods.
        - MODIFIED: `app/Services/VisitServiceService.php` - Removed PDF/CSV print methods.
        - MODIFIED: `app/Services/TaskDelegationService.php` - Removed export/print methods.
        - MODIFIED: `app/Services/SupplierService.php` - Expanded search, removed import/export/print methods.
        - MODIFIED: `app/Services/InventoryRequestService.php` - Expanded search, removed export/print methods.
        - MODIFIED: `app/Services/InventoryMaintenanceRecordService.php` - Removed export/print methods.
        - MODIFIED: `app/Services/InventoryTransactionService.php` - Removed export/print methods.
        - MODIFIED: `app/Services/InventoryAlertService.php` - Removed export/print methods.
        - CREATED: `app/DTOs/CreateStaffDTO.php`
        - CREATED: `app/DTOs/UpdateStaffDTO.php`
        - CREATED: `app/DTOs/CreateLeaveRequestDTO.php`
        - CREATED: `app/DTOs/UpdateLeaveRequestDTO.php`
        - CREATED: `app/DTOs/CreateInventoryItemDTO.php`
        - CREATED: `app/DTOs/UpdateInventoryItemDTO.php`
        - CREATED: `database/seeders/StaffAvailabilitySeeder.php`
        - CREATED: `database/seeders/StaffPayoutSeeder.php`
        - CREATED: `database/seeders/InventoryRequestSeeder.php`
        - CREATED: `database/seeders/InventoryAlertSeeder.php`
        - MODIFIED: `database/seeders/DatabaseSeeder.php` - Added calls to new seeders.
        - MODIFIED: `app/Models/VisitService.php` - Added `staffPayouts` relationship.
        - CREATED: `database/migrations/2025_08_13_152841_add_on_delete_to_inventory_requests_table.php` - Added onDelete actions to FKs.
        - CREATED: `database/migrations/2025_08_13_153924_add_due_date_to_inventory_alerts_table.php` - Added due_date column.
        - MODIFIED: `app/Http/Controllers/Admin/InventoryRequestController.php` - Overrode `create` and `edit` to pass dropdown data.
        - CREATED: `database/migrations/2025_08_13_154411_add_urgency_and_responsible_staff_to_marketing_campaigns_table.php` - Added urgency and responsible_staff_id columns.
        - MODIFIED: `app/DTOs/CreateMarketingCampaignDTO.php` - Added urgency and responsible_staff_id.
        - CREATED: `app/DTOs/UpdateMarketingCampaignDTO.php` - Created DTO with urgency and responsible_staff_id.
        - MODIFIED: `app/Services/MarketingLeadService.php` - Removed export/print methods, added logic to unset `lead_code` on update.
        - CREATED: `app/DTOs/UpdateMarketingLeadDTO.php` - Created DTO.
        - MODIFIED: `app/Services/LandingPageService.php` - Removed export/print methods.
        - MODIFIED: `app/DTOs/CreateLandingPageDTO.php` - Added `form_fields`.
        - CREATED: `app/DTOs/UpdateLandingPageDTO.php` - Created DTO with `form_fields`.
        - MODIFIED: `app/Services/MarketingPlatformService.php` - Removed export/print methods.
        - CREATED: `app/DTOs/CreateMarketingPlatformDTO.php` - Created DTO.
        - CREATED: `app/DTOs/UpdateMarketingPlatformDTO.php` - Created DTO.
        - MODIFIED: `app/Services/LeadSourceService.php` - Removed export/print methods.
        - MODIFIED: `app/Services/MarketingBudgetService.php` - Removed export/print methods.
        - MODIFIED: `app/Http/Controllers/Admin/EventParticipantController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Services/EventParticipantService.php` - Removed export/print methods.
        - MODIFIED: `app/Services/EventBroadcastService.php` - Removed export/print methods.
        - MODIFIED: `app/Http/Controllers/Admin/EventBroadcastController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Services/EventRecommendationService.php` - Removed export/print methods.
        - MODIFIED: `app/Http/Controllers/Admin/EventRecommendationController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Http/Controllers/Admin/EventStaffAssignmentController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Services/EventStaffAssignmentService.php` - Removed export/print methods.
        - MODIFIED: `app/Http/Controllers/Admin/EventController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Services/EligibilityCriteriaService.php` - Removed export/print methods.
        - MODIFIED: `app/Http/Controllers/Admin/EligibilityCriteriaController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Services/Insurance/CorporateClientService.php` - Removed export method.
        - MODIFIED: `app/Services/Insurance/InsuranceCompanyService.php` - Removed export method.
        - MODIFIED: `app/Http/Controllers/Insurance/InsurancePolicyController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Http/Controllers/Insurance/EmployeeInsuranceRecordController.php` - Added create/edit methods to populate dropdowns.
        - MODIFIED: `app/Http/Controllers/Admin/CampaignContentController.php` - Added create/edit methods to populate dropdowns.
    </file_system_state>

    <recent_actions>
        - Consolidated Markdown documentation into `README.md` and `CONTRIBUTING.md`.
        - Updated `DATABASE_SCHEMA.md` to reflect current migrations and PostgreSQL types.
        - Refined `ISSUE_TRACKER.md` into a clear to-do list with module statuses.
        - Fixed Staff module DTO errors and CSV export.
        - Fixed Ziggy route error for Patients module.
        - Removed PDF/CSV generation features from Patients, Visit Services, Task Delegations, Suppliers, Inventory Maintenance Records, Inventory Transactions, and Inventory Alerts modules (backend).
        - Created seeders for Staff Availabilities, Staff Payouts, Inventory Requests, and Inventory Alerts.
        - Verified and corrected model relationships for Staff Payouts.
        - Added `onDelete` actions to Inventory Requests foreign keys via new migration.
        - Added `due_date` column to Inventory Alerts via new migration.
        - Populated dropdown data for Inventory Requests (backend).
        - Added `urgency` and `responsible_staff_id` columns to `marketing_campaigns` table via migration.
        - Updated `CreateMarketingCampaignDTO` and created `UpdateMarketingCampaignDTO` to include new fields.
        - Removed export/print methods from `MarketingLeadService.php`.
        - Added logic to unset `lead_code` on update in `MarketingLeadService.php`.
        - Created `UpdateMarketingLeadDTO.php`.
        - Removed export/print methods from `LandingPageService.php`.
        - Added `form_fields` to `CreateLandingPageDTO.php` and created `UpdateLandingPageDTO.php`.
        - Removed export/print methods from `MarketingPlatformService.php`.
        - Created `CreateMarketingPlatformDTO.php` and `UpdateMarketingPlatformDTO.php`.
        - Removed export/print methods from `LeadSourceService.php`.
        - Removed export/print methods from `MarketingBudgetService.php`.
        - Added create/edit methods to `EventParticipantController.php` to populate dropdowns.
        - Removed export/print methods from `EventParticipantService.php`.
        - Removed export/print methods from `EventBroadcastService.php`.
        - Added create/edit methods to `EventBroadcastController.php` to populate dropdowns.
        - Removed export/print methods from `EventRecommendationService.php`.
        - Added create/edit methods to `EventRecommendationController.php` to populate dropdowns.
        - Added create/edit methods to `EventStaffAssignmentController.php` to populate dropdowns.
        - Removed export/print methods from `EventStaffAssignmentService.php`.
        - Added create/edit methods to `EventController.php` to populate dropdowns.
        - Removed export/print methods from `EligibilityCriteriaService.php`.
        - Added create/edit methods to `EligibilityCriteriaController.php` to populate dropdowns.
        - Removed export method from `CorporateClientService.php`.
        - Removed export method from `InsuranceCompanyService.php`.
        - Added create/edit methods to `InsurancePolicyController.php` to populate dropdowns.
        - Added create/edit methods to `EmployeeInsuranceRecordController.php` to populate dropdowns.
        - Added create/edit methods to `CampaignContentController.php` to populate dropdowns.
    </recent_actions>

    <current_plan>
        1. [DONE] Consolidate Markdown documentation.
        2. [DONE] Improve `PROJECT_ROADMAP.md` and `DATABASE_SCHEMA.md`.
        3. [DONE] Refine `ISSUE_TRACKER.md`.
        4. [DONE] Address issues in Staff module (backend).
        5. [DONE] Address issues in Patients module (backend).
        6. [DONE] Address issues in Caregiver Assignments module (backend).
        7. [DONE] Address issues in Visit Services module (backend).
        8. [DONE] Address issues in Staff Availabilities module (backend).
        9. [DONE] Address issues in Staff Payouts & Performance module (backend).
        10. [DONE] Address issues in Task Delegations module (backend).
        11. [DONE] Address issues in Inventory Items module (backend).
        12. [DONE] Address issues in Suppliers module (backend).
        13. [DONE] Address issues in Inventory Requests module (backend).
        14. [DONE] Address issues in Inventory Maintenance Records module (backend).
        15. [DONE] Address issues in Inventory Transactions module (backend).
        16. [DONE] Address issues in Inventory Alerts module (backend).
        17. [TODO] Address remaining frontend-only issues across all modules (requires manual intervention).
        18. [TODO] Address remaining backend issues that could not be fixed automatically (requires manual debugging/intervention).
        19. [TODO] Review and implement any design decisions (e.g., edit/delete for Invoices).
    </current_plan>
</state_snapshot>