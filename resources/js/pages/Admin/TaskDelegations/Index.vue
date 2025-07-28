<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed, nextTick } from 'vue' // Added nextTick
import debounce from 'lodash/debounce'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { Download, FileText, Printer, ArrowUpDown, Edit3, Trash2, Eye } from 'lucide-vue-next'
import { format } from 'date-fns'
import { Button } from '@/components/ui/button'
import TaskDelegationsPrint from '@/components/print/TaskDelegationsPrint.vue' // Import the print component

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

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

const showPrintView = ref(false); // Reactive variable to control print view

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

async function printTable() {
  showPrintView.value = true; // Show the print-only component
  await nextTick(); // Wait for the DOM to update
  try {
    window.print();
  } catch (error) {
    console.error('Print failed:', error);
    alert('Failed to open print dialog. Please check your browser settings or try again.');
  } finally {
    showPrintView.value = false; // Hide the print-only component after printing
  }
}
</script>

<template>
  <Head title="Task Delegations" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Header & Actions -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-muted/40 p-4 rounded-lg shadow print:hidden">
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
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
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
      <div class="overflow-x-auto bg-white shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Hospital</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Task Delegations Report</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th @click="toggleSort('title')" class="px-6 py-3 cursor-pointer">
                Title <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Assigned To</th>
              <th @click="toggleSort('due_date')" class="px-6 py-3 cursor-pointer">
                Due Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="task in taskDelegations.data"
              :key="task.id"
              class="border-b hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 print-table-row"
            >
              <td class="px-6 py-4">{{ task.title }}</td>
              <td class="px-6 py-4">
                <!-- 3. Use assignee relation -->
                {{ task.assignee.first_name }} {{ task.assignee.last_name }}
              </td>
              <td class="px-6 py-4">{{ new Date(task.due_date).toLocaleDateString() }}</td>
              <td class="px-6 py-4">{{ task.status }}</td>
              <td class="px-6 py-4 text-right print:hidden">
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
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

      <div class="flex justify-between items-center mt-6 print:hidden">
        <Pagination v-if="taskDelegations.data.length" :links="taskDelegations.links" />
      </div>
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
  .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 18%; } /* Item */
  .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 15%; } /* Scheduled Date */
  .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 15%; } /* Actual Date */
  .print-table th:nth-child(4), .print-table td:nth-child(4) { width: 15%; } /* Performed By */
  .print-table th:nth-child(5), .print-table td:nth-child(5) { width: 10%; } /* Status */


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
