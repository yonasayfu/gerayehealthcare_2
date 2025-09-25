<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3 } from 'lucide-vue-next' // Import icons
import type { BreadcrumbItemType } from '@/types' // Assuming you have this type defined
import { format } from 'date-fns' // For date formatting
import ShowHeader from '@/components/ShowHeader.vue'


const props = defineProps<{
  inventoryTransaction: any; // Ideally, define a more specific type for inventory transaction data
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Transactions', href: route('admin.inventory-transactions.index') },
  { title: props.inventoryTransaction.id, href: route('admin.inventory-transactions.show', props.inventoryTransaction.id) },
]

function printPage() {
  // Add a small delay to ensure the DOM is ready for printing.
  // This can sometimes resolve issues where the print dialog doesn't appear
  // or content is not rendered correctly.
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      // Optionally, provide user feedback if print fails
      alert('Failed to open print dialog. Please check your browser settings or try again.');
    }
  }, 100); // 100ms delay
}

// Delete action removed per UI policy (no Delete on Show pages)
</script>

<template>
  <Head :title="`Inventory Transaction: ${inventoryTransaction.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Inventory Transaction Details" :subtitle="`Inventory Transaction: ${inventoryTransaction.item?.name || inventoryTransaction.id}`">
        <template #actions>
          <Link :href="route('admin.inventory-transactions.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

        <div class="p-6 space-y-6">
            <div class="print-document bg-card text-card-foreground shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Inventory Transaction Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Transaction Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Item:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.item?.name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Transaction Type:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.transaction_type ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Quantity:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.quantity ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">From Location:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.from_location ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">To Location:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.to_location ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Performed By:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.performed_by?.first_name }} {{ inventoryTransaction.performed_by?.last_name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Notes:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.notes ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Administrative Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Created At:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.created_at ? format(new Date(inventoryTransaction.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Updated At:</p>
                      <p class="font-medium text-foreground">{{ inventoryTransaction.updated_at ? format(new Date(inventoryTransaction.updated_at), 'PPP p') : '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print:text-xs">
                    <hr class="my-2 border-gray-300">
                    <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
                    </div>

            </div>
        </div>

              <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          
          <button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.inventory-transactions.edit', inventoryTransaction.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>

    </div>

  </AppLayout>
</template>

<style>
/* Optimized Print Styles for A4 */
@media print {
  @page {
    size: A4;
    margin: 1cm;
  }

  /* Universal print adjustments */
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
  }

  /* Elements to hide during print */
  .print\:hidden {
    display: none !important;
  }
  
  /* HIDE BREADCRUMBS AND TOP NAV (from AppSidebarLayout.vue) */
  .app-sidebar-header, .app-sidebar {
    display: none !important;
  }
  
  /* Fallback/more general selectors if the above doesn't catch it all */
  body > header,
  body > nav,
  [role="banner"],
  [role="navigation"] {
    display: none !important;
  }

  /* Elements to show only during print */
  .hidden.print\:block {
    display: block !important;
  }

  /* Specific styles for the print header content (logo and clinic name) */
  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
    text-align: center;
  }

  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .print-clinic-name {
    font-size: 1.8rem !important;
    margin-bottom: 0.2rem !important;
    line-height: 1.2 !important;
  }

  .print-document-title {
    font-size: 0.9rem !important;
    color: #555 !important;
  }

  /* Target the main document container for proper centering and layout */
  .print-document {
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    padding: 1cm !important;
    margin: 0 auto !important;
    width: 100% !important;
    max-width: 210mm !important; /* A4 width */
    height: auto !important;
    overflow: visible !important;
  }

  /* Reduce overall top-level padding/margin if the wrapper div adds too much */
  .p-6.space-y-6 {
    padding: 0 !important;
    margin: 0 !important;
  }
  
  /* Adjust spacing within sections for print */
  .space-y-8 > div:not(:first-child) {
    margin-top: 0.8rem !important;
    margin-bottom: 0.8rem !important;
  }
  
  .space-y-6 > div:not(:first-child) {
    margin-top: 0.6rem !important;
    margin-bottom: 0.6rem !important;
  }

  /* TYPOGRAPHY ADJUSTMENTS */
  h2 { 
    font-size: 1.3rem !important; 
    margin-bottom: 0.6rem !important; 
  }
  
  p { 
    font-size: 0.85rem !important; 
    line-height: 1.4 !important; 
  }
  
  .text-sm { 
    font-size: 0.8rem !important; 
  }
  
  .text-xs { 
    font-size: 0.75rem !important; 
  }
  
  .font-medium { 
    font-weight: 600 !important; 
  }

  /* BORDER STYLES */
  .border-b {
    border-bottom: 1px solid #ddd !important;
    padding-bottom: 0.7rem !important;
    margin-bottom: 0.7rem !important;
  }

  /* GRID LAYOUT FOR DATA FIELDS (Two-column "Label: Value" format) */
  .grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important; /* Force 2 equal columns */
    gap: 0.8rem 0 !important; /* Vertical gap, horizontal gap is now handled by padding */
    page-break-inside: avoid !important;
  }

  /* Style individual data items within the grid for visual grouping */
  .grid > div {
    display: flex !important;
    flex-direction: row !important;
    align-items: baseline !important;
    gap: 0.4rem !important; /* Gap between label and value */
    padding: 0.1rem 0 !important;
  }

  /* Add a subtle dashed vertical line between the two columns */
  .grid > div:nth-child(odd) { /* Targets items in the left column */
    border-right: 1px dashed #eee !important; /* Subtle dashed line */
    padding-right: 1.5rem !important; /* Space between content and line */
  }
  
  .grid > div:nth-child(even) { /* Targets items in the right column */
    padding-left: 1.5rem !important; /* Space between line and content */
  }

  .grid > div p:first-child { /* The Label */
    font-weight: 600 !important;
    color: #000 !important;
    flex-shrink: 0 !important;
  }

  .grid > div p:last-child { /* The Value */
    flex-grow: 1 !important;
    white-space: normal !important;
    word-break: break-word !important;
    color: #333 !important;
  }
  
  /* Footer adjustments */
  .print-footer {
    text-align: center;
    margin-top: 1cm;
  }
}
</style>
