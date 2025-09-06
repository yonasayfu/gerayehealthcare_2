<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import TextInput from '@/components/ui/input/Input.vue'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

interface MarketingTask {
  id: number;
  task_code: string;
  title: string;
  expected_results?: string;
  task_type: string;
  status: string;
  scheduled_at: string;
  completed_at: string;
  campaign: { campaign_name: string };
  related_content: { title: string };
  assigned_to_staff?: { user?: { name?: string } };
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
  taskTypes: string[];
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    campaign_id?: number;
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
const campaignId = ref(props.filters.campaign_id || '')
const assignedToStaffId = ref(props.filters.assigned_to_staff_id || '')
const taskType = ref(props.filters.task_type || '')

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage, campaignId, assignedToStaffId, taskType], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
    campaign_id: campaignId.value,
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

function printCurrentView() {
  window.print();
}



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
  perPage.value = 5;
  campaignId.value = '';
  assignedToStaffId.value = '';
  taskType.value = '';
}
</script>

<style>
/* Print optimizations for Index "Print Current" */
@media print {
  @page {
    size: A4;
    margin: 1cm;
  }

  html, body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    font-size: 12px !important; /* Prevent over-shrinking */
    line-height: 1.4 !important;
    overflow: visible !important;
  }

  /* Remove overflow clipping from containers */
  .overflow-x-auto {
    overflow: visible !important;
  }

  /* Table layout adjustments */
  .print-table {
    width: 100% !important;
    table-layout: auto !important; /* Let columns size naturally */
    border-collapse: collapse !important;
  }
  .print-table-header th {
    background: #f3f4f6 !important; /* subtle gray */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .print-table th,
  .print-table td {
    padding: 6px 8px !important; /* Slightly tighter to avoid squish */
    white-space: normal !important; /* Allow wrapping */
    word-break: break-word !important; /* Break long words */
    vertical-align: top !important;
    font-size: 12px !important;
  }

  /* Hide interactive-only elements */
  .print\:hidden { display: none !important; }
  .hidden.print\:block { display: block !important; }

  /* Reduce outer spacing for a tighter print */
  .space-y-6 > :not([hidden]) ~ :not([hidden]) {
    margin-top: .75rem !important;
  }

  /* Footer */
  .print-footer {
    page-break-inside: avoid !important;
    margin-top: .5rem !important;
    font-size: 11px !important;
    color: #555 !important;
  }
}
</style>

<template>
  <Head title="Marketing Tasks" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Marketing Tasks</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage marketing tasks</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.marketing-tasks.create')" class="btn-glass">
              <span>Add Marketing Task</span>
            </Link>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
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
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 print:hidden">
        <div>
          <label for="campaignId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Campaign:</label>
          <select v-model="campaignId" id="campaignId" class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5">
            <option value="">All Campaigns</option>
            <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
          </select>
        </div>

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
              <th class="px-6 py-3">
                Expected Results
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
              <td class="px-6 py-4">{{ task.expected_results ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.campaign?.campaign_name ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.task_type ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.status ?? '-' }}</td>
              <td class="px-6 py-4">{{ task.scheduled_at ? format(new Date(task.scheduled_at), 'PPP p') : '-' }}</td>
              <td class="px-6 py-4">{{ task.assigned_to_staff?.user?.name ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.marketing-tasks.show', task.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.marketing-tasks.edit', task.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(task.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
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