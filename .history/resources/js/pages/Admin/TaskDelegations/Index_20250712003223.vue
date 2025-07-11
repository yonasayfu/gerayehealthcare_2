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
      assignee: { first_name: string; last_name: string }
      due_date: string
      status: string
    }>
    links: { url: string | null; label: string; active: boolean }[]
  }
  filters: {
    search: string | null
    sort_by: string
    sort_order: 'asc' | 'desc'
  }
}>()

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Task Delegations', href: route('admin.task-delegations.index') },
]

// Filters & sort
const search    = ref(filters.search || '')
const sortBy    = ref(filters.sort_by)
const sortOrder = ref(filters.sort_order)

watch([search, sortBy, sortOrder], debounce(() => {
  router.get(
    route('admin.task-delegations.index'),
    { search: search.value, sort_by: sortBy.value, sort_order: sortOrder.value },
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
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
          >+ Assign Task</Link>
          <button @click="exportCSV" class="btn-outline">CSV</button>
          <button @click="exportPDF" class="btn-outline">PDF</button>
          <button @click="printTable" class="btn-outline">Print</button>
        </div>
      </div>

      <!-- Search -->
      <div class="w-full md:w-1/3">
        <input
          v-model="search"
          placeholder="Search tasks..."
          class="w-full rounded border px-3 py-2"
        />
      </div>

      <!-- Table -->
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
                {{ task.assignee.first_name }} {{ task.assignee.last_name }}
              </td>
              <td class="px-6 py-4">{{ new Date(task.due_date).toLocaleDateString() }}</td>
              <td class="px-6 py-4">{{ task.status }}</td>
              <td class="px-6 py-4 text-right">
                <Link
                  :href="route('admin.task-delegations.edit', { task_delegation: task.id })"
                  class="text-blue-600 mr-2"
                  title="Edit"
                ><Edit3 /></Link>
                <button @click="destroy(task.id)" class="text-red-600" title="Delete">
                  <Trash2 />
                </button>
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

      <!-- Pagination -->
      <Pagination
        v-if="taskDelegations.data.length"
        :links="taskDelegations.links"
        class="mt-4 flex justify-center"
      />
    </div>
  </AppLayout>
</template>

<style scoped>
.btn-outline {
  @apply px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm;
}
@media print {
  body * { visibility: hidden; }
  table, table * { visibility: visible; position: relative; top: 0; left: 0; width: 100%; }
}
</style>
