<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { Download, FileText, Printer, ArrowUpDown, Edit3, Trash2 } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

// Props from controller
const { taskDelegations, filters } = defineProps<{
  taskDelegations: {
    data: Array<{
      id: number
      title: string
      assigned_to: { first_name: string; last_name: string }
      due_date: string
      status: string
    }>
    links: { url: string|null; label: string; active: boolean }[]
  }
  filters: {
    search: string|null
    sort_by: string
    sort_order: 'asc'|'desc'
  }
}>()

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Task Delegations', href: route('admin.task-delegations.index') },
]

// Reactive filter + sort state
const search        = ref(filters.search || '')
const sortBy        = ref(filters.sort_by)
const sortOrder     = ref(filters.sort_order)

// Watch & debounce to reload index
watch([search, sortBy, sortOrder], debounce(() => {
  router.get(
    route('admin.task-delegations.index'),
    { search: search.value, sort_by: sortBy.value, sort_order: sortOrder.value },
    { preserveState: true, replace: true }
  )
}, 500))

// Helpers
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

function exportCSV() {
  window.open(route('admin.task-delegations.export', { type: 'csv' }), '_blank')
}
function exportPDF() {
  window.open(route('admin.task-delegations.export', { type: 'pdf' }), '_blank')
}
function printTable() {
  window.print()
}
</script>

<template>
  <Head title="Task Delegations" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Header & Actions -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold">Task Delegations</h1>
          <p class="text-sm text-muted-foreground">Assign and manage tasks for staff members.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link
            :href="route('admin.task-delegations.create')"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md"
          >
            + Assign Task
          </Link>
          <button @click="exportCSV" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportPDF" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printTable" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
            <Printer class="h-4 w-4" /> Print
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search tasks..."
            class="form-input w-full rounded-md border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500"
          />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" />
          </svg>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-100 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('title')">
                <div class="flex items-center">
                  Title
                  <ArrowUpDown class="inline w-4 h-4 ml-1" />
                </div>
              </th>
              <th class="px-6 py-3">Assigned To</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('due_date')">
                <div class="flex items-center">
                  Due Date
                  <ArrowUpDown class="inline w-4 h-4 ml-1" />
                </div>
              </th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in taskDelegations.data" :key="task.id" class="border-b hover:bg-gray-50">
              <td class="px-6 py-4">{{ task.title }}</td>
              <td class="px-6 py-4">{{ task.assigned_to.first_name }} {{ task.assigned_to.last_name }}</td>
              <td class="px-6 py-4">{{ new Date(task.due_date).toLocaleDateString() }}</td>
              <td class="px-6 py-4">{{ task.status }}</td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center space-x-2">
                  <Link
                    :href="route('admin.task-delegations.edit', { task_delegation: task.id })"
                    class="p-2 rounded-full hover:bg-blue-100 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(task.id)"
                    class="p-2 rounded-full hover:bg-red-100 text-red-600"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="taskDelegations.data.length === 0">
              <td colspan="5" class="py-10 text-center text-muted-foreground">
                No tasks assigned.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Links -->
      <Pagination :links="taskDelegations.links" class="mt-4 flex justify-center" />
    </div>
  </AppLayout>
</template>

<style scoped>
@media print {
  body * { visibility: hidden; }
  table, table * { visibility: visible; position: relative; top: 0; left: 0; width: 100%; }
}
</style>
