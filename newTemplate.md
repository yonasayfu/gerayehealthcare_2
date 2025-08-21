# Liquid Glass / Theme Update — Implementation Guide

## Purpose
- Standardize the "liquid glass" UI (buttons, header cards, print behavior, dark-mode aware selects) across CRUD pages.
- Provide step-by-step snippets so other modules or AI agents can apply changes consistently without touching business logic.

## Prerequisites
- Project built with Tailwind + Vite + Inertia + Vue 3.
- Confirm CSS entry file: `resources/css/app.css` (or similar) is loaded by Vite.
- Confirm components: `AppLayout.vue`, `Form.vue` (shared patient form) exist.

## High-level steps (repeatable pattern)
1. Add global CSS utilities (btn-glass, liquid glass).
2. Update the page header/toolbar: wrap with `.liquidGlass-wrapper`, add `.liquidGlass-inner-shine` and `.liquidGlass-content`.
3. Use `.btn-glass` / `.btn-glass-sm` for toolbar buttons (label + optional icon).
4. Ensure print behavior: hide interactive elements using `print:hidden` and add `print`-only footer.
5. For form selects, add dark-friendly classes and robust option labels.
6. Test in light & dark mode and print preview.

### A. Global CSS (add once)
- **File**: resources/css/app.css
- Add the liquid glass + btn-glass rules (pick one of the existing variants). Example:

```css
/* Liquid glass wrapper */
.liquidGlass-wrapper{
  position:relative; overflow:hidden; border-radius:1rem; padding:.75rem;
  -webkit-backdrop-filter: blur(12px) saturate(150%); backdrop-filter: blur(12px) saturate(150%);
  background: linear-gradient(180deg, rgba(255,255,255,0.75), rgba(255,255,255,0.35));
  border: 1px solid rgba(255,255,255,0.65);
  box-shadow: 0 12px 30px rgba(2,6,23,0.08), inset 0 6px 18px rgba(255,255,255,0.45);
  transition: transform .18s ease, box-shadow .18s ease;
}
.dark .liquidGlass-wrapper{
  background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
  border: 1px solid rgba(255,255,255,0.06);
  box-shadow: 0 12px 30px rgba(2,6,23,0.45), inset 0 6px 18px rgba(255,255,255,0.02);
}
.liquidGlass-wrapper::before{ /* highlight layer */
  content:""; position:absolute; inset:0; pointer-events:none; border-radius:inherit;
  background: linear-gradient(120deg, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0.06) 35%, rgba(255,255,255,0) 100%);
  mix-blend-mode: screen; filter: blur(8px); transform: translateX(-30%) rotate(-6deg); z-index:0;
}
.liquidGlass-inner-shine{ position:absolute; top:-25%; left:-10%; width:140%; height:80%; pointer-events:none;
  background: radial-gradient(600px 160px at 20% 10%, rgba(255,255,255,0.45), rgba(255,255,255,0.02) 35%, transparent 60%); z-index:1;
}
.liquidGlass-content{ position:relative; z-index:2; }

/* Glass button */
.btn-glass{
  display:inline-flex; align-items:center; gap:.5rem; padding:.5rem .75rem; border-radius:.5rem;
  font-weight:600; cursor:pointer; position:relative; overflow:hidden;
  -webkit-backdrop-filter: blur(8px) saturate(140%); backdrop-filter: blur(8px) saturate(140%);
  background: linear-gradient(180deg, rgba(255,255,255,.65), rgba(255,255,255,.45));
  color:#0f172a; border:1px solid rgba(15,23,42,0.06);
  box-shadow:0 6px 18px rgba(2,6,23,0.08), inset 0 2px 6px rgba(255,255,255,0.5);
  transition: transform .12s, box-shadow .12s;
}
.btn-glass::after{ content:""; position:absolute; inset:0; pointer-events:none;
  background: linear-gradient(120deg, rgba(255,255,255,0.22), rgba(255,255,255,0.06)); mix-blend-mode:screen; filter:blur(6px);
}
.btn-glass:hover{ transform:translateY(-2px); }
.btn-glass-sm{ padding:.35rem .6rem; font-size:.8125rem; gap:.375rem; }

/* Dark variants */
.dark .btn-glass{ background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01)); color:#f8fafc; border:1px solid rgba(255,255,255,0.06); }
```
````markdown
<!-- filepath: [Index.vue](http://_vscodecontentref_/1) -->
<div class="liquidGlass-wrapper print:hidden">
  <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
  <div class="liquidGlass-content flex items-center justify-between">
    <div class="print:hidden">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Patients</h1>
      <p class="text-sm text-gray-600 dark:text-gray-300">Manage patient records</p>
    </div>

    <div class="flex items-center gap-2 print:hidden">
      <a :href="route('admin.patients.create')" class="btn-glass">Add Patient</a>
      <button @click="exportData('csv')" class="btn-glass btn-glass-sm">Export CSV</button>
      <button @click="printCurrentView" class="btn-glass btn-glass-sm">Print Current</button>
      <button @click="printAllPatients" class="btn-glass btn-glass-sm">Print All</button>
    </div>
  </div>
</div>

<!-- Print footer (visible only when printing) -->
<div class="hidden print:block mt-6 text-center text-sm text-gray-700 dark:text-gray-300">
  <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="mx-auto mb-2 print-logo" style="height:36px" />
  <div class="font-semibold">Geraye Home Care Services</div>
  <div class="mt-1">Printed: {{ currentDate }}</div>
</div>
````