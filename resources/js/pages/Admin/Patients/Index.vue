<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3' // Added router
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns' // Keep this import
import { useTableFilters } from '@/composables/useTableFilters'
import { useExport } from '@/composables/useExport'

import type { PatientPagination } from '@/types';

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

// Centralized search/sort/perPage handling with URL param sync
const { search, perPage, toggleSort } = useTableFilters({
  routeName: 'admin.patients.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort,
    direction: props.filters?.direction,
    per_page: props.filters?.per_page ?? props.patients?.per_page ?? 5,
  },
})

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

// Delete confirmation modal state
const showConfirm = ref(false)
const pendingDeleteId = ref<number | null>(null)

function confirmDelete(id: number) {
  pendingDeleteId.value = id
  showConfirm.value = true
}

function cancelDelete() {
  showConfirm.value = false
  pendingDeleteId.value = null
}

function proceedDelete() {
  if (!pendingDeleteId.value) return
  router.delete(route('admin.patients.destroy', pendingDeleteId.value), {
    onFinish: () => {
      showConfirm.value = false
      pendingDeleteId.value = null
    },
  })
}

const { exportData, printCurrentView, printAllRecords: printAllPatients } = useExport({ routeName: 'admin.patients', filters: props.filters || {} })

// Expose sort toggler for headers
function onToggleSort(field: string) { toggleSort(field) }

</script>

<template>
  <AppLayout>
    <Head title="Patients" />

    <div class="flex-1 space-y-4 p-6">

      <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Patients</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage patient records</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <a :href="route('admin.patients.create')" class="btn-glass">
              <span>Add Patient</span>
            </a>

            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
            <button @click="printAllPatients" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print All</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <!-- Responsive search input container -->
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search patients..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent relative z-10">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Patient List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-100 print-table">
          <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-600 dark:text-gray-300 print-table-header">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('full_name')">
                Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('patient_code')">
                Patient Code <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('fayda_id')">
                Fayda ID <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('date_of_birth')">
                Age <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('gender')">
                Gender <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('phone_number')">
                Phone <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('source')">
                Source <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(patient, index) in patients.data" :key="patient.id"
                class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 print-table-row">
              <td class="px-6 py-4 text-gray-700 dark:text-gray-200">{{ currentIndex + index + 1 }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ patient.full_name }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ patient.patient_code ?? '-' }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ patient.fayda_id ?? '-' }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ patient.age !== undefined && patient.age !== null ? patient.age : (patient.date_of_birth ? Math.max(0, new Date().getFullYear() - new Date(patient.date_of_birth).getFullYear() - ((new Date().getMonth() < new Date(patient.date_of_birth).getMonth()) || (new Date().getMonth() === new Date(patient.date_of_birth).getMonth() && new Date().getDate() < new Date(patient.date_of_birth).getDate()) ? 1 : 0)) : '-') }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ patient.gender ?? '-' }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ patient.phone_number ?? '-' }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ patient.source ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.patients.show', patient.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.patients.edit', patient.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="confirmDelete(patient.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="patients.data.length === 0">
              <td colspan="9" class="text-center px-6 py-4 text-gray-400 dark:text-gray-400">No patients found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="patients.data.length > 0" :links="patients.links" class="mt-6 flex justify-center print:hidden" />

      <!-- Confirm Delete Modal -->
      <div v-if="showConfirm" class="fixed inset-0 z-[60] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="cancelDelete" aria-hidden="true"></div>
        <div role="dialog" aria-modal="true" aria-labelledby="confirm-title"
             class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
          <h3 id="confirm-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delete Patient</h3>
          <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Are you sure you want to delete this patient? This action cannot be undone.</p>
          <div class="mt-6 flex justify-end gap-2">
            <button type="button" @click="cancelDelete" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
            <button type="button" @click="proceedDelete" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Delete</button>
          </div>
        </div>
      </div>
      <p v-if="patients.total" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300 print:hidden">
        Showing {{ patients.from || 0 }}–{{ patients.to || 0 }} of {{ patients.total }}
      </p>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300 dark:border-gray-600">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

    </div>
  </AppLayout>
</template>

<style>
/* ...existing styles... */

/* Search card — pop / glass effect */
.search-card {
  position: relative;
  background: linear-gradient(180deg, rgba(255,255,255,0.65), rgba(255,255,255,0.45));
  -webkit-backdrop-filter: blur(8px) saturate(140%);
  backdrop-filter: blur(8px) saturate(140%);
  border: 1px solid rgba(15,23,42,0.06);
  box-shadow: 0 6px 18px rgba(2,6,23,0.06), inset 0 2px 6px rgba(255,255,255,0.5);
  transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
  z-index: 2;
}

/* dark variant uses inherited .dark rules from .btn-glass / liquidGlass */
.dark .search-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
  border: 1px solid rgba(255,255,255,0.06);
  box-shadow: 0 6px 18px rgba(2,6,23,0.45), inset 0 2px 8px rgba(255,255,255,0.02);
}

/* hover / focus pop */
.search-card:hover,
.search-card:focus-within {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 14px 30px rgba(2,6,23,0.10);
}

/* input sizing */
.search-card input {
  -webkit-appearance: none;
  appearance: none;
  border: none;
  background: transparent;
}

/* accessibility: reduce motion respect */
@media (prefers-reduced-motion: reduce) {
  .search-card,
  .search-card:hover,
  .search-card:focus-within {
    transition: none;
    transform: none;
  }
}
</style>
