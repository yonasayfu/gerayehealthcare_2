<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { confirmDialog } from '@/lib/confirm'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Loader2, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { useExport } from '@/composables/useExport';

const props = defineProps<{
  assignments: any;
  filters?: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Assignments', href: route('admin.assignments.index') },
]

// Fix: Provide default values and use optional chaining
const search = ref(props.filters?.search || '')
const sortField = ref(props.filters?.sort || '')
const sortDirection = ref(props.filters?.direction || 'asc')
const perPage = ref(props.filters?.per_page || 5)

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

  router.get(route('admin.assignments.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Assignment',
    message: 'Are you sure you want to delete this assignment?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.assignments.destroy', id))
}

// Fix: Pass filters with default empty object
const { exportData, printCurrentView } = useExport({ 
  routeName: 'admin.assignments', 
  filters: { ...(props.filters || {}), preview: true } 
});

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

const getStaffFullName = (staff) => {
    if (!staff) return 'N/A';
    return `${staff.first_name || ''} ${staff.last_name || ''}`.trim();
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return format(new Date(dateString), 'PPP p');
}
</script>

<template>
  <Head title="Caregiver Assignments" />



  <AppLayout :breadcrumbs="breadcrumbs">
    <div id="main-content" class="space-y-6 p-6 print:p-0 print:space-y-0">

      <!-- Liquid glass header -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="print:hidden">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Caregiver Assignments</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">Manage all staff and patient assignments here.</p>
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.assignments.create')" class="btn-glass">
              <span>Add Assignment</span>
            </Link>
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView()" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search assignments..."
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

      <div id="assignments-table" class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Caregiver Assignments (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Staff Member</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('shift_start')">
                Shift Start <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('shift_end')">
                Shift End <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('created_at')">
                Date Created <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(assignment, index) in assignments.data" :key="assignment.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ ((assignments.current_page - 1) * assignments.per_page) + index + 1 }}</td>
              <td class="px-6 py-4">{{ assignment.patient?.full_name ?? 'N/A' }}</td>
              <td class="px-6 py-4">{{ getStaffFullName(assignment.staff) }}</td>
              <td class="px-6 py-4">{{ formatDate(assignment.shift_start) }}</td>
              <td class="px-6 py-4">{{ formatDate(assignment.shift_end) }}</td>
              <td class="px-6 py-4">
                 <span :class="[
                  'px-2 py-1 rounded-full text-xs font-semibold',
                  assignment.status === 'Completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                  assignment.status === 'Assigned' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                  assignment.status === 'In Progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                ]">{{ assignment.status }}</span>
              </td>
              <td class="px-6 py-4">{{ formatDate(assignment.created_at) }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.assignments.show', assignment.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.assignments.edit', assignment.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(assignment.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="assignments.data.length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No assignments found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6 space-y-2 print:hidden">
        <div class="flex justify-center">
          <Pagination v-if="assignments.data.length > 0" :links="assignments.links" />
        </div>
        <p v-if="assignments.total" class="text-center text-sm text-gray-600 dark:text-gray-300">
          Showing {{ assignments.from || 0 }}–{{ assignments.to || 0 }} of {{ assignments.total }}
        </p>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ formatDate(new Date().toISOString()) }}</p>
      </div>

    </div>
  </AppLayout>
</template>

<style>
/* Print styles for Caregiver Assignments */
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
    margin-bottom: 0.8cm !important;
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

  /* Table styling for print */
  .print-table {
    width: 100% !important;
    border-collapse: collapse !important;
    margin: 0 !important;
  }

  .print-table th,
  .print-table td {
    border: 1px solid #ccc !important;
    padding: 4px 6px !important;
    font-size: 10px !important;
    text-align: left !important;
  }

  .print-table thead {
    background-color: #f3f4f6 !important;
  }
}
</style>
