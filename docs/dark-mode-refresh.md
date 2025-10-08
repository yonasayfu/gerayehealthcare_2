# Dark Mode Refresh Audit

Updates delivered in this pass bring the admin and staff surfaces in line with the new dark theme tokens and the reusable surface styles.

## Global
- `resources/css/app.css`
  - Added dark-mode remapping for common Tailwind neutrals (backgrounds, borders, grays).
  - Normalised input/background handling and the glass button styling.
  - Introduced `surface-panel`, `surface-muted`, and `surface-subtle` utility classes for consistent cards.

## Screens Given Targeted Passes
- `resources/js/pages/Staff/TaskDelegations/Index.vue`
- `resources/js/pages/Staff/MyToDo/Index.vue`
- `resources/js/pages/Admin/CaregiverAssignments/Show.vue`
- `resources/js/pages/Admin/VisitServices/Show.vue`
- `resources/js/pages/Admin/Prescriptions/Create.vue`
- `resources/js/pages/Admin/Staff/Show.vue`
- `resources/js/pages/Admin/StaffAvailabilities/Index.vue`

These views now rely on the new surface utilities so their cards, forms, and summaries respond to dark mode without washed-out or “blackout” sections.

## Reusable UI
- `resources/js/components/PerPageSelect.vue`
  - New component to standardise per-page dropdown styling in both themes.
- `resources/js/app.ts`
  - Registered the `PerPageSelect` component globally.

## Supporting Utilities
- `resources/js/composables/useTableFilters.ts`
  - Default page size now 5 to match the select options.

## Follow-up
- Remaining CRUD pages inherit the new dark-mode palette via the global adjustments. If any screen still looks off, wrap its primary container in `surface-panel` or `surface-muted`.
- Run `npm run lint` before committing.
