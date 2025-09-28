<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ArrowLeft, Printer, Download, Edit3, Trash2 } from 'lucide-vue-next'
import ShowHeader from '@/components/ShowHeader.vue'
import { format } from 'date-fns'
import { confirmDialog } from '@/lib/confirm'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  medicalDocument: {
    id: number
    title: string | null
    document_type: string
    document_date: string | null
    summary: string | null
    file_path?: string | null
    patient?: { id: number; full_name?: string }
    medical_visit_id?: number | null
  }
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Medical Documents', href: route('admin.medical-documents.index') },
  { title: props.medicalDocument.title || 'Details', href: null },
]

function printSingleDocument() {
  window.print();
}

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Medical Document',
    message: 'Are you sure you want to delete this medical document?',
    confirmText: 'Delete',
  })
  if (!ok) return
  router.delete(route('admin.medical-documents.destroy', id))
}
</script>

<template>
  <Head :title="`Medical Document: ${props.medicalDocument.title || 'Details'}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Medical Document Details" :subtitle="`Document: ${props.medicalDocument.title || 'N/A'}`">
        <!-- <template #actions>
          <Link :href="route('admin.medical-documents.index')" class="btn-glass btn-glass-sm">Back</Link>
          <button @click="printSingleDocument" class="btn-glass btn-glass-sm">
            <Printer class="icon" />
            <span class="hidden sm:inline">Print</span>
          </button>
          <Link :href="route('admin.medical-documents.edit', props.medicalDocument.id)" class="btn-glass btn-glass-sm">
            <Edit3 class="icon" />
            <span class="hidden sm:inline">Edit</span>
          </Link>
          <button @click="destroy(props.medicalDocument.id)" class="btn-glass btn-glass-sm">
            <Trash2 class="icon" />
            <span class="hidden sm:inline">Delete</span>
          </button>
        </template> -->
      </ShowHeader>

      <div class="p-6 space-y-6">
        <div class="print-document bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

           <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
             <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
             <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
             <p class="text-gray-600 dark:text-gray-400 print-document-title">Medical Document Record</p>
             <hr class="my-3 border-gray-300 dark:border-gray-600 print:my-2">
           </div>

           <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
             <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 print:mb-2">Document Information</h2>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
               <div>
                 <p class="text-sm text-gray-600 dark:text-gray-300">Title:</p>
                 <p class="font-medium text-gray-800 dark:text-gray-100">{{ props.medicalDocument.title || '-' }}</p>
               </div>
               <div>
                 <p class="text-sm text-gray-600 dark:text-gray-300">Document Type:</p>
                 <p class="font-medium text-gray-800 dark:text-gray-100">{{ props.medicalDocument.document_type || '-' }}</p>
               </div>
               <div>
                 <p class="text-sm text-gray-600 dark:text-gray-300">Document Date:</p>
                 <p class="font-medium text-gray-800 dark:text-gray-100">{{ props.medicalDocument.document_date ? format(new Date(props.medicalDocument.document_date), 'PPP') : '-' }}</p>
               </div>
               <div>
                 <p class="text-sm text-gray-600 dark:text-gray-300">Patient:</p>
                 <p class="font-medium text-gray-800 dark:text-gray-100">{{ props.medicalDocument.patient?.full_name || '-' }}</p>
               </div>
               <div>
                 <p class="text-sm text-gray-600 dark:text-gray-300">Medical Visit:</p>
                 <p class="font-medium text-gray-800 dark:text-gray-100">{{ props.medicalDocument.medical_visit_id || '-' }}</p>
               </div>
             </div>
           </div>

           <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
             <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 print:mb-2">Summary</h2>
             <div class="grid grid-cols-1">
               <p class="font-medium text-gray-800 dark:text-gray-100 whitespace-pre-line">{{ props.medicalDocument.summary || '-' }}</p>
             </div>
           </div>

           <div>
             <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 print:mb-2">Attached File</h2>
             <div class="grid grid-cols-1">
               <div v-if="props.medicalDocument.file_path">
                 <a :href="`/storage/${props.medicalDocument.file_path}`" target="_blank" class="underline text-primary-600 flex items-center gap-2">
                   <Download class="icon" />
                   View / Download File
                 </a>
               </div>
               <div v-else class="text-gray-500">No file uploaded</div>
             </div>
           </div>

         </div>
       </div>

      <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <button @click="printSingleDocument" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.medical-documents.edit', props.medicalDocument.id)" class="btn-glass btn-glass-sm">Edit</Link>
          <button @click="destroy(props.medicalDocument.id)" class="btn-glass btn-glass-sm">Delete</button>
        </div>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300 dark:border-gray-600">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

    </div>
  </AppLayout>
</template>

<style>
/* Optimized Print Styles for A4 */
@media print {
  @page {
    size: A4 portrait; /* Changed to portrait for better content flow */
    margin: 1cm; /* Standard margin */
  }

  /* Universal print adjustments */
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important; /* Allow overflow for content */
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
    overflow: visible !important; /* Ensure content is visible */

    /* Removed transform: scale() to prevent content cutting */
    /* transform: scale(0.98); */
    /* transform-origin: top left; */
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
  p { font-size: 0.85rem !important; line-height: 1.4 !important; word-break: break-word; } /* Added word-break */
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
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important; /* More flexible columns */
    gap: 0.8rem 1.5rem !important; /* Vertical gap, horizontal gap */
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

  /* Remove subtle dashed vertical line between the two columns, as it might not be needed with flexible grid */
  .grid > div:nth-child(odd) { /* Targets items in the left column */
    border-right: none !important;
    padding-right: 0 !important;
  }
  .grid > div:nth-child(even) { /* Targets items in the right column */
    padding-left: 0 !important;
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