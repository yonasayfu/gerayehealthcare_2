# UI Template — Dark / Light theme guidance

Purpose
- Single reference for implementing theme-safe UI for existing modules and new CRUD modules.
- Use this as the checklist and pattern whenever you create/update pages/components so dark mode remains readable and consistent.

Principles
- Prefer semantic tokens (CSS variables) over hard-coded color classes.
- Use Tailwind `dark:` utilities and CSS variables together.
- Ensure charts, icons and images get theme-aware colors (either via CSS variables or runtime reconfiguration).
- Update shared primitives first (buttons, inputs, dropdowns, tables, cards, modals). Then update page-level CRUD views.

Files to update (global)
1. resources/css/app.css
   - Add CSS variable palette for light and dark themes.
   - Ensure `.theme-dark` / `.dark` variables set.
2. resources/js/composables/useAppearance.ts
   - Ensure theme toggling reliably adds/removes `dark` (and `theme-dark`) classes on documentElement; expose a re-runable color update.
3. resources/views/app.blade.php
   - Ensure initial theme class is emitted server-side to avoid FOUC (flash of wrong theme).
4. src/shared components (location: resources/js/components or similar)
   - StatCard.vue
   - Button.vue
   - Dropdown.vue
   - Table.vue / DataTable.vue
   - Modal.vue / Dialog.vue
   - Input/Textarea/Select components
   - Toast/Notification components
   - Layouts/AppLayout.vue and Sidebar/Nav components

Common module pages to update per CRUD (example module path)
- resources/js/pages/Admin/<ModuleName>/Index.vue
- resources/js/pages/Admin/<ModuleName>/Create.vue
- resources/js/pages/Admin/<ModuleName>/Edit.vue
- resources/js/pages/Admin/<ModuleName>/Show.vue

Checklist to apply per CRUD module (do one module at a time)
1. Update shared primitives (only once)
   - Buttons: use classes like `btn bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100` (or use variables).
   - Dropdowns / selects: invert background/text in dark mode; ensure options text uses `dark:text-...`.
   - Table rows / headers: header `bg-gray-100 dark:bg-gray-700` and cell text `text-gray-800 dark:text-gray-200`.
   - Cards: `bg-white dark:bg-gray-800` and `text-gray-900 dark:text-gray-100`.
   - Inputs: border and placeholder colors: `border-gray-300 dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400`.
   - Modals: overlay + content background with dark variants.
2. Update page-level views
   - Index.vue: table headings, action buttons, badges, pagination controls.
   - Show.vue: label / value pairs — labels should be `text-muted` and values `text-gray-800 dark:text-gray-200`.
   - Create/Edit: form labels, placeholders, required/validation messages (`text-red-600 dark:text-red-400`), submit/cancel buttons.
   - Ensure back/print/edit buttons have sufficient contrast in dark mode.
3. Charts & Canvas
   - Use CSS variables or a themeColors ref to configure Chart.js/Apex on mount and when theme toggles.
   - Example: set legend/tick/toolip colors to `getComputedStyle(document.documentElement).getPropertyValue('--fg')` (or a runtime ref).
4. Images / Icons
   - Avoid images with transparent text. Provide alternative (SVG with currentColor) or add CSS filters for dark mode if necessary.
5. Accessibility check
   - Minimum contrast for body text: WCAG AA ~ 4.5:1 for normal text. Prefer `text-gray-200` over `text-gray-400` for small text on dark backgrounds.

Key snippets

1) CSS variables (resources/css/app.css)
```css
/* filepath: c:\MyProject\gerayehealthcare_2\resources\css\app.css */
/* ...existing code... */
:root{
  --bg: #ffffff;
  --card-bg: #ffffff;
  --fg: #1f2937; /* gray-800 */
  --muted: #6b7280; /* gray-500 */
  --border: #e5e7eb; /* gray-200 */
  --ring: rgba(59,130,246,0.3);
}

.dark, .theme-dark {
  --bg: #0b1220; /* near-black bg */
  --card-bg: #0f1724; /* slightly lighter */
  --fg: #e5e7eb; /* gray-200 */
  --muted: #9ca3af; /* gray-400 */
  --border: #374151; /* gray-700 */
  --ring: rgba(99,102,241,0.18);
}

/* Use variables in utility classes if desired */
.bg-app { background-color: var(--bg); }
.text-fg { color: var(--fg); }
.bg-card { background-color: var(--card-bg); }
/* ...existing code... */
```

2) useAppearance guidance (resources/js/composables/useAppearance.ts)
```ts
// filepath: c:\MyProject\gerayehealthcare_2\resources\js\composables\useAppearance.ts
// ...existing code...
export function setTheme(isDark: boolean) {
  const el = document.documentElement;
  if (isDark) {
    el.classList.add('dark','theme-dark');
  } else {
    el.classList.remove('dark','theme-dark');
  }
  // trigger a global event or call to update chart colors if needed
  window.dispatchEvent(new CustomEvent('theme:changed'));
}
```

3) Chart.js runtime theme update (example)
```ts
// Inside a page component setup():
const applyChartTheme = () => {
  const isDark = document.documentElement.classList.contains('dark');
  const textColor = isDark ? '#E5E7EB' : '#374151';
  // update options object or rerender chart
};

onMounted(() => {
  applyChartTheme();
  window.addEventListener('theme:changed', applyChartTheme);
});
onUnmounted(() => {
  window.removeEventListener('theme:changed', applyChartTheme);
});
```

Button / action examples
- Back / Edit / Print buttons:
  - Use: `class="inline-flex items-center px-3 py-1.5 rounded text-sm font-medium bg-white text-gray-900 border border-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"`
  - For danger / primary use consistent dark variants: `bg-indigo-600 text-white dark:bg-indigo-500`

Example mapping for CRUD fields (labels/values)
- Label: `text-sm text-muted-foreground dark:text-gray-300`
- Value: `text-sm text-gray-800 dark:text-gray-100`
- Field container: `bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3`

Testing & QA (manual)
1. Start dev server: `npm run dev` and `php artisan serve` (Windows).
2. Open dashboard and toggle theme (app toggle or manually add/remove `dark` on <html>).
3. Visit CRUD pages for the module:
   - Index: check table header, rows, action buttons, pagination.
   - Create/Edit: check inputs, dropdowns, validation colors.
   - Show: check labels/values and action buttons.
4. Charts: verify axes, legends and tooltips switch color.
5. Images: ensure logos/icons visible on dark bg; if not, provide alternate SVG or add `filter: invert(1) hue-rotate(...)` as last resort.

Workflow for module-by-module updates
1. Update shared primitives (one time).
2. Pick a module (suggestion order): Users, Patients, Inventory, Appointments.
3. Update Index -> Create -> Edit -> Show in that order.
4. Run smoke tests and ask for confirmation before next module.

Commit / PR message suggestion
- "chore(ui): add theme token guidance and update dashboard chart/text to support dark mode"
- For each module change: "fix(ui): make <ModuleName> CRUD dark-mode friendly"

If you confirm, I will:
- Create/modify UI_Template.md with the content above (done here).
- Then start the next module you prefer (suggest Users module). Which module should I update first?