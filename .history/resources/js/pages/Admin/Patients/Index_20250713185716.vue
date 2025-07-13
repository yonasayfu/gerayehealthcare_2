<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue' // Import 'computed'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns' // Keep this import

const props = defineProps<{
  patients: any; // Ideally, define a more specific type for patients data
  filters: any;
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
  window.print()
}

function printAllPatients() {
  const printWindow = window.open(route('admin.patients.printAll'), '_blank');
  if (printWindow) {
    // Optional: Add logic to close the window after print (might need a slight delay)
     printWindow.onload = () => {
       printWindow.print();
        setTimeout(() => { printWindow.close(); }, 1000); // Give browser time to show print dialog
    };
  } else {
    alert("Please allow pop-ups for this site to print all patients.");
  }
}

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
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" />
          </svg>
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