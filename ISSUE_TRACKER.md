# Project To-Do List & Issue Tracker

> New to this codebase? Read [AI_AGENT_ONBOARDING.md](./AI_AGENT_ONBOARDING.md) first for architecture, status, and how to work issues effectively.

This document outlines the current development tasks and known issues for the Home-to-Home Care Platform.

## Module Status Summary

*   **Patient Management**: Complete
*   **Caregiver Assignments**: Complete
*   **Visit Services**: Complete
*   **Staff**: Complete 
*   **All Other Modules**: In Progress / Requires Attention (specific issues listed below)

---

## Global UX/Polish (not Completed)

- [x] Standardize UI elements (buttons, etc.) across modules (Staff)
- [x] Fix Spatie\LaravelData\Data not found in `CreateStaffDTO`
- [x] Recheck index number and CSV export for consistency
- [x] Make print current/single UI more visually appealing ("fancy")
- [x] Move search icon to right inside search box

## Detailed To-Do List

### Phase 1: Foundational Core Modules

#### Module: Staff (Complete 100%)
- [ ] Fix the foreign key violation error on staff deletion.
- [ ] Resolve the Class "App\\Http\\Controllers\\Admin\\CreateStaffDTO" not found errors.
- [ ] Address the "The POST data is too large" error during file attachment on the create page.
- [ ] Correct the empty "Full Name" column in the exported CSV.
- [ ] Ensure the "Department" input on the edit page is a dropdown populated with available departments.
- [ ] Resolve the "Undefined array key" error for "Print All."
- [ ] Remove the extra "Print Document" buttons on the show.vue page and fix the UI for "Print Current" to prevent the footer from shrinking.

#### Module: Patients (Complete 100%)
- [ ] Fix the Ziggy error for the 'admin.patients.printSingle' route in show.vue.
- [ ] Ensure the "Date of Birth (Gregorian)" and "Phone Number" are correctly populated and formatted on the edit.vue page.
- [ ] Implement automatic population of Gregorian and Ethiopian dates when one is set.
- [ ] Resolve the issue where dropdowns for "Employer (Corporate Client)" and "Source" are not visible due to text color. This issue also affects the create.vue page.
- [ ] Fix the submission and update functionality, which is failing due to the above issues.
- [ ] Remove PDF download/generation features from both the frontend and backend, as requested.

#### Module: Caregiver Assignments (Complete 100%)
- [ ] Correct the empty "PATIENT NAME STAFF MEMBER" column in the exported CSV.
- [ ] Fix the PDF download functionality to preview the PDF in the browser instead of directly downloading it.
- [ ] Fix the print page button to use the correct template and not include unwanted headers and data.
- [ ] Ensure print all opens a preview in the browser before downloading.
- [ ] Resolve the TypeError: Cannot read properties of undefined (reading 'id') errors in Show.vue and Edit.vue.
- [ ] N/A — centralized ExportableTrait with preview streaming replaces removal of PDF generation features.

#### Module: Visit Services (Complete 100%)
- [ ] Fix the data fetching and display on Show.vue to correctly reflect the index view.
- [ ] Resolve the Call to a member function store() on string error when updating, and ensure attachments are working correctly.
- [ ] Populate the patient and assigned staff dropdowns on the create.vue page and ensure new visits can be created with working attachments.
- [ ] Remove the extra pagination element on the left side of the UI.
- [ ] Correct the "per page" view to include the number 10.
- [ ] Remove the PDF download/generation features from the frontend and backend.

### Phase 2: Administrative and Inventory Modules

#### Module: Staff Availabilities  (Complete 100%)
- [ ] Fix the staff member dropdown to show available staff so that new availability slots can be created.
- [ ] Create a special seeder to test this module.
- [ ] Review the relationships with other modules to ensure data integrity.

#### Module: Staff Payouts & Performance (Complete 100%)
- [x] Add a special seeder to populate test data for this module.
- [x] Review the relationships with other modules to ensure data integrity.

#### Module: Task Delegations (Complete 100%)
- [x] Remove the CSV, PDF, and print features.
- [x] Populate the "assign task to" dropdown on the create page by checking relationships with other models.

#### Module: Staff Leave Requests (Complete 100%)
- [x] Resolve the Class "App\\Http\\Controllers\\Admin\\CreateLeaveRequestDTO" not found error when approving or denying requests.
- [x] Create a special seeder and test the feature from both the admin and staff perspectives.

#### Module: Inventory Items (Complete 100%)
- [x] Fix the issue where nothing happens on submission for both create.vue and edit.vue.
- [x] Resolve the Cannot redeclare error for exporting CSV and "Print All."
- [x] Fix the Method ...::generateSinglePdf does not exist error when clicking "download pdf" on show.vue.
- [ ] Fix the UI for "Print Current" to be consistent with other modules.
- [ ] Add an "import CSV" feature that matches the export format.
- [ ] Make the "download pdf" button on show.vue consistent with other modules.

#### Module: Suppliers (Complete 100%)
- [x] Expand the search functionality to include name, contact person, and phone number, using ilike instead of like.
- [x] Remove the import CSV and PDF download features.
- [x] Resolve the Cannot redeclare error for "Print All."
- [x] Fix the Method ...::generateSinglePdf does not exist error for the "download pdf" button on show.vue and rename it for consistency.
- [x] Resolve the Class "App\\Http\\Controllers\\Admin\\CreateSupplierDTO" not found error on submission from edit.vue.

#### Module: Inventory Requests (COMPLATE 100%)
- [x] Add "requester" to the search functionality.
- [x] Remove the export CSV and PDF features.
- [x] Fix the "Print All" functionality, as nothing happens when clicked.
- [x] Resolve the TypeError: Cannot read properties of undefined error on show.vue.
- [x] Populate the "Requester" and "Item" dropdowns on edit.vue and create.vue.
- [x] Address the foreign key violation error on deletion.
- [x] Create a special seeder to test this module from both the staff and admin sides.

#### Module: Inventory Maintenance Records (Complete 100%)
- [x] Fix the "Print All" and "Print Current" functionalities, and make the UI consistent.
- [ ] Resolve the pagination issue where the next page disappears.
- [x] Fix the Cannot redeclare error for CSV export.
- [ ] Resolve the Page not found error for show.vue.
- [ ] Fix the TypeError: Cannot read properties of undefined error on edit.vue.

#### Module: Inventory Transactions (Complete)
- [x] Backend: Populate the "Item" and "Performed By" fields on show.vue. (Backend is sending the data, frontend needs to display it)
- [ ] Fix the UI for single prints to remove extra buttons.
- [ ] Add "edit" and "delete" buttons to the action column in index.vue.
- [x] Remove the PDF generation feature and add "Print All."
- [ ] Fix the pagination indicator to be consistent with other modules.

#### Module: Inventory Alerts (Complete 100%)
- [x] Remove all export and print features.
- [x] Fix the pagination and its indicator to be consistent with other modules.
- [x] Create a special seeder and test the module to ensure it works correctly.
- [x] Review the logic for disabling alerts and add a "due date" column for viewing.

#### Module: Invoices (Complete 100%)
- [ ] Change the view action button to an icon for consistency.
- [ ] Check the module's relationships and usage to determine if "edit" and "delete" are needed.
- [x] Populate the dropdowns for patient and service on the "Create New Invoice" page.
- [ ] Add search functionality to index.vue.

### Phase 3: Marketing and Events Modules

#### Module: Marketing Campaigns (Complete)
- [x] Remove PDF and CSV generation.
- [x] Add columns for "urgentness/mandatoryness" and "responsible staff."
- [ ] Ensure the "Print Current" functionality works and use its UI template for all other print features.
- [ ] Fix the "Print All" functionality.

#### Module: Marketing Leads (Complete)
- [ ] Populate the "Source Campaign" and "Assigned Staff" columns in index.vue.
- [x] Remove CSV and PDF features.
- [x] Fix the issue where dropdowns for "Source," "Campaign," "Landing Page," "Converted Patient," and "Assigned Staff" are empty on both create.vue and edit.vue.
- [x] Resolve the Not null violation error for lead_code on edit.vue.
- [ ] Verify that pagination works correctly.

#### Module: Landing Pages (Complete)
- [ ] Populate the "Campaign" column in index.vue.
- [x] Remove CSV and PDF features.
- [ ] Fix the "Print All" functionality.
- [ ] Ensure "Print Current" uses the same UI as the Marketing Campaigns module.
- [x] Address the null value for "Additional Detail Form Fields."
- [x] Populate dropdowns and ensure submission works for create.vue and edit.vue.
- [ ] Fix the pagination UI.

#### Module: Marketing Platforms (Complete 100%)
- [x] Remove export PDF, CSV, and "Print All."
- [ ] Fix the "Print Current" functionality, as it shows an empty page.
- [x] Resolve the Class "App\\Http\\Controllers\\Admin\\CreateMarketingPlatformDTO" not found error on edit.vue.
- [ ] Resolve the Method ...::toggleStatus does not exist error when clicking "active/disactive."
- [ ] Ensure the UI for show.vue and edit.vue is used as the standard for all modules.

#### Module: Lead Sources (Complete)
- [ ] Resolve the Method ...::toggleStatus does not exist error when activating a lead source.
- [x] Remove export CSV, PDF, and "Print All."
- [ ] Fix "Print Current," as it is empty.
- [ ] Fix the pagination UI for "per page 5."
- [ ] Fix the empty print document on show.vue.
- [x] Resolve the Class "App\\Http\\Controllers\\Admin\\CreateLeadSourceDTO" not found error on edit.vue.
- [ ] Populate the "select category" dropdown on create.vue.

#### Module: Marketing Budgets (Complete 100%)
- [x] Remove PDF and CSV export.
- [ ] Fix "Print Current," as it is empty.
- [ ] Fix "Print All," as there is no response.
- [x] Populate the "select campaign" and "select platform" dropdowns on create.vue and edit.vue.
- [ ] Add a label and fix the UI for edit.vue, using the marketing platforms module as a reference.
- [ ] Fix the pagination indicator glitch.

#### Module: Campaign Contents Management (Complete 100%)
- [x] Remove export PDF and CSV.
- [ ] Fix the display of "Metrics (JSON)" on show.vue to be more clear.
- [x] Populate all dropdowns on create.vue and edit.vue and check relationships.

#### Module: Marketing Tasks Management (Complete 100%)
- [ ] Add an "expected results" column.
- [x] Remove CSV and PDF generation.
- [ ] Fix "Print Current" and "Print All."
- [ ] Fix the "per page 5" pagination.
- [ ] Populate the filter and other dropdowns on create.vue and edit.vue.

#### Module: Marketing Analytics (Complete 100%)
- [x] Ensure this module correctly populates data from related modules.

#### Module: Events (Complete 100%)
- [x] Fix the issue where nothing happens after data is inserted and submitted on create.vue and edit.vue.
- [x] Remove CSV and PDF generation.
- [x] Fix the Element is missing end tag error on show.vue.

#### Module: Eligibility Criteria (Complete 100%)
- [x] Resolve the TypeError: Cannot read properties of undefined (reading 'data') error.

#### Module: Event Recommendations (Complete 100%)
- [ ] Populate the "source," "Recommended By," and "patient phone" columns in index.vue.
- [x] Remove CSV and PDF generation.
- [ ] Fix pagination for "per page 5."
- [ ] Fix the UI of the print document on show.vue.
- [x] Populate the "Event ID" dropdown on create.vue and fix the submission issue.
- [ ] Resolve the Argument #1 ($id) must be of type int, string given error for print all.
- [ ] Fix the UI for "current print."

#### Module: Event Staff Assignments (Complete 100%)
- [ ] Add the staff's name to the column in index.vue instead of just the ID.
- [ ] Resolve the TypeError: Cannot read properties of undefined (reading 'role') error on show.vue.
- [ ] Resolve the TypeError: Cannot read properties of undefined (reading 'event_id') error on edit.vue.
- [ ] Resolve the Argument #1 ($id) must be of type int, string given error for print all.
- [ ] Fix the UI for "print current" to use the recommended UI.

#### Module: Event Broadcasts (Complete 100%)
- [x] Resolve the Class "App\\Http\\Controllers\\Admin\\CreateEventBroadcastDTO" not found error on create.vue.
- [x] Remove other export features and only keep "print current."
- [ ] Create a seeder and test all features.

### Phase 4: Insurance and User Management Modules

#### Module: Insurance Companies (Complete 100%)
- [x] Remove PDF generation.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current" using the recommended UI.
- [x] Remove the "Amharic Name" column.
- [ ] Fix the UI for show.vue.
- [ ] Fix the search functionality and its UI.

#### Module: Corporate Clients (Complete 100%)
- [ ] Fix the search functionality and its UI.
- [x] Remove the "Organization Name (Amharic)" column.
- [ ] Fix the UI for show.vue.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current."

#### Module: Insurance Policies (Complete)
- [x] Populate dropdown data on create.vue and edit.vue and check if submission works.
- [x] Remove CSV and PDF.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current."
- [x] Remove the "Service Type (Amh∆aric)" column.
- [ ] Fix the UI for show.vue and edit.vue.

#### Module: Employee Insurance Records (Complete)
- [x] Use "Fayda ID" instead of "Federal ID" for consistency.
- [ ] Fix the UI for show.vue and edit.vue.
- [x] Populate dropdown data on create.vue and edit.vue.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current."

#### Module: Insurance Claims (Complete)
- [x] Remove CSV and PDF.
- [ ] Fix "print all" and "print current."
- [ ] Resolve the "Loading" message and Vue warn on show.vue.
- [ ] Resolve the ReferenceError: format is not defined on edit.vue.
- [x] Resolve the Class "App\\Http\\Controllers\\Insurance\\InsuranceClaimRules" not found error on create.vue.

#### Module: Ethiopian Calendar Days (Complete)
- [ ] Fix the UI of show.vue.
- [x] Remove the "region" column.

#### Module: Role Management (Complete)
- [ ] Fix the "Assign Permissions" feature on create and edit pages to be an editable input or dropdown.

#### Module: User Management (Complete)
- [ ] Recheck if "add new user" is the same as "register new staff."
- [ ] Address the registration feature on the landing page and manage user self-registration to ensure it is not publicly exposed.
