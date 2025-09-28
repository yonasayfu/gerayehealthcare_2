<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3 } from 'lucide-vue-next'
import { format } from 'date-fns'
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps<{
  eventRecommendation: any;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Event Recommendations', href: route('admin.event-recommendations.index') },
  { title: props.eventRecommendation.patient_name, href: route('admin.event-recommendations.show', props.eventRecommendation.id) },
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

// Delete removed per UI policy; keeping page lean
</script>

<template>
  <Head :title="`Event Recommendation: ${eventRecommendation.patient_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">

      <ShowHeader title="Event Recommendation Details" :subtitle="eventRecommendation.patient_name">
        <template #actions>
          <Link :href="route('admin.event-recommendations.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <!-- Print Header (Logo + Titles) -->
                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Event Recommendation Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Recommendation Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Event ID:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ eventRecommendation.event_id }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Source:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ eventRecommendation.source_channel }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Recommended By:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ eventRecommendation.recommended_by_name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Patient Name:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ eventRecommendation.patient_name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Patient Phone:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ eventRecommendation.phone_number ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Status:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ eventRecommendation.status }}</p>
                    </div>
                    <div class="lg:col-span-3">
                      <p class="text-sm text-muted-foreground">Notes:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ eventRecommendation.notes ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Eligibility</h2>
                  <div class="flex items-center gap-3">
                    <span
                      v-if="eventRecommendation.eligibility?.status === 'eligible'"
                      class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300"
                    >Eligible</span>
                    <span
                      v-else-if="eventRecommendation.eligibility?.status === 'ineligible'"
                      class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300"
                    >Ineligible</span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300"
                    >Unknown</span>
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                      Checked {{ eventRecommendation.eligibility?.checked ?? 0 }}/{{ eventRecommendation.eligibility?.total ?? 0 }} rules
                    </span>
                  </div>

                  <div v-if="eventRecommendation.eligibility?.failed?.length" class="mt-3">
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">Failed Rules:</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300">
                      <li v-for="fr in eventRecommendation.eligibility.failed" :key="fr.title + fr.operator + fr.expected">
                        {{ fr.title }} {{ fr.operator }} {{ fr.expected }} (actual: {{ fr.actual ?? '-' }})
                      </li>
                    </ul>
                  </div>

                  <div v-if="eventRecommendation.eligibility?.missing?.length" class="mt-3">
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">Missing Data For:</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300">
                      <li v-for="m in eventRecommendation.eligibility.missing" :key="m">{{ m }}</li>
                    </ul>
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
          <Link :href="route('admin.event-recommendations.edit', eventRecommendation.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>

    </div>

  </AppLayout>
</template>

<style>
/* Optimized Print Styles for A4 */
@media print {
  @page {
    size: A4 landscape; /* Set page size to A4 */
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
