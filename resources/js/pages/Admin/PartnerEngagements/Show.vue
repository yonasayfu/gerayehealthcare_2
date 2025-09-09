<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3 } from 'lucide-vue-next'
import type { BreadcrumbItemType } from '@/types'
import { format } from 'date-fns'
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps<{
  partnerEngagement: {
    id: number
    partner_id: number
    staff_id: number
    engagement_type: string
    summary: string
    engagement_date: string
    follow_up_date: string | null
    created_at: string
    updated_at: string
    partner?: { name: string } | null;
    staff?: { first_name: string; last_name: string } | null;
  };
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Partner Engagements', href: route('admin.partner-engagements.index') },
  { title: `Engagement: ${props.partnerEngagement.summary.substring(0, 20)}...`, href: route('admin.partner-engagements.show', props.partnerEngagement.id) },
]

function printPage() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog. Please check your browser settings or try again.');
    }
  }, 100);
}
</script>

<template>
  <Head :title="`Partner Engagement: ${props.partnerEngagement.summary.substring(0, 20)}...`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Partner Engagement Details" :subtitle="`Partner Engagement: ${props.partnerEngagement.summary?.substring(0, 30) ?? props.partnerEngagement.id}`">
        <template #actions>
          <Link :href="route('admin.partner-engagements.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>
    </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Partner Engagement Record Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Engagement Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Partner:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.partner?.name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Staff:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.staff?.first_name }} {{ props.partnerEngagement.staff?.last_name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Type:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.engagement_type }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Summary:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.summary }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Engagement Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.engagement_date }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Follow Up Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.follow_up_date ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">System Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Created At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.created_at ? format(new Date(props.partnerEngagement.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Last Updated:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ props.partnerEngagement.updated_at ? format(new Date(props.partnerEngagement.updated_at), 'PPP p') : '-' }}</p>
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

              <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.partner-engagements.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.partner-engagements.edit', props.partnerEngagement.id)" class="btn-glass btn-glass-sm">Edit</Link>
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
