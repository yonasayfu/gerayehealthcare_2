<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, ArrowUpDown, Eye, Search, Printer } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { confirmDialog } from '@/lib/confirm'

import type { EmployeeInsuranceRecordPagination } from '@/types';

const props = defineProps<{
  employeeInsuranceRecords: EmployeeInsuranceRecordPagination;
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance', href: route('admin.employee-insurance-records.index') },
  { title: 'Employee Insurance Records', href: route('admin.employee-insurance-records.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.employee-insurance-records.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Employee Insurance Record',
    message: 'Are you sure you want to delete this employee insurance record?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.employee-insurance-records.destroy', id))
}

function exportCsv() {
  window.open(route('admin.employee-insurance-records.export', { type: 'csv' }), '_blank');
}

function printCurrentView() { setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100); }

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
  <Head title="Employee Insurance Records" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="liquidGlass-wrapper relative overflow-hidden rounded-xl p-5 print:hidden">
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-white/10 to-white/5"></div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Employee Insurance Records</h1>
            <p class="text-sm text-muted-foreground">Manage all employee insurance records here.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link :href="route('admin.employee-insurance-records.create')" class="btn-glass btn-glass-sm inline-flex items-center gap-2">
              + Add Record
            </Link>
            <button @click="exportCsv" class="btn-glass btn-glass-sm inline-flex items-center gap-1">
              <Download class="h-4 w-4" /> CSV
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm inline-flex items-center gap-1">
              <Printer class="h-4 w-4" /> Print Current
            </button>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search records..."
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

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

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Employee Insurance Records List (Current View)</p>
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('kebele_id')">
                Kebele ID <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('woreda')">
                Woreda <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('region')">
                Region <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('federal_id')">
                Fayda ID <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('ministry_department')">
                Ministry Department <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('employee_id_number')">
                Employee ID Number <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('verified')">
                Verified <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in employeeInsuranceRecords.data" :key="record.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ record.kebele_id ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.woreda ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.region ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.federal_id ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.ministry_department ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.employee_id_number ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.verified ? 'Yes' : 'No' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.employee-insurance-records.show', record.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.employee-insurance-records.edit', record.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(record.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="employeeInsuranceRecords.data.length === 0">
              <td colspan="8" class="text-center px-6 py-4 text-gray-400">No employee insurance records found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="employeeInsuranceRecords.data.length > 0" :links="employeeInsuranceRecords.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
        <p>Printed on: {{ formattedGeneratedDate }}</p>
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
<style>
@page { size: A4 landscape; margin: 12mm; }
@media print {
  html, body { background: #fff !important; }
  .print-header-content { page-break-inside: avoid; }
  .print-logo { display: inline-block; margin: 0 auto 6px auto; max-width: 100%; height: auto; }
  .print-clinic-name { font-size: 16px; margin: 0; }
  .print-document-title { font-size: 12px; margin: 2px 0 0 0; }
  table { border-collapse: collapse; }
  hr { display: none !important; }
  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: none !important; }
}
</style>
