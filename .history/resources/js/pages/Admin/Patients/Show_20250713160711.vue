<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3' // <-- CORRECTED: router is imported here
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
    // router is already imported at the top level, so we just use it directly
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

      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:border-none">

        <div class="hidden print:block text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Your Clinic Name / Logo Here</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Patient Record Document</p>
            <hr class="my-4 border-gray-300">
        </div>

        <div class="border-b pb-4 mb-4">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Patient Identification</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
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

        <div class="border-b pb-4 mb-4">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Demographics</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
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

        <div class="border-b pb-4 mb-4">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Contact Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
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
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Administrative Details</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
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
                <span v-if="patient.registered_by_staff">Staff: {{ patient.registered_by_staff.name }}</span>
                <span v-else-if="patient.registered_by_caregiver">Caregiver: {{ patient.registered_by_caregiver.name }}</span>
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

        <div class="hidden print:block text-center mt-8 text-sm text-gray-500">
            <hr class="my-4 border-gray-300">
            <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
            </div>

      </div>
    </div>
  </AppLayout>
</template>

<style>
/* Basic Print Styles - Add this to your main CSS file if it affects all pages,
   or keep it here if only relevant to this component.
   Using a <style> tag in a Vue SFC will scope it to this component.
*/
@media print {
  /* Hide elements not needed for print */
  .print\:hidden {
    display: none !important;
  }

  /* Make print-specific elements visible */
  .hidden.print\:block {
    display: block !important;
  }

  /* Ensure text is black for readability on paper */
  body {
    color: #000 !important;
    -webkit-print-color-adjust: exact; /* Ensures background colors are printed if any, though we're removing them */
    print-color-adjust: exact;
  }

  /* Remove shadows, borders, background colors from main content area */
  .print\:shadow-none {
    box-shadow: none !important;
  }
  .print\:rounded-none {
    border-radius: 0 !important;
  }
  .print\:p-0 {
    padding: 0 !important;
  }
  .print\:border-none {
    border: none !important;
  }

  /* Ensure background of elements is white on print */
  .bg-white, .dark\:bg-gray-900 {
    background-color: white !important;
  }
  .dark\:text-white, .dark\:text-gray-300, .dark\:text-gray-400 {
    color: #000 !important;
  }
  .dark\:border-gray-700 {
    border-color: #eee !important; /* Lighter border for print */
  }

  /* Text colors for print */
  .text-gray-800, .text-gray-900 {
    color: #000 !important;
  }
  .text-muted-foreground, .text-gray-600, .text-gray-500 {
    color: #333 !important;
  }
  .font-medium {
      font-weight: 600 !important; /* Ensure good bolding */
  }
}
</style>