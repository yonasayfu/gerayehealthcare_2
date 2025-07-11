<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { Download, FileText, Printer, ArrowUpDown, Edit3, Trash2 } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

// Props from controller
const props = defineProps<{
  staff: {
    data: Array<{
      id: number
      first_name: string
      last_name: string
      email: string
      phone: string
      position: string
      department: string
      status: string
    }>
    links: { url: string|null; label: string; active: boolean }[]
  }
  filters: {
    search: string|null
    sort: string
    direction: 'asc'|'desc'
    per_page: number
  }
}>()

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff',     href: route('admin.staff.index') },
]

// Reactive filter + sort + pagination state
const search        = ref(props.filters.search || '')
const sortField     = ref(props.filters.sort)
const sortDirection = ref(props.filters.direction)
const perPage       = ref(props.filters.per_page)

// Watch & debounce to reload index
watch([search, sortField, sortDirection, perPage], debounce(() => {
  router.get(
    route('admin.staff.index'),
    {
      search:    search.value,
      sort:      sortField.value,
      direction: sortDirection.value,
      per_page:  perPage.value,
    },
    { preserveState: true, replace: true }
  )
}, 500))

// Helpers
function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

function destroy(id: number) {
  if (confirm('Delete this staff member?')) {
    router.delete(route('admin.staff.destroy', { staff: id }))
  }
}

function exportCSV() {
  window.open(route('admin.staff.export', { type: 'csv' }), '_blank')
}
function exportPDF() {
  window.open(route('admin.staff.export', { type: 'pdf' }), '_blank')
}
function printTable() {
  window.print()
}
</script>

<template>
  <Head title="Staff Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Header & Actions -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold">Staff</h1>
          <p class="text-sm text-muted-foreground">Manage your team profiles, roles, and statuses.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link
            :href="route('admin.staff.create')"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md"
          >
            + Add Staff
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
            placeholder="Search staff..."
            class="form-input w-full rounded-md border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500"
          />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" />
          </svg>
        </div>
        <div>
          <label for="perPage" class="mr-2 text-sm">Per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-100 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('full_name')">
                Name <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('email')">
                Email <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('phone')">
                Phone <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3">Position</th>
              <th class="px-6 py-3">Department</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in staff.data" :key="member.id" class="border-b hover:bg-gray-50">
              <td class="px-6 py-4">{{ member.first_name }} {{ member.last_name }}</td>
              <td class="px-6 py-4">{{ member.email }}</td>
              <td class="px-6 py-4">{{ member.phone }}</td>
              <td class="px-6 py-4">{{ member.position }}</td>
              <td class="px-6 py-4">{{ member.department }}</td>
              <td class="px-6 py-4">{{ member.status }}</td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center space-x-2">
                  <Link
                    :href="route('admin.staff.edit', { staff: member.id })"
                    class="p-2 rounded-full hover:bg-blue-100 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(member.id)"
                    class="p-2 rounded-full hover:bg-red-100 text-red-600"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="staff.data.length === 0">
              <td colspan="7" class="py-10 text-center text-muted-foreground">
                No staff found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Links -->
      <Pagination :links="staff.links" class="mt-4 flex justify-center" />
    </div>
  </AppLayout>
</template>

<style scoped>
@media print {
  body * { visibility: hidden; }
  table, table * { visibility: visible; position: relative; top: 0; left: 0; width: 100%; }
}
</style>
