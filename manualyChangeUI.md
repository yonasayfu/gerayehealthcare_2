# Manual UI Update Guide — apply Patients "Liquid Glass" to other modules

Purpose
- Step-by-step instructions and copy/paste snippets to replicate the Patients UI changes (liquid glass header, glass buttons, search, dark-friendly selects, print behavior) in other modules.
- Only update templates and CSS. Do NOT change business logic or scripts.

Prerequisites
- Confirm project uses: resources/css/app.css and Vite/Tailwind build.
- Have git available to create quick backups/restore.

Quick checklist (per module)
1. Backup files (templates & module CSS).
2. Add global CSS once (app.css).
3. Replace header toolbar template with liquid-glass wrapper.
4. Replace footer action bar buttons with `.btn-glass` (right-aligned).
5. Update form select controls to dark-friendly classes.
6. Convert existing search input to use the glass wrapper (preserve original size & rounding).
7. Hide interactive elements during print (`print:hidden`) and add print-only header/footer where needed.
8. Test in light/dark/print and revert if necessary.

1) Add global CSS (once)
- File: `resources/css/app.css`
- Add or merge the `.liquidGlass-wrapper`, `.btn-glass`, `.search-glass` rules from the Patients module.
- Minimal example to paste:

```css
/* add to resources/css/app.css */
.liquidGlass-wrapper{
  position:relative; overflow:hidden; border-radius:1rem; padding:.75rem;
  -webkit-backdrop-filter: blur(12px) saturate(150%); backdrop-filter: blur(12px) saturate(150%);
  background: linear-gradient(180deg, rgba(255,255,255,0.75), rgba(255,255,255,0.35));
  border: 1px solid rgba(255,255,255,0.65);
  box-shadow: 0 12px 30px rgba(2,6,23,0.08), inset 0 6px 18px rgba(255,255,255,0.45);
}
.dark .liquidGlass-wrapper{ background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01)); }

.btn-glass{
  display:inline-flex; align-items:center; gap:.625rem; padding:.625rem 1rem;
  border-radius:.5rem; font-weight:600; -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px);
  background: linear-gradient(180deg, rgba(255,255,255,.65), rgba(255,255,255,.45));
  border: 1px solid rgba(15,23,42,0.06); transition:transform .12s, box-shadow .12s;
}
.btn-glass-sm{ padding:.4rem .6rem; font-size:.85rem; }
.search-glass{ position:relative; border-radius:.5rem; }
.search-glass::before{ content:""; position:absolute; inset:-6px; border-radius:inherit;
  background: linear-gradient(120deg, rgba(255,255,255,0.28), rgba(255,255,255,0)); filter:blur(8px); z-index:0; }
.search-glass:hover::before, .search-glass:focus-within::before{ transform: translateY(-4px) scale(1.02); }
```

2) Header toolbar replacement (template only)
- Replace current header block with liquid glass wrapper. Keep text/props the same.
- Snippet (Vue template):

```vue
<!-- header: replace existing header block -->
<div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
  <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
  <div class="liquidGlass-content flex items-center justify-between p-6">
    <div class="print:hidden">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Module Title</h1>
      <p class="text-sm text-gray-600 dark:text-gray-300">Optional subtitle</p>
    </div>

    <!-- actions (hidden when printing) -->
    <div class="flex items-center gap-2 print:hidden">
      <a :href="route('admin.module.create')" class="btn-glass">Add Item</a>
      <button @click="exportData('csv')" class="btn-glass btn-glass-sm">Export CSV</button>
      <button @click="printCurrentView" class="btn-glass btn-glass-sm">Print Current</button>
    </div>
  </div>
</div>
```

3) Footer actions (keep single action bar)
- Right-align and use `.btn-glass` to match Patients:

```vue
<!-- footer actions: place near end of page container -->
<div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
  <div class="flex justify-end gap-2">
    <Link :href="route('admin.module.index')" class="btn-glass btn-glass-sm">Back to List</Link>
    <button @click="submit" class="btn-glass btn-glass-sm">Save</button>
    <Link :href="route('admin.module.edit', item.id)" class="btn-glass btn-glass-sm">Edit</Link>
  </div>
</div>
```

4) Form selects — dark-friendly classes (replace select classes only)
- Snippet for selects:

```vue
<label class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Employer</label>
<select v-model="form.corporate_client_id"
  class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
  <option value="">Select</option>
  <option v-for="o in options" :key="o.id" :value="o.id">{{ o.name ?? o.organization_name ?? '—' }}</option>
</select>
```

5) Search input — preserve original size & rounded corners
- Wrap your existing input with `.search-glass` to keep size/curve but add hover/liquid effect:

```vue
<div class="search-glass relative w-full md:w-1/3">
  <input v-model="search" type="text" placeholder="Search..." class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:text-gray-100" />
  <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400" />
</div>
```

6) Table action column — compact glass buttons
- Use `.btn-glass-sm` and keep icons + labels on desktop if space allows:

```vue
<td class="whitespace-nowrap text-right print:hidden">
  <Link :href="route('admin.module.show', row.id)" class="btn-glass btn-glass-sm">View</Link>
  <Link :href="route('admin.module.edit', row.id)" class="btn-glass btn-glass-sm">Edit</Link>
  <button @click="destroy(row.id)" class="btn-glass btn-glass-sm">Delete</button>
</td>
```

7) Print behavior
- Hide interactive controls: add `print:hidden` to action containers (headers/footers/action columns).
- Add print-only header/footer if needed:

```vue
<!-- print-only header -->
<div class="hidden print:block text-center">
  <img src="/images/geraye_logo.jpeg" alt="logo" class="mx-auto mb-2" style="height:36px" />
  <div class="font-semibold">Organization Name</div>
  <div>Printed: {{ format(new Date(), 'PPP p') }}</div>
</div>
```

8) Reusable components (optional but recommended)
- Create `resources/js/components/GlassCard.vue` and `GlassButton.vue` to avoid repeating markup.
- Example (simplified):

```vue
<!-- resources/js/components/GlassButton.vue -->
<template>
  <button :class="['btn-glass', size === 'sm' ? 'btn-glass-sm' : '']"><slot /></button>
</template>
<script setup> defineProps({ size: { type: String, default: '' } }) </script>
```


10) Reverting & backups
- Use git for rollback:
  - git add .
  - git commit -m "ui: backup before glass updates"
  - If needed: git checkout -- path/to/file.vue
- Or copy files to a tmp backup directory before making changes.

11) New module scaffold (quick mapping)
- For a new CRUD module replicate structure used in Patients:
  - Pages: resources/js/pages/Admin/Module/{Index.vue,Create.vue,Edit.vue,Show.vue,Form.vue}
  - Apply header/footer snippets, form select snippet, search wrapper, and action buttons.
  - Import shared Glass components if created.

Notes / Do not change
- Avoid modifying scripts, props, service calls, or DTO usage while updating UI.
- Keep all changes in `<template>` and global CSS files unless intentionally adding components.

## 🚨 **CRITICAL: Show.vue JavaScript Error Fixes**

**URGENT:** Many Show.vue files have JavaScript errors that must be fixed:
```
Uncaught (in promise) TypeError: Cannot read properties of undefined (reading 'name')
```

### **Quick Fix Pattern (Apply to ALL Show.vue files):**

**Error 1: Wrong Variable References**
```vue
❌ WRONG: <p>Entity: {{ item.name || item.title || item.id }}</p>
✅ CORRECT: <p>Entity: {{ actualEntity.actualProperty }}</p>
```

**Error 2: Non-existent Functions**  
```vue
❌ WRONG: @click="printSingleSomething"
✅ CORRECT: @click="printPage"
```

**Error 3: Wrong Route Parameters**
```vue
❌ WRONG: route('admin.module.edit', item.id)
✅ CORRECT: route('admin.module.edit', actualEntity.id)
```

### **Files Already Fixed (8/33):**
- ✅ MarketingCampaigns/Show.vue
- ✅ EligibilityCriteria/Show.vue  
- ✅ LandingPages/Show.vue
- ✅ EventRecommendations/Show.vue
- ✅ MarketingLeads/Show.vue
- ✅ Partners/Show.vue
- ✅ LeadSources/Show.vue
- ✅ VisitServices/Show.vue

### **Remaining Files (~25) - Need Same Fixes:**
```
CampaignContents/Show.vue
EventBroadcasts/Show.vue
EventParticipants/Show.vue
EventStaffAssignments/Show.vue
InventoryMaintenanceRecords/Show.vue
InventoryRequests/Show.vue
InventoryTransactions/Show.vue
Invoices/Show.vue
MarketingPlatforms/Show.vue
MarketingTasks/Show.vue
PartnerAgreements/Show.vue
PartnerCommissions/Show.vue
PartnerEngagements/Show.vue
Referrals/Show.vue
ReferralDocuments/Show.vue
TaskDelegations/Show.vue
Users/Show.vue
+ others...
```

### **Step-by-Step Fix Instructions:**
1. **Open the Show.vue file**
2. **Find `defineProps<{...}>()` to identify correct variable name**
3. **Replace the 3 error patterns** with correct variable references
4. **Test the page** - no console errors should appear
5. **Move to next file**

**📄 See: `SHOW_VUE_FIXES_SUMMARY.md` for complete details**

---

If you want, I can:
- generate a one-click patch set for one example module (Index/Create/Edit/Show/Form) to demonstrate the full process, or
- scaffold `GlassCard.vue` and `GlassButton.vue` and replace Patients markup to use them (then you can
