<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3, Trash2 } from 'lucide-vue-next' // Import icons
import { format } from 'date-fns' // For date formatting
import ShowHeader from '@/components/ShowHeader.vue'

interface LeadSource {
  id: number;
  name: string;
  category: string;
  description: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  leadSource: LeadSource;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Lead Sources', href: route('admin.lead-sources.index') },
  { title: props.leadSource.name, href: route('admin.lead-sources.show', props.leadSource.id) },
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
  if (confirm('Are you sure you want to delete this lead source?')) {
    router.delete(route('admin.lead-sources.destroy', id))
  }
}
</script>

<template>
  <Head :title="`Lead Source: ${leadSource.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Lead Source Details" :subtitle="`Lead Source: ${leadSource.name}`" class="print:hidden">
        <template #actions>
          <Link :href="route('admin.lead-sources.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

        <div class="p-6 space-y-6">
            <div class="print-document bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Lead Source Record</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Source Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Source Name:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ leadSource.name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Category:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ leadSource.category ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Is Active:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ leadSource.is_active ? 'Yes' : 'No' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Timestamps</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Created At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ leadSource.created_at ? format(new Date(leadSource.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Updated At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ leadSource.updated_at ? format(new Date(leadSource.updated_at), 'PPP p') : '-' }}</p>
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
          <Link :href="route('admin.lead-sources.edit', leadSource.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>

    </div>

  </AppLayout>
</template>

<style>
/* Print styles based on CaregiverAssignment Show.vue */
@media print {
  @page {
    size: A4 landscape;
    margin: 2cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .print\:hidden {
    display: none !important;
  }

  .hidden.print\:block {
    display: block !important;
  }

  /* Print header styling */
  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 1.5cm !important;
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

  /* Content adjustments */
  .space-y-8 > div:not(:first-child) {
    margin-top: 1.5rem !important;
    margin-bottom: 1.5rem !important;
  }

  .space-y-6 > div:not(:first-child) {
    margin-top: 1rem !important;
    margin-bottom: 1rem !important;
  }

  /* Typography adjustments */
  h2 {
    font-size: 1.3rem !important;
    margin-bottom: 1rem !important;
  }

  p {
    font-size: 0.9rem !important;
    line-height: 1.5 !important;
  }

  .text-sm {
    font-size: 0.85rem !important;
  }

  .text-xs {
    font-size: 0.8rem !important;
  }

  .font-medium {
    font-weight: 600 !important;
  }

  /* Border styles */
  .border-b {
    border-bottom: 1px solid #ddd !important;
    padding-bottom: 1rem !important;
    margin-bottom: 1rem !important;
  }

  /* Fixed footer for print */
  .print-footer {
    position: fixed !important;
    bottom: 1.5cm !important;
    left: 2cm !important;
    right: 2cm !important;
    width: calc(100% - 4cm) !important;
    background: #ffffff !important;
    padding-top: 0.3rem !important;
    font-size: 0.8rem !important;
  }

  /* Grid layout for data fields */
  .grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 1.2rem 0 !important;
    page-break-inside: avoid !important;
  }

  /* Style individual data items within the grid for visual grouping */
  .grid > div:nth-child(odd) {
    border-right: 1px dashed #eee !important;
    padding-right: 2.5rem !important;
  }

  .grid > div:nth-child(even) {
    padding-left: 2.5rem !important;
  }

  .grid > div p:first-child {
    font-weight: 600 !important;
    color: #000 !important;
    flex-shrink: 0 !important;
  }

  .grid > div p:last-child {
    flex-grow: 1 !important;
    white-space: normal !important;
    word-break: break-word !important;
    color: #333 !important;
  }

  /* Hide elements not needed in print */
  .btn-glass,
  .liquidGlass-wrapper,
  .border-t {
    display: none !important;
  }

  /* Add padding to the main print container */
  .print-document {
    padding: 2cm !important;
    margin: 0 !important;
  }

  /* Prevent content overlap */
  .print-document > div {
    break-inside: avoid !important;
  }
}
</style>