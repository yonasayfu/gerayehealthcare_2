<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3, Trash2 } from 'lucide-vue-next' // Import icons
import type { BreadcrumbItemType, Staff } from '@/types' // Import Staff type
import { format } from 'date-fns' // For date formatting

const props = defineProps<{
  staff: Staff; // Use Staff type for type safety
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff', href: route('admin.staff.index') },
  { title: `${props.staff.first_name} ${props.staff.last_name}`, href: route('admin.staff.show', props.staff.id) },
]

function printPage() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog. Please check your browser settings or try again.');
    }
  }, 100); // 100ms delay
}
</script>

<template>
  <Head :title="`Staff: ${props.staff.first_name} ${props.staff.last_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-background text-foreground border border-border rounded-lg shadow relative m-10">

        <div class="flex items-center justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Staff Details: {{ staff.first_name }} {{ staff.last_name }}
            </h3>
            <div class="flex items-center gap-3">
              <img
                :src="staff.photo_url ?? '/images/staff/placeholder.jpg'"
                alt="Staff Photo"
                class="w-28 h-28 rounded-full object-cover border print:w-32 print:h-32"
                @error="(e: Event) => { (e.target as HTMLImageElement).src = '/images/staff/placeholder.jpg' }"
              />
              <Link :href="route('admin.staff.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center print:hidden" title="Close">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
              </Link>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="print-document bg-card text-card-foreground shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Staff Record Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Staff Identification</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">First Name:</p>
                      <p class="font-medium text-foreground">{{ staff.first_name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Last Name:</p>
                      <p class="font-medium text-foreground">{{ staff.last_name }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Contact Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Phone:</p>
                      <p class="font-medium text-foreground">{{ staff.phone ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Email:</p>
                      <p class="font-medium text-foreground">{{ staff.email ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Employment Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Position:</p>
                      <p class="font-medium text-foreground">{{ staff.position ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Department:</p>
                      <p class="font-medium text-foreground">{{ staff.department ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Status:</p>
                      <p class="font-medium text-foreground">{{ staff.status ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Hire Date:</p>
                      <p class="font-medium text-foreground">{{ staff.hire_date ? format(new Date(staff.hire_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Hourly Rate:</p>
                      <p class="font-medium text-foreground">
                        {{ staff.hourly_rate !== null && staff.hourly_rate !== undefined && !isNaN(Number(staff.hourly_rate))
                          ? Number(staff.hourly_rate).toFixed(2)
                          : '-' }}
                      </p>
                    </div>
                  </div>
                </div>

                

                <div>
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">System Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Created At:</p>
                      <p class="font-medium text-foreground">{{ staff.created_at ? format(new Date(staff.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Last Updated:</p>
                      <p class="font-medium text-foreground">{{ staff.updated_at ? format(new Date(staff.updated_at), 'PPP p') : '-' }}</p>
                    </div>
                  </div>
                </div>

                <!-- spacer to keep content above fixed print footer -->
                <div class="hidden print:block h-24"></div>
                <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print:text-xs print-footer">
                    <hr class="my-2 border-gray-300">
                    <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
                </div>

            </div>
        </div>

        <div class="p-6 border-t border-border rounded-b">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.staff.index')" class="btn btn-outline print:hidden">
                Back to List
              </Link>
              <button @click="printPage" class="btn btn-dark print:hidden">
                <Printer class="h-4 w-4" /> Print Current
              </button>
              <Link :href="route('admin.staff.edit', props.staff.id)"
                class="btn btn-primary print:hidden">
                Edit
              </Link>
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

  /* Fixed footer for print */
  .print-footer {
    position: fixed !important;
    bottom: 0.3cm !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    background: #ffffff !important;
    padding-top: 0.2rem !important;
  }

  /* GRID LAYOUT FOR DATA FIELDS (Two-column "Label: Value" format) */
  .grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important; /* Force 2 equal columns */
    gap: 0.8rem 0 !important; /* Vertical gap, horizontal gap is now handled by padding */
    page-break-inside: avoid !important;
  }

  /* Style individual data items within the grid for visual grouping */
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
