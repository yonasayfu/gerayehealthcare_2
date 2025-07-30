<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import TextInput from '@/components/ui/input/Input.vue'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

interface MarketingTask {
  id: number;
  task_code: string;
  title: string;
  task_type: string;
  status: string;
  scheduled_at: string;
  completed_at: string;
  campaign: { campaign_name: string };
  assigned_to_staff?: { user?: { name: string } }; // Make user optional
  related_content: { title: string };
  doctor: { user?: { name: string } }; // Make user optional
}

interface MarketingTaskPagination {
  data: MarketingTask[];
  links: any[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

const props = defineProps<{
  marketingTasks: MarketingTaskPagination;
  campaigns: any[];
  staffs: any[];
  campaignContents: any[];
  taskTypes: string[];
  statuses: string[];
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    assigned_to_staff_id?: number;
    task_type?: string;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Tasks', href: route('admin.marketing-tasks.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)
const assignedToStaffId = ref(props.filters.assigned_to_staff_id || '')
const taskType = ref(props.filters.task_type || '')

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage, assignedToStaffId, taskType], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
    assigned_to_staff_id: assignedToStaffId.value,
    task_type: taskType.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.marketing-tasks.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this marketing task?')) {
    router.delete(route('admin.marketing-tasks.destroy', id))
  }
}

function exportData(type: 'csv' | 'pdf') {
  window.open(route('admin.marketing-tasks.export', { type }), '_blank');
}

function printCurrentView() {
    const params = {
        search: search.value,
        sort: sortField.value,
        direction: sortDirection.value,
        assigned_to_staff_id: assignedToStaffId.value,
        task_type: taskType.value,
    };
    const url = route('admin.marketing-tasks.printCurrent', params);
    window.open(url, '_blank');
}

const printAllTasks = () => {
    window.open(route('admin.marketing-tasks.printAll'), '_blank');
};

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

function clearFilters() {
  search.value = '';
  sortField.value = '';
  sortDirection.value = 'asc';
  perPage.value = 10;
  assignedToStaffId.value = '';
  taskType.value = '';
}
</script>

<template>
  <Head title="Marketing Tasks" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div class="flex-grow min-w-0">
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Marketing Tasks Management</h1>
          <p class="text-sm text-muted-foreground">Manage all marketing tasks here, including creation, editing, and deletion.</p>
        </div>
        <div class="flex-shrink-0 flex flex-wrap gap-2">
          <Link :href="route('admin.marketing-tasks.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Task
          </Link>
          <button @click="exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printAllTasks" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print All
          </button>
          <button @click="printCurrentView" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print Current View
          </button>
        </div>
      </div>





      <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <TextInput
            type="text"
            v-model="search"
            placeholder="Search tasks..."
            class="block w-full pr-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>
        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 print:hidden">
        <div>
          <label for="assignedToStaffId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned To Staff:</label>
          <select v-model="assignedToStaffId" id="assignedToStaffId" class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5">
            <option value="">All Staff</option>
            <option v-for="staff in staffs" :key="staff.id" :value="staff.id">{{ staff.user?.name ?? '-' }}</option>
          </select>
        </div>

        <div>
          <label for="taskType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Task Type:</label>
          <select v-model="taskType" id="taskType" class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5">
            <option value="">All Task Types</option>
            <option v-for="type in taskTypes" :key="type" :value="type">{{ type }}</option>
          </select>
        </div>

        <div class="col-span-full flex items-end">
          <button @click="clearFilters" class="w-full inline-flex items-center justify-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            Clear Filters
          </button>
        </div>
      </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Marketing Tasks List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('title')">
                Title <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('task_code')">
                Task Code <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('campaign_id')">
                Campaign <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('task_type')">
                Type <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('scheduled_at')">
                Scheduled At <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('assigned_to_staff_id')">
                Assigned To <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in marketingTasks.data" :key="task.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ task.title }}</td>
              <td class="px-6 py-4">{{ task.task_code ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.campaign?.campaign_name ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.task_type ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.status ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.scheduled_at ? format(new Date(task.scheduled_at), 'PPP p') : '-' }}</td>
              <td class="px-6 py-4">{{ task.assigned_to_staff?.user?.name ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.marketing-tasks.show', task.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.marketing-tasks.edit', task.id)"
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
            <tr v-if="marketingTasks.data.length === 0">
              <td colspan="8" class="text-center px-6 py-4 text-gray-400">No marketing tasks found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="marketingTasks.data.length > 0" :links="marketingTasks.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="print:block text-center mt-4 text-sm text-gray-500 print-footer">
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
  .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 15%; } /* Title */
  .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 10%; } /* Task Code */
  .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 15%; } /* Campaign */
  .print-table th:nth-child(4), .print-table td:nth-child(4) { width: 10%; }  /* Type */
  .print-table th:nth-child(5), .print-table td:nth-child(5) { width: 10%; } /* Status */
  .print-table th:nth-child(6), .print-table td:nth-child(6) { width: 15%; } /* Scheduled At */
  .print-table th:nth-child(7), .print-table td:nth-child(7) { width: 15%; } /* Assigned To */


  .print-table tbody tr:nth-child(even) {
    background-color: #f9f9f9 !important; /* Subtle zebra striping */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .print-table tbody tr:last-child {
    border-bottom: 1px solid #ddd !important;
  }
  .print-table-row {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
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
    position: relative; /* Changed from fixed */
    margin-top: 1cm;
    font-size: 0.75rem !important;
    color: #666 !important;
  }
  .print-footer hr {
    border-color: #ccc !important;
  }
}
</style>