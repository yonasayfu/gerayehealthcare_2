<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { ArrowUpDown, Edit3, Trash2, Eye, Printer, Download } from 'lucide-vue-next'
import { format } from 'date-fns'

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
      priority_level: number
      created_by_user: { name: string } | null
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
    search: string
    sort_by: string
    sort_order: 'asc' | 'desc'
    per_page: number
  },
}>()
const search    = ref(filters.search || '')
const sortBy    = ref(filters.sort_by)
const sortOrder = ref(filters.sort_order)
const perPage   = ref(filters.per_page || 5)

// Export/Print handlers
function exportData(format: 'csv') {
  // Client-side CSV export of currently visible rows
  const rows = taskDelegations.data
  const header = ['Title', 'Assigned To', 'Due Date', 'Status', 'Priority', 'Created By']
  const csv = [
    header.join(','),
    ...rows.map((t) => [
      '"' + (t.title ?? '') + '"',
      '"' + (t.assignee?.first_name + ' ' + t.assignee?.last_name) + '"',
      '"' + new Date(t.due_date).toLocaleDateString() + '"',
      '"' + (t.status ?? '') + '"',
      '"' + (t.priority_level <= 1 ? 'Low' : t.priority_level === 2 ? 'Medium' : t.priority_level === 3 ? 'Normal' : t.priority_level === 4 ? 'High' : 'Critical') + '"',
      '"' + (t.created_by_user?.name ?? 'Unknown') + '"',
    ].join(',')), 
  ].join('\n')

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'task_delegations.csv'
  a.click()
  URL.revokeObjectURL(url)
}

function printCurrentView() { setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100); }

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

// Breadcrumbs for layout
const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Task Delegations', href: route('admin.task-delegations.index') },
]
</script>

<template>
  <Head title="Task Delegations" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Header & Actions -->
            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Task Delegations</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage task delegations</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.task-delegations.create')" class="btn-glass">
              <span>Add Task Delegation</span>
            </Link>
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
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
            <PerPageSelect v-model="perPage" id="perPage" />
          </div>
      </div>

      <!-- Print Header for Current View -->
      <div class="hidden print:block text-center mb-4 print-header-content">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
        <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
        <p class="text-gray-600 dark:text-gray-400 print-document-title">Task Delegations (Current View)</p>
        <hr class="my-3 border-gray-300 print:my-2">
      </div>

      <div class="overflow-x-auto bg-white shadow rounded-lg">
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
              <th @click="toggleSort('priority_level')" class="px-6 py-3 cursor-pointer">
                Priority <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Created By</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="task in taskDelegations.data"
              :key="task.id"
              class="border-b hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row"
            >
              <td class="px-6 py-4">{{ task.title }}</td>
              <td class="px-6 py-4">
                <!-- 3. Use assignee relation -->
                {{ task.assignee?.first_name }} {{ task.assignee?.last_name }}
              </td>
              <td class="px-6 py-4" :class="(new Date(task.due_date) < new Date() && task.status !== 'Completed') ? 'text-red-600 font-medium' : ''">{{ new Date(task.due_date).toLocaleDateString() }}</td>
              <td class="px-6 py-4">
                <span
                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                    :class="{
                      'bg-gray-100 text-gray-800': task.priority_level <= 1, // Low
                      'bg-yellow-100 text-yellow-800': task.priority_level === 2, // Medium
                      'bg-orange-100 text-orange-800': task.priority_level === 3, // Normal
                      'bg-red-100 text-red-800': task.priority_level === 4, // High
                      'bg-purple-100 text-purple-800': task.priority_level === 5, // Critical
                    }"
                  >
                    {{ 
                      task.priority_level <= 1 ? 'Low' : 
                      task.priority_level === 2 ? 'Medium' : 
                      task.priority_level === 3 ? 'Normal' : 
                      task.priority_level === 4 ? 'High' : 'Critical' 
                    }}
                  </span>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                  :class="{
                    'bg-yellow-100 text-yellow-800': task.status === 'Pending',
                    'bg-blue-100 text-blue-800': task.status === 'In Progress',
                    'bg-green-100 text-green-800': task.status === 'Completed',
                  }"
                >
                  {{ task.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                {{ task.created_by_user?.name || 'Unknown' }}
              </td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.task-delegations.show', task.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.task-delegations.edit', task.id)"
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
            <tr v-if="!taskDelegations.data.length">
              <td colspan="6" class="py-6 text-center text-muted-foreground">
                No tasks found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Print Footer -->
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>

      <div class="flex justify-between items-center mt-6 print:hidden">
        <Pagination v-if="taskDelegations.data.length" :links="taskDelegations.links" />
      </div>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page { size: A4 landscape; margin: 0.5cm; }
  .app-sidebar-header, .app-sidebar { display: none !important; }
  body > header, body > nav, [role="banner"], [role="navigation"] { display: none !important; }
  html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
  table { border-collapse: collapse; width: 100%; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }
}
</style>