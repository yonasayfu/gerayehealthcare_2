<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Book, Loader2 } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue' // Use the component


const props = defineProps<{
  assignments:as
  filters?: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Assignments', href: '/dashboard/assignments' },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)
const isPrintingAll = ref(false)

// Trigger search, sort, pagination
watch([search, sortField, sortDirection, perPage], debounce(() => {
  // Don't refetch data if we are in the middle of printing all records
  if (isPrintingAll.value) return;
  router.get('/dashboard/assignments', {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this assignment?')) {
    router.delete(route('admin.assignments.destroy', id))
  }
}

import { useExport } from '@/Composables/useExport';

const { exportData, printCurrentView, printAllRecords } = useExport({ routeName: 'admin.assignments', filters: props.filters });


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
    return new Date(dateString).toLocaleString();
}
</script>

<template>
  <Head title="Caregiver Assignments" />

  <!-- Custom Print Header -->
  <div id="print-header" class="hidden print-only">
    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;">
    <h1 style="text-align: center; margin: 0; font-size: 20px;">Geraye Home Care Services</h1>
    <p style="text-align: center; margin: 0; font-size: 14px;">Caregiver Assignment Records</p>
  </div>

  <AppLayout :breadcrumbs="breadcrumbs">
    <div id="main-content" class="space-y-6 p-6">

      <!-- Header + Actions -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Caregiver Assignments</h1>
          <p class="text-sm text-muted-foreground">Manage all staff and patient assignments here.</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
          <Link :href="route('admin.assignments.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Assignment
          </Link>
          <button @click="exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printCurrentView()" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200" title="Print Current Page">
            <Printer class="h-4 w-4" /> Print Page
          </button>
          <button @click="printAllRecords()" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200" title="Print All Records">
             <Book class="h-4 w-4" />
             Print All
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 no-print">
        <!-- Search -->
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search assignments..."
            class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
          />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" />
          </svg>
        </div>

        <!-- Pagination Dropdown -->
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

      <!-- Table -->
      <div id="assignments-table" class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Staff Member</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('shift_start')">
                Shift Start <span class="no-print"><ArrowUpDown class="inline w-4 h-4 ml-1" /></span>
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('shift_end')">
                Shift End <span class="no-print"><ArrowUpDown class="inline w-4 h-4 ml-1" /></span>
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <span class="no-print"><ArrowUpDown class="inline w-4 h-4 ml-1" /></span>
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('created_at')">
                Date Created <span class="no-print"><ArrowUpDown class="inline w-4 h-4 ml-1" /></span>
              </th>
              <th class="px-6 py-3 text-right no-print">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="assignment in assignments.data" :key="assignment.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
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
              <td class="px-6 py-4 text-right no-print">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.assignments.show', assignment.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.assignments.edit', assignment.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(assignment.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
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

      <!-- Pagination Links -->
     
      <!-- THE FIX IS HERE: Replaced the manual links with the reusable Pagination component -->
      <Pagination v-if="assignments.data.length > 0" :links="assignments.links" class="mt-6 flex justify-center" />

    </div>
  </AppLayout>
</template>

