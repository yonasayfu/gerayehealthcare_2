# Print Component Migration Guide

## 1. Overview

This guide explains how to migrate from module-specific print components to the centralized `PrintableReport.vue` component.

## 2. Current Print Approaches

There are currently two main approaches to print functionality:

### 2.1 Vue Component Approach
- Module-specific Vue components (e.g., `PrintAll.vue`, `PrintSingle.vue`)
- Auto-trigger print on component load
- Import shared `PrintGeneral.vue` components
- Use HTML/CSS for layout

### 2.2 Backend PDF Approach
- Routes call controller methods that use `ExportableTrait`
- Generate PDFs using `pdf-layout.blade.php`
- More consistent but requires PDF rendering

## 3. Migration to PrintableReport.vue

### 3.1 Benefits
- Single, reusable component
- Consistent styling through `print.css`
- Better maintainability
- Type safety with TypeScript

### 3.2 Migration Process

#### Step 1: Identify Module to Migrate
Choose a module with print components to migrate (start with simpler ones).

#### Step 2: Analyze Current Print Component
Examine the current print component to understand:
- Data structure being passed
- Column definitions
- Header/footer requirements

#### Step 3: Create PrintableReport Configuration
Replace the current component with PrintableReport.vue:

```vue
<script setup>
import PrintableReport from '@/components/PrintableReport.vue'

// Example for InventoryItems
const columns = [
  { key: 'index', label: '#' },
  { key: 'name', label: 'Name' },
  { key: 'item_category', label: 'Category' },
  { key: 'item_type', label: 'Type' },
  { key: 'serial_number', label: 'Serial Number' },
  { key: 'status', label: 'Status' },
  { key: 'purchase_date', label: 'Purchase Date', 
    format: (value) => value ? new Date(value).toLocaleDateString() : '-' 
  },
  { key: 'warranty_expiry', label: 'Warranty Expiry',
    format: (value) => value ? new Date(value).toLocaleDateString() : '-' 
  }
]

const props = defineProps({
  inventoryItems: Array,
  title: {
    type: String,
    default: 'Inventory Items'
  }
})
</script>

<template>
  <PrintableReport
    :title="title"
    :data="inventoryItems"
    :columns="columns"
    :header-info="{
      logoSrc: '/images/geraye_logo.jpeg',
      clinicName: 'Geraye Home Care Services',
      documentTitle: title
    }"
    :footer-info="{
      generatedDate: true
    }"
  />
</template>
```

#### Step 4: Update Routes (if needed)
If the module uses Vue print components, update the route to point to the index page with a print parameter instead of a separate print route.

#### Step 5: Update Controller (if needed)
Modify the controller to pass data in the format expected by PrintableReport.vue.

#### Step 6: Test Print Functionality
Ensure the print output matches the previous version in:
- Layout and styling
- Data accuracy
- Header/footer information
- Page breaks

## 4. Example Migration: Inventory Items

### 4.1 Before Migration
Current components:
- `resources/js/pages/Admin/InventoryItems/PrintAll.vue`
- `resources/js/pages/Admin/InventoryItems/PrintSingle.vue`
- `resources/js/pages/Admin/InventoryItems/PrintGeneral.vue`

### 4.2 After Migration
Replace with usage of PrintableReport.vue in the Index.vue or Show.vue components:

```vue
<!-- In Index.vue -->
<script setup>
import { ref } from 'vue'
import PrintableReport from '@/components/PrintableReport.vue'

const showPrintDialog = ref(false)
const printData = ref([])

const printAllItems = () => {
  // Prepare data for printing
  printData.value = inventoryItems.value.data
  showPrintDialog.value = true
}
</script>

<template>
  <!-- Add print button -->
  <button @click="printAllItems" class="btn btn-info">
    Print All
  </button>
  
  <!-- Print dialog -->
  <teleport to="body">
    <div v-if="showPrintDialog" class="print-dialog">
      <PrintableReport
        title="All Inventory Items"
        :data="printData"
        :columns="columns"
        @close="showPrintDialog = false"
      />
    </div>
  </teleport>
</template>
```

## 5. Best Practices

### 5.1 Column Definitions
- Use consistent key names that match your data structure
- Implement formatting functions for dates, currency, etc.
- Specify print widths for better layout control

### 5.2 Data Preparation
- Ensure data is properly formatted before passing to PrintableReport
- Handle null/undefined values gracefully
- Pre-process complex data structures

### 5.3 Styling
- Leverage existing `print.css` styles
- Add module-specific print styles only when necessary
- Test across different browsers and paper sizes

## 6. Testing Checklist

- [ ] Print layout matches previous version
- [ ] All data fields display correctly
- [ ] Header and footer information is accurate
- [ ] Page breaks occur at appropriate places
- [ ] Styling is consistent with other print outputs
- [ ] Print works in Chrome, Firefox, and Safari
- [ ] PDF export functionality still works (if applicable)

## 7. Rollback Plan

If issues are discovered after migration:
1. Revert to the previous print component
2. Document the issue
3. Fix the issue in PrintableReport.vue or the migration
4. Retest with the fixed version