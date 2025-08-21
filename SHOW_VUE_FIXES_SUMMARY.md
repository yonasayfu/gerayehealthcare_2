# Show.vue Files - JavaScript Error Fixes Summary

## 🎯 **CRITICAL ISSUE RESOLVED**

**Problem:** Multiple Show.vue files have JavaScript errors causing:
```
Uncaught (in promise) TypeError: Cannot read properties of undefined (reading 'name')
```

**Root Cause:** Incorrect variable references in Vue templates

---

## ✅ **SOLUTION PATTERN (Apply to ALL Show.vue files)**

### **Error Pattern 1: Undefined Variable Reference**
```vue
❌ WRONG:
<p>{{ item.name || item.title || item.id }}</p>

✅ CORRECT:
<p>{{ actualEntityName.propertyName }}</p>
```

### **Error Pattern 2: Non-existent Function Calls**  
```vue
❌ WRONG:
<button @click="printSingleSomething">Print</button>

✅ CORRECT:
<button @click="printPage">Print</button>
```

### **Error Pattern 3: Wrong Route Parameters**
```vue
❌ WRONG:
<Link :href="route('admin.module.edit', item.id)">Edit</Link>

✅ CORRECT:
<Link :href="route('admin.module.edit', actualEntity.id)">Edit</Link>
```

---

## 🔧 **FILES ALREADY FIXED (8/33 completed)**

| ✅ **Fixed Files** | Variable Fixed | Function Fixed | Route Fixed |
|------------------|----------------|----------------|-------------|
| **MarketingCampaigns/Show.vue** | `item.name` → `marketingCampaign.campaign_name` | `printSingleMarketingCampaign` → `printPage` | `item.id` → `marketingCampaign.id` |
| **EligibilityCriteria/Show.vue** | `item.name` → `eligibilityCriteria.criteria_title` | `printSingleEligibilityCriteria` → `printPage` | `item.id` → `eligibilityCriteria.id` |
| **LandingPages/Show.vue** | `item.name` → `landingPage.page_title` | `printSingleLandingPage` → `printPage` | `item.id` → `landingPage.id` |
| **EventRecommendations/Show.vue** | Already correct | `printSingleEventRecommendation` → `printPage` | `item.id` → `eventRecommendation.id` |
| **MarketingLeads/Show.vue** | `item.name` → `marketingLead.first_name + last_name` | `printSingleMarketingLead` → `printPage` | `item.id` → `marketingLead.id` |
| **Partners/Show.vue** | `item.name` → `partner.name` | `printSinglePartner` → `printPage` | `item.id` → `partner.id` |
| **LeadSources/Show.vue** | `item.name` → `leadSource.name` | `printSingleLeadSource` → `printPage` | `item.id` → `leadSource.id` |
| **VisitServices/Show.vue** | `item.name` → `visitService.id` | `printSingleVisitService` → `printSingleVisit` | `item.id` → `visitService.id` |

---

## 🔄 **REMAINING FILES TO FIX (~25 files)**

Based on grep analysis, these files need the same pattern fixes:

### **High Priority Files:**
```
resources/js/pages/Admin/CampaignContents/Show.vue
resources/js/pages/Admin/EventBroadcasts/Show.vue  
resources/js/pages/Admin/EventParticipants/Show.vue
resources/js/pages/Admin/EventStaffAssignments/Show.vue
resources/js/pages/Admin/InventoryMaintenanceRecords/Show.vue
resources/js/pages/Admin/InventoryRequests/Show.vue
resources/js/pages/Admin/InventoryTransactions/Show.vue
resources/js/pages/Admin/Invoices/Show.vue
resources/js/pages/Admin/MarketingPlatforms/Show.vue
resources/js/pages/Admin/MarketingTasks/Show.vue
resources/js/pages/Admin/PartnerAgreements/Show.vue
resources/js/pages/Admin/PartnerCommissions/Show.vue
resources/js/pages/Admin/PartnerEngagements/Show.vue
resources/js/pages/Admin/Referrals/Show.vue
resources/js/pages/Admin/ReferralDocuments/Show.vue
resources/js/pages/Admin/TaskDelegations/Show.vue
resources/js/pages/Admin/Users/Show.vue
```

---

## 🛠️ **STEP-BY-STEP FIX INSTRUCTIONS**

### **Step 1: Identify the Entity Variable**
Look at the `defineProps` section to find the correct variable name:

```vue
// Example 1:
const props = defineProps<{ marketingCampaign: any }>()
// Use: marketingCampaign.campaign_name

// Example 2:  
const props = defineProps<{ leadSource: LeadSource }>()
// Use: leadSource.name

// Example 3:
const props = defineProps<{ staff: Staff }>()
// Use: staff.first_name + " " + staff.last_name
```

### **Step 2: Fix the Header Display Line**
Find this pattern and replace:
```vue
❌ FIND:
<p class="text-sm text-gray-600 dark:text-gray-300">Entity: {{ item.name || item.title || item.id }}</p>

✅ REPLACE WITH:
<p class="text-sm text-gray-600 dark:text-gray-300">Entity: {{ correctEntity.correctProperty }}</p>
```

### **Step 3: Fix the Print Function**
Find and replace non-existent print functions:
```vue
❌ FIND:
<button @click="printSingleXxxxx" class="btn-glass btn-glass-sm">Print Current</button>

✅ REPLACE WITH:
<button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
```

### **Step 4: Fix the Edit Route**
Find and fix the edit link:
```vue
❌ FIND:
<Link :href="route('admin.module.edit', item.id)" class="btn-glass btn-glass-sm">Edit</Link>

✅ REPLACE WITH:
<Link :href="route('admin.module.edit', correctEntity.id)" class="btn-glass btn-glass-sm">Edit</Link>
```

### **Step 5: Fix HTML Structure Issues**
Look for extra closing tags:
```vue
❌ FIND:
      </div>
    </div>
      </div>  <!-- ← Extra closing div -->

✅ REPLACE WITH:
      </div>
    </div>
```

---

## 📋 **ENTITY VARIABLE MAPPING GUIDE**

| **Module** | **Variable Name** | **Display Property** |
|------------|-------------------|---------------------|
| CampaignContents | `campaignContent` | `campaignContent.title` |
| EventBroadcasts | `eventBroadcast` | `eventBroadcast.title` |
| EventParticipants | `eventParticipant` | `eventParticipant.patient_name` |
| EventStaffAssignments | `eventStaffAssignment` | `eventStaffAssignment.staff_name` |
| InventoryMaintenanceRecords | `inventoryMaintenanceRecord` | `inventoryMaintenanceRecord.item_name` |
| InventoryRequests | `inventoryRequest` | `inventoryRequest.item_name` |
| InventoryTransactions | `inventoryTransaction` | `inventoryTransaction.item_name` |
| Invoices | `invoice` | `invoice.invoice_number` |
| MarketingPlatforms | `marketingPlatform` | `marketingPlatform.name` |
| MarketingTasks | `marketingTask` | `marketingTask.title` |
| PartnerAgreements | `partnerAgreement` | `partnerAgreement.title` |
| PartnerCommissions | `partnerCommission` | `partnerCommission.partner_name` |
| PartnerEngagements | `partnerEngagement` | `partnerEngagement.title` |
| Referrals | `referral` | `referral.patient_name` |
| ReferralDocuments | `referralDocument` | `referralDocument.document_name` |
| TaskDelegations | `taskDelegation` | `taskDelegation.title` |
| Users | `user` | `user.name` |

---

## 🔒 **IMPORTANT: PRESERVE ALL LOGIC**

### **✅ DO NOT CHANGE:**
- Any JavaScript functions in `<script>` section
- Print functionality logic
- Export functionality logic  
- Create/Edit form logic
- Database operations
- Business logic
- Route definitions

### **✅ ONLY CHANGE:**
- Variable references in `{{ }}` template expressions
- Function names in `@click` handlers (if function doesn't exist)
- Route parameters in `:href` attributes
- HTML structure (remove extra closing tags)

---

## 🧪 **TESTING CHECKLIST**

After fixing each file, verify:

1. **✅ No JavaScript Console Errors**
   - Open browser dev tools → Console tab
   - Navigate to the Show page
   - Confirm no "Cannot read properties of undefined" errors

2. **✅ Page Displays Correctly**
   - Header shows correct entity name/title
   - All data fields populate correctly
   - No "[object Object]" displays

3. **✅ Buttons Work**
   - Print button works (opens print dialog)
   - Edit button navigates to correct edit page
   - Back button returns to index

4. **✅ Preserved Functionality**
   - All original features still work
   - No broken functionality

---

## 🚀 **AUTOMATION COMMANDS**

To find remaining files that need fixes:

```bash
# Find files with the problematic pattern
grep -r "item\.name.*item\.title.*item\.id" resources/js/pages/Admin/*/Show.vue

# Find files with non-existent print functions  
grep -r "printSingle[A-Z]" resources/js/pages/Admin/*/Show.vue

# Find files with wrong route parameters
grep -r "item\.id.*btn-glass.*Edit" resources/js/pages/Admin/*/Show.vue
```

---

## 📝 **EXAMPLE FIX (Complete)**

**Before (Broken):**
```vue
<script setup lang="ts">
const props = defineProps<{ leadSource: LeadSource }>()
// ... other code
</script>

<template>
  <!-- Header with wrong variable -->
  <p class="text-sm text-gray-600 dark:text-gray-300">Lead Source: {{ item.name || item.title || item.id }}</p>
  
  <!-- Wrong function call -->
  <button @click="printSingleLeadSource" class="btn-glass btn-glass-sm">Print Current</button>
  
  <!-- Wrong route parameter -->
  <Link :href="route('admin.lead-sources.edit', item.id)" class="btn-glass btn-glass-sm">Edit</Link>
</template>
```

**After (Fixed):**
```vue
<script setup lang="ts">
const props = defineProps<{ leadSource: LeadSource }>()
// ... other code (UNCHANGED)
</script>

<template>
  <!-- Header with correct variable -->
  <p class="text-sm text-gray-600 dark:text-gray-300">Lead Source: {{ leadSource.name }}</p>
  
  <!-- Correct function call -->
  <button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
  
  <!-- Correct route parameter -->
  <Link :href="route('admin.lead-sources.edit', leadSource.id)" class="btn-glass btn-glass-sm">Edit</Link>
</template>
```

---

## ⚡ **QUICK COPY-PASTE FIXES**

For each remaining Show.vue file:

1. **Find the props definition** to identify the correct variable name
2. **Use these search-and-replace patterns** (adjust variable names):

```javascript
// Pattern 1: Fix header display
FIND: {{ item.name || item.title || item.id }}
REPLACE: {{ correctVariableName.correctProperty }}

// Pattern 2: Fix print function  
FIND: @click="printSingle
REPLACE: @click="printPage

// Pattern 3: Fix edit route
FIND: route('admin.module.edit', item.id)
REPLACE: route('admin.module.edit', correctVariableName.id)
```

---

## 📞 **AI AGENT INSTRUCTIONS**

If you're an AI agent helping with these fixes:

1. **Read the file first** to identify the `defineProps` variable name
2. **Apply only the 3 pattern fixes** shown above
3. **Do NOT modify any JavaScript logic** 
4. **Test the page** after each fix to ensure no errors
5. **Move to the next file** only after confirming the current one works

**The goal is to eliminate all "Cannot read properties of undefined" JavaScript errors while preserving 100% of the existing functionality.**
