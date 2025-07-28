<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { Download, FileText, Printer, ArrowUpDown, Edit3, Trash2, Eye } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

// ————————————————
// 1. Destructure props: use `assignee` here
// ————————————————
const { taskDelegations, filters } = defineProps<{
  taskDelegations: {
    data: Array<{
      id: number
      title: string
      assignee: { first_name: string; last_name: string }
      due_date: string
      status: string
    }>
    links: { url: string | null; label: string; active: boolean }[]
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      per_page: number;
      to: number;
      total: number;
    };
  }
  filters: {
    type: Object,
    default: () => ({
      search: '',
      sort_by: 'due_date',
      sort_order: 'asc',
      per_page: 10,
    }),
  },
}>()
const search    = ref(filters.search || '')
const sortBy    = ref(filters.sort_by)
const sortOrder = ref(filters.sort_order)
const perPage   = ref(filters.per_page || 10)

watch([search, sortBy, sortOrder, perPage], debounce(([searchValue, sort_byValue, sort_orderValue, perPageValue]) => {
  router.get(
    route('admin.task-delegations.index'),
    { search: searchValue, sort_by: sort_byValue, sort_order: sort_orderValue, per_page: perPageValue },
    { preserveState: true, replace: true }
  )
}, 500))

function toggleSort(field: string) {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = field
    sortOrder.value = 'asc'
  }
}

function destroy(id: number) {
  if (confirm('Delete this task?')) {
    router.delete(route('admin.task-delegations.destroy', { task_delegation: id }))
  }
}

function exportCSV() { window.open(route('admin.task-delegations.export', { type: 'csv' }), '_blank') }
function exportPDF() { window.open(route('admin.task-delegations.export', { type: 'pdf' }), '_blank') }
function printTable() { window.print() }
</script>

<template>
  <Head title="Task Delegations" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Header & Actions -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-muted/40 p-4 rounded-lg shadow">
        <div>
          <h1 class="text-xl font-semibold">Task Delegations</h1>
          <p class="text-sm text-muted-foreground">Assign and manage tasks for staff members.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link
            :href="route('admin.task-delegations.create')"
            class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded"
          >+ Assign Task</Link>
          <button @click="exportCSV" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportPDF" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printTable" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
            <Printer class="h-4 w-4" /> Print
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <div class="relative w-full md:w-1/3">
              <input type="text" v-model="search" placeholder="Search tasks..." class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100" />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
          </div>
          <div>
              <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
              <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
          </div>
      </div>

      <!-- Table -->
      <div class="printable-area">
        <div class="header hidden print:block">
          <img src="/images/geraye_logo.jpeg" alt="Logo" class="logo" />
          <h1 class="title">Task Delegations Report</h1>
        </div>
        <div class="overflow-x-auto bg-white shadow rounded-lg">
          <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 text-xs uppercase text-muted-foreground">
              <tr>
                <th @click="toggleSort('title')" class="px-6 py-3 cursor-pointer">
                  Title <ArrowUpDown class="inline w-4 h-4 ml-1" />
                </th>
                <th class="px-6 py-3">Assigned To</th>
                <th @click="toggleSort('due_date')" class="px-6 py-3 cursor-pointer">
                  Due Date <ArrowUpDown class="inline w-4 h-4 ml-1" />
                </th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="task in taskDelegations.data"
                :key="task.id"
                class="border-b hover:bg-gray-50"
              >
                <td class="px-6 py-4">{{ task.title }}</td>
                <td class="px-6 py-4">
                  <!-- 3. Use assignee relation -->
                  {{ task.assignee.first_name }} {{ task.assignee.last_name }}
                </td>
                <td class="px-6 py-4">{{ new Date(task.due_date).toLocaleDateString() }}</td>
                <td class="px-6 py-4">{{ task.status }}</td>
                <td class="px-6 py-4 text-right">
                  <div class="inline-flex items-center justify-end space-x-2">
                    <Link
                      :href="route('admin.task-delegations.show', { task_delegation: task.id })"
                      class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                      title="View Details"
                    >
                      <Eye class="w-4 h-4" />
                    </Link>
                    <Link
                      :href="route('admin.task-delegations.edit', { task_delegation: task.id })"
                      class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                      title="Edit"
                    >
                      <Edit3 class="w-4 h-4" />
                    </Link>
                    <button @click="destroy(task.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!taskDelegations.data.length">
                <td colspan="5" class="py-6 text-center text-muted-foreground">
                  No tasks found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="footer hidden print:block">
          Page <span class="page-number"></span> of <span class="total-pages"></span>
        </div>
      </div>

      <div class="flex justify-between items-center mt-6">
        <Pagination v-if="taskDelegations.data.length" :links="taskDelegations.links" />
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
@media print {
  body * { visibility: hidden; }
  .printable-area, .printable-area * { visibility: visible; }
  .printable-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #ccc;
  }
  .logo {
    max-width: 150px;
  }
  .title {
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
  }
  .footer {
    text-align: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #ccc;
  }
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    border: 1px solid #ccc;
    padding: 0.5rem;
  }
  th {
    background-color: #f2f2f2;
  }
}
</style>
