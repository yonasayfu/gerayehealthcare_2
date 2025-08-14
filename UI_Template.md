UI Consistency Reference (for module implementers)
Purpose: Give UI agents a compact, actionable guide to build or fix module UIs (like Staff) consistently with the rest of the app.

Use this as a template. Replace labels/fields per module.

1) Core Layouts
App shell: Inertia pages under resources/js/Pages/Admin/<Module>/
Index.vue (list)
Create.vue (form)
Edit.vue (form)
Card/page layout: Single-column container with page title, toolbar, table/form.
Spacing: Use consistent spacing scale (xs/sm/md/lg). Keep breathing room around toolbars and tables.
Typography: Use standard heading for page titles; body text 14–16px.
2) Standard Actions (Top Toolbar)
Buttons (left to right):
Add [Entity]
Export CSV
Print Current
Print All
Search: Right-aligned search input with debounce (300–500ms).
Per-page selector: Optional; default pagination is 5 (consistent with BaseService::getAll()).
Example toolbar (pseudo-Vue):

vue
<div class="toolbar">
  <div class="left">
    <Link href="{ route('admin.staff.create') }" class="btn btn-primary">Create Staff</Link>
  </div>
  <div class="right">
    <input v-model="filters.search" placeholder="Search by name, email, position..." />
    <button @click="exportCsv" class="btn btn-success"><Download class="h-4 w-4"/> Export CSV</button>
    <button @click="printCurrent" class="btn btn-dark"><Printer class="h-4 w-4"/> Print Current</button>
    <button @click="printAll" class="btn btn-info"><Printer class="h-4 w-4"/> Print All</button>
  </div>
</div>
3) Tables (Index pages)
Header order: Matches ExportConfig columns order when reasonable.
Sortable columns: Visual caret; clicking toggles asc/desc; syncs to URL query.
Index column: Optional leading “#” when include_index is true (mirrors PDF).
Truncation: Truncate long text with tooltip on hover.
Empty state: Friendly empty-state message with “Create” CTA.
Example table header cell (pseudo):

vue
<th @click="sortBy('last_name')" :class="{ sorted: sort==='last_name' }">
  Last Name
  <SortIcon :direction="direction" v-if="sort==='last_name'" />
</th>
4) Forms (Create/Edit)
Preload data: Only what’s needed to render the form (e.g., config('hr.departments') for Staff).
Validation display: Inline errors under fields; top-level alert for form submit errors.
Submit buttons:
Primary “Save”
Secondary “Cancel” (back to index)
Consistent field order: Name, Contacts, Role/Dept, Status, Dates, Notes.
Minimal form actions:

vue
<div class="form-actions">
  <button type="submit" class="btn btn-primary">Save</button>
  <Link :href="route('admin.staff.index')" class="btn">Cancel</Link>
</div>
5) Colors and Buttons
Primary: For Create/Save.
Outline/Neutral: For Export/Print actions.
Destructive: For Delete with confirm modal.
Keep contrast accessible (WCAG AA). Avoid introducing new palettes per module.
If using Tailwind, use the centralized button classes defined in `resources/css/app.css` (@layer components):

- `.btn` base + one color variant
- Filled variants: `.btn-primary` (cyan), `.btn-success` (green), `.btn-info` (blue), `.btn-dark` (slate), `.btn-danger` (red)
- Outline/neutral: `.btn-outline`
- Icon-only actions: `.btn-icon`

Example toolbar usage:

```vue
<div class="flex flex-wrap gap-2">
  <Link :href="route('admin.module.create')" class="btn btn-primary">+ Create</Link>
  <button @click="exportCsv" class="btn btn-success"><Download class="h-4 w-4"/> CSV</button>
  <button @click="printCurrent" class="btn btn-dark"><Printer class="h-4 w-4"/> Print Current</button>
  <button @click="printAll" class="btn btn-info"><Printer class="h-4 w-4"/> Print All</button>
</div>
```

Example icon actions in tables:

```vue
<div class="inline-flex items-center justify-end space-x-2">
  <Link :href="route('admin.module.show', row.id)" class="btn-icon text-indigo-600" title="View"><Eye class="w-4 h-4"/></Link>
  <Link :href="route('admin.module.edit', row.id)" class="btn-icon text-blue-600" title="Edit"><Edit3 class="w-4 h-4"/></Link>
  <button @click="destroy(row.id)" class="btn-icon text-red-600 hover:text-red-800" title="Delete"><Trash2 class="w-4 h-4"/></button>
</div>
```

Palette mapping (do not override per module):

- Create/Save/Schedule → `.btn-primary`
- Export CSV → `.btn-success`
- Print Current → `.btn-dark`
- Print All → `.btn-info`
- Delete → `.btn-danger`

Centralization policy: Do not hardcode button colors per page. Always use these classes so changes apply globally.

6) Export/Print UI Contract
Actions must map to centralized backend:
Export CSV → csv
// Export PDF removed by policy; use Print All / Print Current via browser and universal templates
Print Current → current_page
Print All → all_records
Print Single (from show/detail) → single_record
Config-driven: No hardcoded columns in Vue. Use the same columns/labels as 
ExportConfig
 where feasible.
File names: Respect filename_prefix (no “filename” key).
View: Always pdf-layout in backend.
7) PDF/Print Layouts
Universal views:
- resources/views/universal-report.blade.php (lists/tabular reports)
- resources/views/universal-single-record.blade.php (single record details)
Special case retained: resources/views/insurance_claim_single.blade.php
Component: Use x-printable-report for consistent headers/tables.
Branding: Consistent header (logo/title), footer (page number/date), table styling.
Orientation: Controlled by ExportConfig (portrait/landscape).
Index column: Shown when include_index is true.
Backend: Centralized via ExportableTrait using the universal templates.
UI agent only ensures action links exist; layout comes from server-rendered PDF template.

8) UX Behaviors
Loading states: Show spinners on table load and form submit.
Confirmations: Use modal for destructive actions (Delete).
Toasts/alerts: Success and error feedback after actions.
Keyboard and a11y: Focus management on modals; button labels, alt text.
9) Staff module specifics
Preload: departments and positions via config('hr.departments') and config('hr.positions') in `StaffController@create` and `@edit`.
Forms: Position uses a dropdown bound to `positions`; `hourly_rate` uses `v-model.number` to ensure numeric values.
Index: Includes leading “#” index column; columns mirror `ExportConfig::getStaffConfig()`.
Search: Placeholder "Search by name, email, position...".
Exports/Print: Buttons route to centralized handlers (CSV/PDF/print current/all/single).
10) Minimal checklist to implement a module UI
Buttons: Add [Entity], Export CSV, Print Current, Print All.
Show footer (standard): Back to List (btn btn-outline), Edit (btn btn-primary), Print Current (btn btn-dark). No Delete.
Forms: Save/Update (btn btn-primary), Cancel (btn btn-outline). Delete only on Edit, right-aligned, with confirmation.
Search + sort + pagination (default per-page 5).
Table columns consistent with module config.
Create/Edit forms with inline validation + Save/Cancel.
Print/Export buttons linked to backend centralized handlers.
Preload only necessary lists for forms.