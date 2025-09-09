<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { confirmDialog } from '@/lib/confirm'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3, Trash2 } from 'lucide-vue-next'
import type { BreadcrumbItemType } from '@/types'
import { format } from 'date-fns'
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps<{
  visitService: any;
}>()

// Printed date to show in browser print footer
const generatedAt: string = format(new Date(), 'PPP p')

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Visit Services', href: route('admin.visit-services.index') },
  { title: `Visit #${props.visitService.id}`, href: route('admin.visit-services.show', props.visitService.id) },
]

function printSingleVisit() {
  // Print the current page using the built-in browser print dialog
  window.print();
}

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Visit',
    message: 'Are you sure you want to delete this visit?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.visit-services.destroy', id))
}
</script>

<template>
  <Head :title="`Visit Service: ${visitService.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Visit Service Details" :subtitle="`Visit Service: ${visitService.id}`">
        <template #actions>
          <Link :href="route('admin.visit-services.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

        <div class="p-6 space-y-6">
            <div class="print-document bg-card text-card-foreground shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Visit Service Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Visit Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Patient:</p>
                      <p class="font-medium text-foreground">{{ visitService.patient?.full_name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Staff:</p>
                      <p class="font-medium text-foreground">{{ visitService.staff ? `${visitService.staff.first_name} ${visitService.staff.last_name}` : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Scheduled At:</p>
                      <p class="font-medium text-foreground">{{ visitService.scheduled_at ? format(new Date(visitService.scheduled_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Check-in Time:</p>
                      <p class="font-medium text-foreground">{{ visitService.check_in_time ? format(new Date(visitService.check_in_time), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Check-out Time:</p>
                      <p class="font-medium text-foreground">{{ visitService.check_out_time ? format(new Date(visitService.check_out_time), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Status:</p>
                      <p class="font-medium text-foreground">{{ visitService.status ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Visit Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Check-in Location:</p>
                      <p class="font-medium text-foreground">{{ visitService.check_in_latitude && visitService.check_in_longitude ? `${visitService.check_in_latitude}, ${visitService.check_in_longitude}` : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Check-out Location:</p>
                      <p class="font-medium text-foreground">{{ visitService.check_out_latitude && visitService.check_out_longitude ? `${visitService.check_out_latitude}, ${visitService.check_out_longitude}` : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Visit Notes:</p>
                      <p class="font-medium text-foreground">{{ visitService.visit_notes ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Administrative Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Created At:</p>
                      <p class="font-medium text-foreground">{{ visitService.created_at ? format(new Date(visitService.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Updated At:</p>
                      <p class="font-medium text-foreground">{{ visitService.updated_at ? format(new Date(visitService.updated_at), 'PPP p') : '-' }}</p>
                    </div>
                  </div>
                </div>

                

            </div>
        </div>

              <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.visit-services.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="printSingleVisit" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.visit-services.edit', visitService.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>

        <!-- Print-only footer with generated date -->
        <div class="hidden print:block print-footer">
          <div class="print-footer-inner">
            <div>Generated on: {{ generatedAt }}</div>
            <div class="print-footer-note">Geraye Home Care Services - Confidential Document</div>
          </div>
        </div>

    </div>

  </AppLayout>
</template>

<style>
/* Optimized Print Styles for A4 */
@media print {
  @page {
    size: A4; /* Set page size to A4 */
    margin: 0.5cm; /* Reduce margins significantly to give more space for content */
  }

  /* Universal print adjustments */
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
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
  }

  .print-logo {
      max-width: 150px; /* Adjust as needed */
      max-height: 50px; /* Adjust as needed */
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

  /* Print footer */
  .print-footer {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    z-index: 1000 !important;
  }
  .print-footer-inner {
    text-align: center !important;
    font-size: 0.8rem !important;
    color: #444 !important;
    border-top: 1px solid #ccc !important;
    padding: 8px 0 !important;
    background: #fff !important;
  }
  .print-footer-note {
    font-size: 0.7rem !important;
    color: #777 !important;
    margin-top: 2px !important;
  }

  /* Target the main patient document container for scaling and layout */
  .bg-white.dark\:bg-gray-900.shadow.rounded-lg {
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    padding: 1cm !important;
    margin: 0 !important;
    width: 100% !important;
    height: auto !important;
    overflow: visible !important;

    transform: scale(0.98); /* Less aggressive scaling. Adjust if it goes to 2 pages */
    transform-origin: top left;
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
  h2 { font-size: 1.3rem !important; margin-bottom: 0.6rem !important; }
  p { font-size: 0.85rem !important; line-height: 1.4 !important; }
  .text-sm { font-size: 0.8rem !important; }
  .text-xs { font-size: 0.75rem !important; }
  .font-medium { font-weight: 600 !important; }

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
}
</style>
