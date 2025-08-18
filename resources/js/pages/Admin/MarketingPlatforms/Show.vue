<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'
import { Printer, Edit3, Trash2 } from 'lucide-vue-next' // Import icons

interface MarketingPlatform {
  id: number;
  name: string;
  api_endpoint: string;
  api_credentials: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  marketingPlatform: MarketingPlatform;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Platforms', href: route('admin.marketing-platforms.index') },
  { title: props.marketingPlatform.name, href: route('admin.marketing-platforms.show', props.marketingPlatform.id) },
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

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this marketing platform?')) {
    router.delete(route('admin.marketing-platforms.destroy', id))
  }
}
</script>

<template>
  <Head :title="marketingPlatform.name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t print:hidden">
            <h3 class="text-xl font-semibold">
                Marketing Platform Details: {{ marketingPlatform.name }}
            </h3>
            <Link :href="route('admin.marketing-platforms.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6 main-content-display print:hidden print-only">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8">

                <div class="border-b pb-4 mb-4">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Platform Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
                    <div>
                      <p class="text-sm text-muted-foreground">Platform Name:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ marketingPlatform.name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">API Endpoint:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ marketingPlatform.api_endpoint ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">API Credentials:</p>
                      <p class="font-medium text-gray-900 dark:text-white break-all">{{ marketingPlatform.api_credentials ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Is Active:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ marketingPlatform.is_active ? 'Yes' : 'No' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Timestamps</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
                    <div>
                      <p class="text-sm text-muted-foreground">Created At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ marketingPlatform.created_at ? format(new Date(marketingPlatform.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Updated At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ marketingPlatform.updated_at ? format(new Date(marketingPlatform.updated_at), 'PPP p') : '-' }}</p>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <!-- Print-specific content -->
        <div class="hidden print:block">
            <div class="print-header-content">
                <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                <p class="text-gray-600 dark:text-gray-400 print-document-title">Marketing Platform Record</p>
                <hr class="my-3 border-gray-300 print:my-2">
            </div>

            <div class="print-details-section">
                <h2 class="print-section-title">Platform Details</h2>
                <div class="print-grid">
                    <div class="print-grid-item">
                        <p class="print-label">Platform Name:</p>
                        <p class="print-value">{{ marketingPlatform.name }}</p>
                    </div>
                    <div class="print-grid-item">
                        <p class="print-label">API Endpoint:</p>
                        <p class="print-value">{{ marketingPlatform.api_endpoint ?? '-' }}</p>
                    </div>
                    <div class="print-grid-item">
                        <p class="print-label">API Credentials:</p>
                        <p class="print-value break-all" style="word-wrap: break-word; white-space: normal;">{{ marketingPlatform.api_credentials ?? '-' }}</p>
                    </div>
                    <div class="print-grid-item">
                        <p class="print-label">Is Active:</p>
                        <p class="print-value">{{ marketingPlatform.is_active ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>

            <div class="print-details-section">
                <h2 class="print-section-title">Timestamps</h2>
                <div class="print-grid">
                    <div class="print-grid-item">
                        <p class="print-label">Created At:</p>
                        <p class="print-value">{{ marketingPlatform.created_at ? format(new Date(marketingPlatform.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div class="print-grid-item">
                        <p class="print-label">Updated At:</p>
                        <p class="print-value">{{ marketingPlatform.updated_at ? format(new Date(marketingPlatform.updated_at), 'PPP p') : '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="print-footer-content">
                <hr class="my-2 border-gray-300">
                <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b print:hidden">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.marketing-platforms.index')" class="btn btn-outline">
                Back to List
              </Link>
              <Link
                :href="route('admin.marketing-platforms.edit', marketingPlatform.id)"
                class="btn btn-primary"
              >
                Edit Platform
              </Link>
              <button @click="printPage" class="btn btn-dark">
                <Printer class="h-4 w-4" /> Print Current
              </button>
              <button @click="destroy(marketingPlatform.id)" class="btn btn-danger">
                <Trash2 class="w-4 h-4" /> Delete Platform
              </button>
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
    overflow: visible !important;
  }

  /* Hide all elements that are not explicitly part of the print content */
  body > *:not(.print-only) {
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