****Index.vue*******

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue' // Import 'computed'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns' // Keep this import

import type { PatientPagination } from '@/types'; // Import PatientPagination type

const props = defineProps<{
  patients: PatientPagination;
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 10)

// Create a computed property for the formatted date string
const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p'); // Use the imported format function here
});

// Trigger search, sort, pagination
watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  // Only add sort parameter if sortField.value is not an empty string
  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.patients.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this patient?')) {
    router.delete(route('admin.patients.destroy', id))
  }
}

function exportData(type: 'csv' | 'pdf') {
  window.open(route('admin.patients.export', { type }), '_blank');
}

function printCurrentView() {
  // Trigger print for the current view of the table
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog for current view. Please check your browser settings or try again.');
    }
  }, 100); // Small delay for reliability
}

const printAllPatients = () => {
    // This will call your PatientController@export method with type=pdf
    window.open(route('admin.patients.export', { type: 'pdf' }), '_blank');
};

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}
</script>

<template>
  <Head title="Patients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Patients</h1>
          <p class="text-sm text-muted-foreground">Manage all patient records here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.patients.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Patient
          </Link>
          <button @click="exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printAllPatients" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print All
          </button>
          <button @click="printCurrentView" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print Current View
          </button>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search patients..."
            class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
          />
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Hospital</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Patient List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('full_name')">
                Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('patient_code')">
                Patient Code <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('fayda_id')">
                Fayda ID <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('date_of_birth')">
                Age <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('gender')">
                Gender <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('phone_number')">
                Phone <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('source')">
                Source <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="patient in patients.data" :key="patient.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ patient.full_name }}</td>
              <td class="px-6 py-4">{{ patient.patient_code ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.fayda_id ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.age ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.gender ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.phone_number ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.source ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.patients.show', patient.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.patients.edit', patient.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(patient.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="patients.data.length === 0">
              <td colspan="8" class="text-center px-6 py-4 text-gray-400">No patients found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="patients.data.length > 0" :links="patients.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>

<style>
/* Print-specific styles for Index.vue (Print Current View) */
@media print {
  @page {
    size: A4 landscape; /* Landscape is often better for tables */
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }

  /* Hide elements */
  .print\:hidden {
    display: none !important;
  }

  /* Specific styles for the print header content (logo and clinic name) */
  .print-header-content {
      display: block !important; /* Show header */
      text-align: center;
      padding-top: 0.5cm;
      padding-bottom: 0.5cm;
      margin-bottom: 0.8cm;
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
      font-size: 1.6rem !important; /* Slightly smaller than show view */
      margin-bottom: 0.2rem !important;
      line-height: 1.2 !important;
      font-weight: bold;
  }
  .print-document-title {
      font-size: 0.85rem !important;
      color: #555 !important;
  }
  hr { border-color: #ccc !important; }

  /* Main content container adjustments */
  .space-y-6.p-6 {
    padding: 0 !important;
    margin: 0 !important;
  }

  /* Table specific print styles */
  .overflow-x-auto.bg-white.dark\:bg-gray-900.shadow.rounded-lg {
    box-shadow: none !important;
    border-radius: 0 !important;
    background-color: transparent !important; /* No background color */
    overflow: visible !important; /* Essential to prevent clipping */
    padding: 1cm; /* Inner padding for the table */
    transform: scale(0.97); /* Slight scale down to fit wide tables */
    transform-origin: top left;
  }

  .print-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 0.8rem !important; /* Adjust table body font size */
    table-layout: fixed; /* Helps with column width distribution */
  }

  .print-table-header {
    background-color: #f0f0f0 !important; /* Light grey header background */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    text-transform: uppercase !important;
  }

  .print-table th, .print-table td {
    border: 1px solid #ddd !important; /* Subtle borders for all cells */
    padding: 0.4rem 0.6rem !important; /* Adjust cell padding */
    color: #000 !important;
    vertical-align: top !important; /* Align content to top of cell */
    word-break: break-word; /* Allow long words to break */
  }

  .print-table th {
    font-weight: bold !important;
    font-size: 0.7rem !important; /* Header font size */
    white-space: nowrap; /* Keep header text on one line if possible */
  }

  /* Adjust column widths if needed, target by nth-child or specific content */
  .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 18%; } /* Name */
  .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 12%; } /* Patient Code */
  .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 15%; } /* Fayda ID */
  .print-table th:nth-child(4), .print-table td:nth-child(4) { width: 8%; }  /* Age */
  .print-table th:nth-child(5), .print-table td:nth-child(5) { width: 10%; } /* Gender */
  .print-table th:nth-child(6), .print-table td:nth-child(6) { width: 15%; } /* Phone */
  .print-table th:nth-child(7), .print-table td:nth-child(7) { width: 10%; } /* Source */


  .print-table tbody tr:nth-child(even) {
    background-color: #f9f9f9 !important; /* Subtle zebra striping */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .print-table tbody tr:last-child {
    border-bottom: 1px solid #ddd !important;
  }

  /* Hide actions column for print */
  .print-table th:last-child,
  .print-table td:last-child {
    display: none !important;
  }

  /* Hide sort arrows on print */
  .print\:hidden {
    display: none !important;
  }

  /* Print Footer */
  .print-footer {
    display: block !important;
    text-align: center;
    margin-top: 1cm;
    font-size: 0.75rem !important;
    color: #666 !important;
  }
  .print-footer hr {
    border-color: #ccc !important;
  }
}
</style>


****Form.vue*******

<script setup lang="ts">
import type { PatientForm, InertiaForm } from '@/types' // Import PatientForm and InertiaForm types

interface LocalErrors {
  source?: string;
  phone_number?: string;
  // Add other potential local errors if they exist
}

const props = defineProps<{
  form: InertiaForm<PatientForm>, // Use InertiaForm with PatientForm generic
  localErrors?: LocalErrors // Use defined interface
}>()

const emit = defineEmits(['submit'])

// Define options for dropdowns here
const genders = ['Male', 'Female', 'Other', 'Prefer not to say']
const sources = ['TikTok', 'Website', 'Referral', 'Walk-in']
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="border-b border-gray-900/10 pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Patient Information</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Use accurate and up-to-date details for patient registration.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.full_name"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.full_name" class="text-red-500 text-sm mt-1">
              {{ form.errors.full_name }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Fayda ID</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.fayda_id"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.fayda_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.fayda_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Source</label>
          <div class="mt-2">
            <select
              v-model="form.source"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            >
              <option :value="null">Select source</option>
              <option v-for="sourceOption in sources" :key="sourceOption" :value="sourceOption">{{ sourceOption }}</option>
            </select>
            <div v-if="form.errors.source" class="text-red-500 text-sm mt-1">
              {{ form.errors.source }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.phone_number"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="props.localErrors?.phone_number" class="text-red-500 text-sm mt-1">
              {{ props.localErrors.phone_number }}
            </div>
            <div v-else-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">
              {{ form.errors.phone_number }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Email Address</label>
          <div class="mt-2">
            <input
              type="email"
              v-model="form.email"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
              {{ form.errors.email }}
            </div>
          </div>
        </div>
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Gender</label>
          <div class="mt-2">
            <select
              v-model="form.gender"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            >
              <option :value="null">Select gender</option>
              <option v-for="genderOption in genders" :key="genderOption" :value="genderOption">{{ genderOption }}</option>
            </select>
            <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">
              {{ form.errors.gender }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Date of Birth <span class="text-red-500">*</span></label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.date_of_birth"
              required
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.date_of_birth" class="text-red-500 text-sm mt-1">
              {{ form.errors.date_of_birth }}
            </div>
          </div>
        </div>

        <div class="col-span-full">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Address</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.address"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">
              {{ form.errors.address }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Emergency Contact</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.emergency_contact"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.emergency_contact" class="text-red-500 text-sm mt-1">
              {{ form.errors.emergency_contact }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Geolocation</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.geolocation"
              placeholder="e.g., 9.012345,38.765432"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.geolocation" class="text-red-500 text-sm mt-1">
              {{ form.errors.geolocation }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>


****Create.vue*******

<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Adjust path if Form.vue is in a different directory
import type { BreadcrumbItemType, PatientForm } from '@/types' // Import PatientForm type

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: 'Create', href: route('admin.patients.create') },
]

// Initialize an empty form for a new patient
const form = useForm<any>({
  full_name: null,
  fayda_id: null,
  date_of_birth: null,
  gender: null,
  address: null,
  phone_number: null,
  email: null,
  source: null,
  emergency_contact: null,
  geolocation: null,
})

function submit() {
  form.post(route('admin.patients.store'))
}
</script>

<template>
  <Head title="Create New Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Patient</h1>
        <p class="text-sm text-muted-foreground">Fill in the details to add a new patient.</p>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" />

        <div class="flex justify-end space-x-3">
          <Link
            :href="route('admin.patients.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
          >
            Cancel
          </Link>
          <button
            @click="submit"
            :disabled="form.processing"
            class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition"
          >
            {{ form.processing ? 'Creating...' : 'Create Patient' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>




****Edit.vue*******

<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType, Patient, PatientForm } from '@/types' // Import Patient and PatientForm

const props = defineProps<{
  patient: Patient; // Use the Patient interface
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: 'Edit', href: route('admin.patients.edit', props.patient.id) },
]

const form = useForm<any>({ // Use any for the form data to satisfy FormDataType constraint
  full_name: props.patient.full_name,
  fayda_id: props.patient.fayda_id,
  date_of_birth: props.patient.date_of_birth,
  gender: props.patient.gender,
  address: props.patient.address,
  phone_number: props.patient.phone_number,
  email: props.patient.email, // Add the missing email field
  source: props.patient.source,
  emergency_contact: props.patient.emergency_contact,
  geolocation: props.patient.geolocation,
})

function submit() {
  form.put(route('admin.patients.update', props.patient.id))
}
</script>

<template>
  <Head title="Edit Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Patient</h1>
        <p class="text-sm text-muted-foreground">Update patient details as necessary.</p>
      </div>

      <!-- Form Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" />

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
          <Link
            :href="route('admin.patients.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
          >
            Cancel
          </Link>
          <button
            @click="submit"
            :disabled="form.processing"
            class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition"
          >
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>



****Show.vue*******


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
  // Add a small delay to ensure the DOM is ready for printing.
  // This can sometimes resolve issues where the print dialog doesn't appear
  // or content is not rendered correctly.
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      // Optionally, provide user feedback if print fails
      alert('Failed to open print dialog. Please check your browser settings or try again.');
    }
  }, 100); // 100ms delay
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

        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Hospital</h1>
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



****PrintAll.vue*******


<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { onMounted, computed } from 'vue'
import { format } from 'date-fns'

// Import your main CSS for proper styling in the preview tab
import '../../../../css/app.css'; // Adjust this path if necessary

import type { Patient } from '@/types'; // Import Patient type

const props = defineProps<{
  patients: Patient[]; // Use Patient[] for type safety
}>()

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

onMounted(() => {
  window.onafterprint = () => {
    // Add a small delay (e.g., 10-50 milliseconds) before closing
    setTimeout(() => {
      window.close();
    }, 50); // Try 50ms, you can adjust this value
  };

  window.print();
});

function calculateAge(dob: string) {
  if (!dob) return '-';
  const birthDate = new Date(dob);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  return age;
}
</script>

<template>
  <Head title="All Patients Print View" />

  <div class="print-container">
    <div class="print-header-content">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
        <h1 class="print-clinic-name">Geraye Hospital</h1>
        <p class="print-document-title">All Patient Records</p>
        <hr class="my-3 border-gray-300 print:my-2">
    </div>

    <div class="overflow-x-auto print-table-container">
      <table class="w-full text-left text-sm text-gray-800">
        <thead class="bg-gray-100 text-xs uppercase text-muted-foreground print-table-header">
          <tr>
            <th class="px-6 py-3">Name</th>
            <th class="px-6 py-3">Patient Code</th>
            <th class="px-6 py-3">Fayda ID</th>
            <th class="px-6 py-3">Age</th>
            <th class="px-6 py-3">Gender</th>
            <th class="px-6 py-3">Phone</th>
            <th class="px-6 py-3">Source</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="patient in patients" :key="patient.id" class="border-b print-table-row">
            <td class="px-6 py-4">{{ patient.full_name }}</td>
            <td class="px-6 py-4">{{ patient.patient_code ?? '-' }}</td>
            <td class="px-6 py-4">{{ patient.fayda_id ?? '-' }}</td>
            <td class="px-6 py-4">{{ patient.date_of_birth ? calculateAge(patient.date_of_birth) : '-' }}</td>
            <td class="px-6 py-4">{{ patient.gender ?? '-' }}</td>
            <td class="px-6 py-4">{{ patient.phone_number ?? '-' }}</td>
            <td class="px-6 py-4">{{ patient.source ?? '-' }}</td>
          </tr>
          <tr v-if="patients.length === 0">
            <td colspan="7" class="text-center px-6 py-4 text-gray-400">No patients found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4 text-sm text-gray-500 print-footer">
        <hr class="my-2 border-gray-300">
        <p>Document Generated: {{ formattedGeneratedDate }}</p>
    </div>
  </div>
</template>

<style>
/* Print-specific styles for PrintAll.vue */
@media print {
    @page {
        size: A4 landscape;
        margin: 0.5cm;
    }
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color: #000 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    .print-container {
        padding: 1cm;
        transform: scale(0.95);
        transform-origin: top left;
        width: 100%;
        height: auto;
    }

    .print-header-content {
        text-align: center;
        margin-bottom: 0.8cm;
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
        font-size: 1.6rem !important;
        margin-bottom: 0.2rem !important;
        line-height: 1.2 !important;
        font-weight: bold;
    }
    .print-document-title {
        font-size: 0.85rem !important;
        color: #555 !important;
    }
    hr { border-color: #ccc !important; }

    .print-table-container {
        box-shadow: none !important;
        border-radius: 0 !important;
        overflow: visible !important;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 0.8rem !important;
    }
    thead {
        background-color: #f0f0f0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    th, td {
        border: 1px solid #ddd !important;
        padding: 0.5rem 0.75rem !important;
        color: #000 !important;
    }
    th {
        font-weight: bold !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
    }
    .print-table-row:nth-child(even) {
        background-color: #f9f9f9 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .print-table-row:last-child {
        border-bottom: 1px solid #ddd !important;
    }
    th.text-right, td.text-right {
        text-align: right !important;
    }
    th.text-left, td.text-left {
        text-align: left !important;
    }
    th:last-child, td:last-child {
        display: none !important;
    }

    .print-footer {
        text-align: center;
        margin-top: 1cm;
        font-size: 0.7rem !important;
        color: #666 !important;
    }
    .print-footer hr {
        border-color: #ccc !important;
    }
}
</style>
