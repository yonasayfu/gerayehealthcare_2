<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown } from 'lucide-vue-next'
import debounce from 'lodash/debounce'

const props = defineProps<{ staff: any; filters: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Staff', href: '/dashboard/staff' },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 10)

watch([search, sortField, sortDirection, perPage], debounce(() => {
  router.get('/dashboard/staff', {
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
  if (confirm('Are you sure you want to delete this staff?')) {
    router.delete(route('admin.staff.destroy', id))
  }
}

function exportData(type: 'csv' | 'pdf') {
  const form = document.createElement('form');
  form.method = 'GET';
  form.action = `/dashboard/staff/export`;
  form.target = '_blank';

  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'type';
  input.value = type;
  form.appendChild(input);

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
}

function printTable() {
  window.print()
}

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}
</script>

<template>
  <Head title="Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff</h1>
          <p class="text-sm text-muted-foreground">Manage all staff records here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link href="/dashboard/staff/create" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Staff
          </Link>
          <button @click="() => exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="() => exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printTable" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print
          </button>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search staff..."
            class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
          />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" />
          </svg>
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('first_name')">
                First Name <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('last_name')">
                Last Name <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('email')">
                Email <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3">Phone</th>
              <th class="px-6 py-3">Position</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in staff.data" :key="member.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ member.first_name }}</td>
              <td class="px-6 py-4">{{ member.last_name }}</td>
              <td class="px-6 py-4">{{ member.email ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.phone ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.position ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.status ?? '-' }}</td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('staff.show', member.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-indigo-600" title="View">
  <FileText class="w-4 h-4" />
</Link>

                  <Link :href="route('admin.staff.edit', member.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(member.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="staff.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400">No staff found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-end">
        <div class="flex items-center space-x-1">
          <Link
            v-for="(link, i) in staff.links"
            :key="i"
            :href="link.url || ''"
            v-html="link.label"
            :class="[
              'px-3 py-1 rounded-md text-sm',
              link.active ? 'bg-primary-600 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700',
              !link.url && 'cursor-not-allowed text-gray-400'
            ]"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  body * {
    visibility: hidden !important;
  }
  #print-header, #print-header * {
    visibility: visible !important;
  }
  table, table * {
    visibility: visible !important;
  }
  table {
    position: relative !important;
    top: 0;
    left: 0;
    width: 100%;
    margin-top: 20px;
  }
  .print-only {
    display: block !important;
  }
}
</style>