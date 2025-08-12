# Project Issues Tracker

## Agent Workflow, Labels, and Templates

Use this section when working with browser-based AI agents that lack full repo access.

- **Labels (apply to each issue):**
  - Priority: Blocker | High | Medium | Low
  - Type: Bug | Feature | Refactor | Data | UX
  - Status: Todo | In-Progress | Blocked | Done

- **Attachments to include per issue:**
  1) PROJECT_ROADMAP.md
  2) DATABASE_SCHEMA.md
  3) ISSUE_TRACKER.md (only the specific issue excerpt)
  4) Minimal related code: controller(s), model(s), service(s), Vue pages/components, routes, config

- **Issue Brief Template (copy-paste to the agent):**
  - Title: <concise single-scope change>
  - Module: <e.g., Patients>
  - Priority/Type/Status: <High | Bug | Todo>
  - Current Behavior: <what happens>
  - Expected Behavior: <what should happen>
  - Steps to Reproduce: <short list>
  - Files Provided: <paths>
  - Routes/UI Entry Points: <route names + Vue paths>
  - Constraints/Guidelines: <coding standards, do/don'ts>
  - Acceptance Criteria: <bullet list of checks>

- **Execution checklist per issue:**
  1) Share the three docs + minimal files
  2) Ask the agent to propose a minimal diff/PR
  3) If more files are requested, share only those
  4) Run locally, verify acceptance criteria, then mark Done

## Phase 1: Foundational Core Modules (Staff, Patient, and Visit Services)

### Module 1: Staff
- [x] Fix the foreign key violation error on staff deletion.
- [x] Resolve the Class "App\Http\Controllers\Admin\CreateStaffDTO" not found errors.
- [x] Address the "The POST data is too large" error during file attachment on the create page.
- [x] Correct the empty "Full Name" column in the exported CSV.
- [x] Ensure the "Department" input on the edit page is a dropdown populated with available departments.
- [x] Resolve the "Undefined array key" error for "Print All."
- [x] Remove the extra "Print Document" buttons on the show.vue page and fix the UI for "Print Current" to prevent the footer from shrinking.

### Module 2: Patients
- [ ] Fix the Ziggy error for the 'admin.patients.printSingle' route in show.vue.
- [ ] Ensure the "Date of Birth (Gregorian)" and "Phone Number" are correctly populated and formatted on the edit.vue page.
- [ ] Implement automatic population of Gregorian and Ethiopian dates when one is set.
- [ ] Resolve the issue where dropdowns for "Employer (Corporate Client)" and "Source" are not visible due to text color. This issue also affects the create.vue page.
- [ ] Fix the submission and update functionality, which is failing due to the above issues.
- [ ] Remove PDF download/generation features from both the frontend and backend, as requested.

### Module 3: Caregiver Assignments
- [ ] Correct the empty "PATIENT NAME STAFF MEMBER" column in the exported CSV.
- [ ] Fix the PDF download functionality to preview the PDF in the browser instead of directly downloading it.
- [ ] Fix the print page button to use the correct template and not include unwanted headers and data.
- [ ] Ensure print all opens a preview in the browser before downloading.
- [ ] Resolve the TypeError: Cannot read properties of undefined (reading 'id') errors in Show.vue and Edit.vue.
- [ ] Remove the PDF download/generation features from the frontend and backend.

### Module 4: Visit Services
- [ ] Fix the data fetching and display on Show.vue to correctly reflect the index view.
- [ ] Resolve the Call to a member function store() on string error when updating, and ensure attachments are working correctly.
- [ ] Populate the patient and assigned staff dropdowns on the create.vue page and ensure new visits can be created with working attachments.
- [ ] Remove the extra pagination element on the left side of the UI.
- [ ] Correct the "per page" view to include the number 10.
- [ ] Remove the PDF download/generation features from the frontend and backend.

## Phase 2: Administrative and Inventory Modules

### Module 5: Staff Availabilities
- [ ] Fix the staff member dropdown to show available staff so that new availability slots can be created.
- [ ] Create a special seeder to test this module.
- [ ] Review the relationships with other modules to ensure data integrity.

### Module 6: Staff Payouts & Performance
- [ ] Add a special seeder to populate test data for this module.
- [ ] Review the relationships with other modules to ensure data integrity.

### Module 7: Task Delegations
- [ ] Remove the CSV, PDF, and print features.
- [ ] Populate the "assign task to" dropdown on the create page by checking relationships with other models.

### Module 8: Staff Leave Requests
- [ ] Resolve the Class "App\Http\Controllers\Admin\CreateLeaveRequestDTO" not found error when approving or denying requests.
- [ ] Create a special seeder and test the feature from both the admin and staff perspectives.

### Module 9: Inventory Items
- [ ] Fix the issue where nothing happens on submission for both create.vue and edit.vue.
- [ ] Resolve the Cannot redeclare error for exporting CSV and "Print All."
- [ ] Fix the Method ...::generateSinglePdf does not exist error when clicking "download pdf" on show.vue.
- [ ] Fix the UI for "Print Current" to be consistent with other modules.
- [ ] Add an "import CSV" feature that matches the export format.
- [ ] Make the "download pdf" button on show.vue consistent with other modules.

### Module 10: Suppliers
- [ ] Expand the search functionality to include name, contact person, and phone number, using ilike instead of like.
- [ ] Remove the import CSV and PDF download features.
- [ ] Resolve the Cannot redeclare error for "Print All."
- [ ] Fix the Method ...::generateSinglePdf does not exist error for the "download pdf" button on show.vue and rename it for consistency.
- [ ] Resolve the Class "App\Http\Controllers\Admin\CreateSupplierDTO" not found error on submission from edit.vue.

### Module 11: Inventory Requests
- [ ] Add "requester" to the search functionality.
- [ ] Remove the export CSV and PDF features.
- [ ] Fix the "Print All" functionality, as nothing happens when clicked.
- [ ] Resolve the TypeError: Cannot read properties of undefined error on show.vue.
- [ ] Populate the "Requester" and "Item" dropdowns on edit.vue and create.vue.
- [ ] Address the foreign key violation error on deletion.
- [ ] Create a special seeder to test this module from both the staff and admin sides.

### Module 12: Inventory Maintenance Records
- [ ] Fix the "Print All" and "Print Current" functionalities, and make the UI consistent.
- [ ] Resolve the pagination issue where the next page disappears.
- [ ] Fix the Cannot redeclare error for CSV export.
- [ ] Resolve the Page not found error for show.vue.
- [ ] Fix the TypeError: Cannot read properties of undefined error on edit.vue.

### Module 13: Inventory Transactions
- [ ] Populate the "Item" and "Performed By" fields on show.vue.
- [ ] Fix the UI for single prints to remove extra buttons.
- [ ] Add "edit" and "delete" buttons to the action column in index.vue.
- [ ] Remove the PDF generation feature and add "Print All."
- [ ] Fix the pagination indicator to be consistent with other modules.

### Module 14: Inventory Alerts
- [ ] Remove all export and print features.
- [ ] Fix the pagination and its indicator to be consistent with other modules.
- [ ] Create a special seeder and test the module to ensure it works correctly.
- [ ] Review the logic for disabling alerts and add a "due date" column for viewing.

### Module 15: Invoices
- [ ] Change the view action button to an icon for consistency.
- [ ] Check the module's relationships and usage to determine if "edit" and "delete" are needed.
- [ ] Populate the dropdowns for patient and service on the "Create New Invoice" page.
- [ ] Add search functionality to index.vue.

## Phase 3: Marketing and Events Modules

### Module 16: Marketing Campaigns
- [ ] Remove PDF and CSV generation.
- [ ] Add columns for "urgentness/mandatoryness" and "responsible staff."
- [ ] Ensure the "Print Current" functionality works and use its UI template for all other print features.
- [ ] Fix the "Print All" functionality.

### Module 17: Marketing Leads
- [ ] Populate the "Source Campaign" and "Assigned Staff" columns in index.vue.
- [ ] Remove CSV and PDF features.
- [ ] Fix the issue where dropdowns for "Source," "Campaign," "Landing Page," "Converted Patient," and "Assigned Staff" are empty on both create.vue and edit.vue.
- [ ] Resolve the Not null violation error for lead_code on edit.vue.
- [ ] Verify that pagination works correctly.

### Module 18: Landing Pages
- [ ] Populate the "Campaign" column in index.vue.
- [ ] Remove CSV and PDF features.
- [ ] Fix the "Print All" functionality.
- [ ] Ensure "Print Current" uses the same UI as the Marketing Campaigns module.
- [ ] Address the null value for "Additional Detail Form Fields."
- [ ] Populate dropdowns and ensure submission works for create.vue and edit.vue.
- [ ] Fix the pagination UI.

### Module 19: Marketing Platforms
- [ ] Remove export PDF, CSV, and "Print All."
- [ ] Fix the "Print Current" functionality, as it shows an empty page.
- [ ] Resolve the Class "App\Http\Controllers\Admin\CreateMarketingPlatformDTO" not found error on edit.vue.
- [ ] Resolve the Method ...::toggleStatus does not exist error when clicking "active/disactive."
- [ ] Ensure the UI for show.vue and edit.vue is used as the standard for all modules.

### Module 20: Lead Sources
- [ ] Resolve the Method ...::toggleStatus does not exist error when activating a lead source.
- [ ] Remove export CSV, PDF, and "Print All."
- [ ] Fix "Print Current," as it is empty.
- [ ] Fix the pagination UI for "per page 5."
- [ ] Fix the empty print document on show.vue.
- [ ] Resolve the Class "App\Http\Controllers\Admin\CreateLeadSourceDTO" not found error on edit.vue.
- [ ] Populate the "select category" dropdown on create.vue.

### Module 21: Marketing Budgets
- [ ] Remove PDF and CSV export.
- [ ] Fix "Print Current," as it is empty.
- [ ] Fix "Print All," as there is no response.
- [ ] Populate the "select campaign" and "select platform" dropdowns on create.vue and edit.vue.
- [ ] Add a label and fix the UI for edit.vue, using the marketing platforms module as a reference.
- [ ] Fix the pagination indicator glitch.

### Module 22: Campaign Contents Management
- [ ] Remove export PDF and CSV.
- [ ] Fix the display of "Metrics (JSON)" on show.vue to be more clear.
- [ ] Populate all dropdowns on create.vue and edit.vue and check relationships.

### Module 23: Marketing Tasks Management
- [ ] Add an "expected results" column.
- [ ] Remove CSV and PDF generation.
- [ ] Fix "Print Current" and "Print All."
- [ ] Fix the "per page 5" pagination.
- [ ] Populate the filter and other dropdowns on create.vue and edit.vue.

### Module 24: Marketing Analytics
- [ ] Ensure this module correctly populates data from related modules.

### Module 25: Events
- [ ] Fix the issue where nothing happens after data is inserted and submitted on create.vue and edit.vue.
- [ ] Remove CSV and PDF generation.
- [ ] Fix the Element is missing end tag error on show.vue.

### Module 26: Eligibility Criteria
- [ ] Resolve the TypeError: Cannot read properties of undefined (reading 'data') error.

### Module 27: Event Recommendations
- [ ] Populate the "source," "Recommended By," and "patient phone" columns in index.vue.
- [ ] Remove CSV and PDF generation.
- [ ] Fix pagination for "per page 5."
- [ ] Fix the UI of the print document on show.vue.
- [ ] Populate the "Event ID" dropdown on create.vue and fix the submission issue.
- [ ] Resolve the Argument #1 ($id) must be of type int, string given error for print all.
- [ ] Fix the UI for "current print."

### Module 28: Event Staff Assignments
- [ ] Add the staff's name to the column in index.vue instead of just the ID.
- [ ] Resolve the TypeError: Cannot read properties of undefined (reading 'role') error on show.vue.
- [ ] Resolve the TypeError: Cannot read properties of undefined (reading 'event_id') error on edit.vue.
- [ ] Resolve the Argument #1 ($id) must be of type int, string given error for print all.
- [ ] Fix the UI for "print current" to use the recommended UI.

### Module 29: Event Broadcasts
- [ ] Resolve the Class "App\Http\Controllers\Admin\CreateEventBroadcastDTO" not found error on create.vue.
- [ ] Remove other export features and only keep "print current."
- [ ] Create a seeder and test all features.

## Phase 4: Insurance and User Management Modules

### Module 30: Insurance Companies
- [ ] Remove PDF generation.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current" using the recommended UI.
- [ ] Remove the "Amharic Name" column.
- [ ] Fix the UI for show.vue.
- [ ] Fix the search functionality and its UI.

### Module 31: Corporate Clients
- [ ] Fix the search functionality and its UI.
- [ ] Remove the "Organization Name (Amharic)" column.
- [ ] Fix the UI for show.vue.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current."

### Module 32: Insurance Policies
- [ ] Populate dropdown data on create.vue and edit.vue and check if submission works.
- [ ] Remove CSV and PDF.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current."
- [ ] Remove the "Service Type (Amharic)" column.
- [ ] Fix the UI for show.vue and edit.vue.

### Module 33: Employee Insurance Records
- [ ] Use "Fayda ID" instead of "Federal ID" for consistency.
- [ ] Fix the UI for show.vue and edit.vue.
- [ ] Populate dropdown data on create.vue and edit.vue.
- [ ] Resolve the Call to undefined method error for print all.
- [ ] Fix the UI for "print current."

### Module 34: Insurance Claims
- [ ] Remove CSV and PDF.
- [ ] Fix "print all" and "print current."
- [ ] Resolve the "Loading" message and Vue warn on show.vue.
- [ ] Resolve the ReferenceError: format is not defined on edit.vue.
- [ ] Resolve the Class "App\Http\Controllers\Insurance\InsuranceClaimRules" not found error on create.vue.

### Module 35: Ethiopian Calendar Days
- [ ] Fix the UI of show.vue.
- [ ] Remove the "region" column.

### Module 36: Role Management
- [ ] Fix the "Assign Permissions" feature on create and edit pages to be an editable input or dropdown.

### Module 37: User Management
- [ ] Recheck if "add new user" is the same as "register new staff."
- [ ] Address the registration feature on the landing page and manage user self-registration to ensure it is not publicly exposed.
