<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3 } from 'lucide-vue-next' // Import icons
import { format } from 'date-fns' // For date formatting

interface CampaignContent {
  id: number;
  title: string;
  description: string;
  media_url: string;
  content_type: string;
  status: string;
  scheduled_post_date: string;
  actual_post_date: string;
  engagement_metrics: Record<string, any>;
  campaign: { campaign_name: string };
  platform: { name: string };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  campaignContent: CampaignContent;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Campaign Contents', href: route('admin.campaign-contents.index') },
  { title: props.campaignContent.title, href: route('admin.campaign-contents.show', props.campaignContent.id) },
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

// Delete removed per request

const formatJson = (json: Record<string, any>) => {
  return JSON.stringify(json, null, 2);
};

const metricEntries = computed(() => {
  const m = (props.campaignContent?.engagement_metrics ?? {}) as Record<string, any>
  if (m && typeof m === 'object' && !Array.isArray(m)) {
    return Object.entries(m)
  }
  return [] as Array<[string, any]>
})

function valueType(val: any): 'null'|'array'|'object'|'number'|'boolean'|'string' {
  if (val === null || val === undefined) return 'null'
  if (Array.isArray(val)) return 'array'
  if (typeof val === 'object') return 'object'
  return typeof val as any
}
</script>

<template>
  <Head :title="`Campaign Content: ${campaignContent.title}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Campaign Content Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Campaign Content: {{ item.name || item.title || item.id }}</p>
          </div>
          <!-- top actions intentionally removed to avoid duplication; see footer -->
        </div>
      </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Campaign Content Record</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Content Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Title:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.title }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Campaign:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.campaign?.campaign_name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Platform:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.platform?.name ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Content Type:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.content_type ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Status:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.status ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Scheduled Post Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.scheduled_post_date ? format(new Date(campaignContent.scheduled_post_date), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Actual Post Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.actual_post_date ? format(new Date(campaignContent.actual_post_date), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Media URL:</p>
                      <p class="mt-1 text-lg text-gray-900 dark:text-white"><a :href="campaignContent.media_url" target="_blank" class="text-cyan-600 hover:underline">{{ campaignContent.media_url ?? '-' }}</a></p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Engagement Metrics</h2>
                  <div class="grid grid-cols-1">
                    <div>
                      <template v-if="metricEntries.length">
                        <div class="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-700">
                          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                              <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Metric</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Value</th>
                              </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                              <tr v-for="([key, val], idx) in metricEntries" :key="key + '-' + idx">
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-200">{{ key }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                  <template v-if="valueType(val) === 'string' || valueType(val) === 'number' || valueType(val) === 'boolean'">
                                    <span>{{ String(val) }}</span>
                                  </template>
                                  <template v-else-if="valueType(val) === 'null'">
                                    <span class="text-gray-500">-</span>
                                  </template>
                                  <template v-else>
                                    <details class="group">
                                      <summary class="cursor-pointer select-none text-cyan-700 dark:text-cyan-400 hover:underline">
                                        {{ valueType(val) === 'array' ? 'View items (' + val.length + ')' : 'View details' }}
                                      </summary>
                                      <pre class="mt-2 bg-gray-50 dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700 overflow-auto text-xs">{{ formatJson(val) }}</pre>
                                    </details>
                                  </template>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <details class="mt-3">
                          <summary class="cursor-pointer text-sm text-gray-600 dark:text-gray-300 hover:underline">View raw JSON</summary>
                          <pre class="mt-2 font-mono text-xs text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-800 p-3 rounded-md overflow-auto">{{ formatJson(campaignContent.engagement_metrics) }}</pre>
                        </details>
                      </template>
                      <template v-else>
                        <p class="text-sm text-gray-500">No engagement metrics available.</p>
                      </template>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Timestamps</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Created At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.created_at ? format(new Date(campaignContent.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Updated At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ campaignContent.updated_at ? format(new Date(campaignContent.updated_at), 'PPP p') : '-' }}</p>
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
          <Link :href="route('admin.campaign-contents.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="printSingleCampaignContent" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.campaign-contents.edit', item.id)" class="btn-glass btn-glass-sm">Edit</Link>
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