<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3, Trash2 } from 'lucide-vue-next' // Import icons
import type { BreadcrumbItemType } from '@/types' // Assuming you have this type defined
import { format } from 'date-fns' // For date formatting


const props = defineProps<{
  patient: any; // Ideally, define a more specific type for patient data
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: props.patient.full_name, href: route('admin.patients.show', props.patient.id) },
]

function printSinglePatient() {
  window.open(route('admin.patients.printSingle', { id: props.patient.id, preview: true }), '_blank');
}

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this patient?')) {
    router.delete(route('admin.patients.destroy', id))
  }
}
</script>

<template>
  <Head :title="`Patient: ${patient.full_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Patient Details: {{ patient.full_name }}
            </h3>
            <Link :href="route('admin.patients.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Patient Record Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Patient Identification</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Full Name:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.full_name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Patient Code:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.patient_code ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Fayda ID:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.fayda_id ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Demographics</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Gender:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.gender ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Date of Birth:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.date_of_birth ? format(new Date(patient.date_of_birth), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Age:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.age !== null ? patient.age : '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Contact Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Phone Number:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.phone_number ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Email:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.email ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Emergency Contact:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.emergency_contact ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3">
                      <p class="text-sm text-muted-foreground">Address:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.address ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Administrative Details</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Source:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.source ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Geolocation:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.geolocation ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Registered By:</p>
                      <p class="font-medium text-gray-900 dark:text-white">
                        <span v-if="patient.registered_by_staff">Staff: {{ patient.registered_by_staff.full_name }}</span>
                        <span v-else>-</span>
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Registered Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.created_at ? format(new Date(patient.created_at), 'PPP p') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Last Updated:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ patient.updated_at ? format(new Date(patient.updated_at), 'PPP p') : '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print:text-xs">
                    <hr class="my-2 border-gray-300">
                    <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
                    </div>

            </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap gap-2">
              <button @click="printSinglePatient" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md focus:ring-4 focus:ring-gray-300">
                <Printer class="h-4 w-4" /> Print Document
              </button>
              <Link
                :href="route('admin.patients.edit', patient.id)"
                class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
              >
                Edit Patient
              </Link>
              <button @click="destroy(patient.id)" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md transition">
                <Trash2 class="w-4 h-4" /> Delete Patient
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
