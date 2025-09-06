# HR Config Usage Audit (Phase 2)

File: config/hr.php
Keys:
- departments: array of department names
- positions: array of position names

Consumers:
- Admin/StaffController::create/edit (to render form dropdowns)
- App\Services\OptimizedStaffService::getFormData (cached form data)

Notes:
- Both keys are actively used; no dead keys found.
- Access is standardized via config('hr.departments') and config('hr.positions').
- If future changes are needed (e.g., localization), centralize them in config/hr.php and reference via config().

