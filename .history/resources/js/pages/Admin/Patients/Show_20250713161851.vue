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

function printPage() {
  window.print()
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
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Patient Details: {{ patient.full_name }}</h1>
          <p class="text-sm text-muted-foreground">Comprehensive view of patient record.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button @click="printPage" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print Document
          </button>
          <Link
            :href="route('admin.patients.edit', patient.id)"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md transition"
          >
            <Edit3 class="w-4 h-4" /> Edit Patient
          </Link>
          <button @click="destroy(patient.id)" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md transition">
            <Trash2 class="w-4 h-4" /> Delete Patient
          </button>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

        <div class="hidden print:block text-center mb-4 print:mb-2">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white print:text-xl">Your Clinic Name / Logo Here</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 print:text-xs">Patient Record Document</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2 print:text-base">Patient Identification</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Full Name:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.full_name }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Patient Code:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.patient_code ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Fayda ID:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.fayda_id ?? '-' }}</p>
            </div>
          </div>
        </div>

        <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2 print:text-base">Demographics</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Gender:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.gender ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Date of Birth:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.date_of_birth ? format(new Date(patient.date_of_birth), 'PPP') : '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Age:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.age !== null ? patient.age : '-' }}</p>
            </div>
          </div>
        </div>

        <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2 print:text-base">Contact Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Phone Number:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.phone_number ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Email:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.email ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Emergency Contact:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.emergency_contact ?? '-' }}</p>
            </div>
            <div class="lg:col-span-3">
              <p class="text-sm text-muted-foreground print:text-xs">Address:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.address ?? '-' }}</p>
            </div>
          </div>
        </div>

        <div>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2 print:text-base">Administrative Details</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Source:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.source ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Geolocation:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.geolocation ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Registered By:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">
                <span v-if="patient.registered_by_staff">Staff: {{ patient.registered_by_staff.name }}</span>
                <span v-else-if="patient.registered_by_caregiver">Caregiver: {{ patient.registered_by_caregiver.name }}</span>
                <span v-else>-</span>
              </p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Registered Date:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.created_at ? format(new Date(patient.created_at), 'PPP p') : '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground print:text-xs">Last Updated:</p>
              <p class="font-medium text-gray-900 dark:text-white print:text-sm">{{ patient.updated_at ? format(new Date(patient.updated_at), 'PPP p') : '-' }}</p>
            </div>
          </div>
        </div>

        <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print:text-xs">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
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
    -webkit-print-color-adjust: exact !important; /* Forces printing of background colors/images if needed */
    print-color-adjust: exact !important;
    color: #000 !important; /* Ensure all text is black for print */
    margin: 0 !important; /* Remove default body margins */
    padding: 0 !important; /* Remove default body padding */
    overflow: hidden !important; /* Prevent scrollbars and potential layout shifts */
  }

  /* Elements to hide during print */
  .print\:hidden {
    display: none !important;
  }
  /* HIDE BREADCRUMBS AND TOP NAV from AppSidebarLayout.vue */
  /* This rule targets the header component (AppSidebarHeader) and sidebar (AppSidebar)
     if they are rendered by AppSidebarLayout and marked with print:hidden */
  .app-sidebar-header, .app-sidebar { /* You might need to add specific classes to these components */
      display: none !important;
  }
  /* Fallback/more general selectors if the above doesn't catch it all */
  body > header, /* Hides main layout header if it exists */
  body > nav, /* Hides main layout nav if it exists (e.g., breadcrumbs or main top bar) */
  [role="banner"], /* Common ARIA role for header/banner */
  [role="navigation"] { /* Common ARIA role for navigation */
      display: none !important;
  }


  /* Elements to show only during print */
  .hidden.print\:block {
    display: block !important;
  }

  /* Target the main patient document container for scaling and layout */
  .bg-white.dark\:bg-gray-900.shadow.rounded-lg {
    box-shadow: none !important; /* Remove shadow */
    border-radius: 0 !important; /* Remove rounded corners */
    border: none !important; /* Remove borders */
    padding: 1.5cm !important; /* Adjust padding for print - can be fine-tuned */
    margin: 0 !important; /* Ensure no external margin */
    width: 100% !important; /* Occupy full available print width */
    height: auto !important; /* Let height adjust naturally */
    overflow: visible !important; /* Ensure content is not clipped */

    /* KEY: Scale down the content slightly to fit more */
    transform: scale(0.92); /* Adjust this value (e.g., 0.9 to 0.98) */
    transform-origin: top left; /* Ensures scaling starts from the top-left */
  }

  /* Reduce overall top-level padding/margin if the wrapper div adds too much */
  .p-6.space-y-6 {
    padding: 0 !important;
    margin: 0 !important;
  }
  
  /* Adjust spacing within sections for print */
  .space-y-8 > div:not(:first-child) {
    margin-top: 0.8rem !important; /* Slightly reduce vertical spacing between main sections */
    margin-bottom: 0.8rem !important;
  }
  .space-y-6 > div:not(:first-child) {
    margin-top: 0.6rem !important;
    margin-bottom: 0.6rem !important;
  }

  /* TYPOGRAPHY ADJUSTMENTS */
  h1 { font-size: 1.3rem !important; } /* Clinic Name/Logo */
  h2 { font-size: 1.1rem !important; margin-bottom: 0.6rem !important; } /* Section Titles */
  p { font-size: 0.72rem !important; line-height: 1.35 !important; } /* General text, slightly smaller */
  .text-sm { font-size: 0.68rem !important; }
  .text-xs { font-size: 0.65rem !important; }
  .font-medium { font-weight: 600 !important; }

  /* BORDER STYLES */
  .border-b {
    border-bottom: 1px solid #ddd !important; /* Lighter, clean separator */
    padding-bottom: 0.7rem !important;
    margin-bottom: 0.7rem !important;
  }

  /* GRID LAYOUT FOR DATA FIELDS (This is the "fancy" part) */
  .grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important; /* Force 2 equal columns */
    gap: 0.8rem 2rem !important; /* Vertical gap smaller, horizontal gap wider */
    page-break-inside: avoid !important; /* Prevent breaking within a grid section */
  }

  /* Style individual data items within the grid */
  .grid > div {
    display: flex !important; /* Use flexbox for inline Label: Value */
    flex-direction: row !important; /* Arrange label and value horizontally */
    align-items: baseline !important; /* Align text baselines */
    gap: 0.4rem !important; /* Small gap between label and value */
    padding: 0.1rem 0 !important; /* Very slight padding for each key-value pair */
  }

  .grid > div p:first-child { /* The Label */
    font-weight: 600 !important; /* Make label bolder */
    color: #000 !important; /* Ensure black */
    flex-shrink: 0 !important; /* Prevent label from shrinking */
  }

  .grid > div p:last-child { /* The Value */
    flex-grow: 1 !important; /* Allow value to take up remaining space */
    white-space: normal !important; /* Allow value to wrap */
    word-break: break-word !important; /* Break long words */
    color: #333 !important; /* Ensure dark gray */
  }

  /* Print Header/Footer adjustments */
  .hidden.print\:block .text-center {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
  }
}
</style>