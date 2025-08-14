<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue' // Import 'computed'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns' // Keep this import

import type { PatientPagination } from '@/types'; // Import PatientPagination type

const props = defineProps<{
  patients: PatientPagination;
  filters?: {
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

// Fix: Use optional chaining and provide defaults
const search = ref(props.filters?.search || '')
const sortField = ref(props.filters?.sort || '')
const sortDirection = ref(props.filters?.direction || 'asc')
const perPage = ref(props.filters?.per_page || 5)

// Create a computed property for the formatted date string
const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p'); // Use the imported format function here
});

const currentDate = computed(() => {
  return format(new Date(), 'PPP');
});

// Add a computed property to calculate the index numbers
const currentIndex = computed(() => {
  return (props.patients.current_page - 1) * props.patients.per_page;
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

function exportData(type: 'csv', preview: boolean = false) {
  const params: Record<string, string | boolean> = { type };
  if (preview) {
    params.preview = true;
  }
  window.open(route('admin.patients.export', params), '_blank');
}

function printCurrentView() {
  // Trigger browser print of the current index view
  setTimeout(() => window.print(), 50);
}

const printAllPatients = () => {
    window.open(route('admin.patients.printAll', { preview: true }), '_blank');
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
          <p class="text-sm text-muted-foreground">Today's Date: {{ currentDate }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.patients.create')" class="btn btn-primary">
            + Add Patient
          </Link>
          <button @click="exportData('csv')" class="btn btn-success">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="printAllPatients" class="btn btn-info">
            <Printer class="h-4 w-4" /> Print All
          </button>
          <button @click="printCurrentView" class="btn btn-dark">
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
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="5">5</option>
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
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Patient List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3">#</th> <!-- Added index column header -->
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
            <tr v-for="(patient, index) in patients.data" :key="patient.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
               <td class="px-6 py-4">{{ currentIndex + index + 1 }}</td> 
              <td class="px-6 py-4">{{ patient.full_name }}</td>
              <td class="px-6 py-4">{{ patient.patient_code ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.fayda_id ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.age !== undefined && patient.age !== null ? patient.age : (patient.date_of_birth ? Math.max(0, new Date().getFullYear() - new Date(patient.date_of_birth).getFullYear() - ((new Date().getMonth() < new Date(patient.date_of_birth).getMonth()) || (new Date().getMonth() === new Date(patient.date_of_birth).getMonth() && new Date().getDate() < new Date(patient.date_of_birth).getDate()) ? 1 : 0)) : '-') }}</td>
              <td class="px-6 py-4">{{ patient.gender ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.phone_number ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.source ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.patients.show', patient.id)"
                    class="btn-icon text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.patients.edit', patient.id)"
                    class="btn-icon text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(patient.id)" class="btn-icon text-red-600" title="Delete">
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

    </div>
  </AppLayout>
</template>
