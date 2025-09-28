<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { confirmDialog } from '@/lib/confirm'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

// types imported as needed

const props = defineProps<{
  insuranceCompanies: { // Define a more robust type for insuranceCompanies
    data: Array<any>;
    links: Array<any>;
    current_page: number;
    from: number;
    last_page: number;
    per_page: number;
    to: number;
    total: number;
  };
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

// Note: Do not mutate props. Assume backend provides the prop; handle empties defensively in template.

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance', href: route('admin.insurance-companies.index') },
  { title: 'Companies', href: route('admin.insurance-companies.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)

const canPrintCurrent = computed(() => {
  return (props.insuranceCompanies?.data || []).length > 0
})

function clearSearch() {
  search.value = ''
  // focus back to input after clearing
  const el = document.getElementById('companySearch') as HTMLInputElement | null
  el?.focus()
}

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

  router.get(route('admin.insurance-companies.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Insurance Company',
    message: 'Are you sure you want to delete this insurance company?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.insurance-companies.destroy', id))
}

function exportData(type: 'csv' | 'pdf') {
  window.open(route('admin.insurance-companies.export', { type }), '_blank');
}

function printCurrentView() { setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100); }

const printAllCompanies = () => {
    // Use centralized backend handler for printing all records
    window.open(route('admin.insurance-companies.printAll'), '_blank');
};

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
  <Head title="Insurance Companies" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Insurance Companies</h1>
          <p class="text-sm text-muted-foreground">Manage all insurance companies here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.insurance-companies.create')" class="btn-glass btn-glass-sm">
            + Add Company
          </Link>
          <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="printCurrentView" :disabled="!canPrintCurrent" class="btn-glass btn-glass-sm disabled:opacity-50 disabled:cursor-not-allowed" title="Preview printable PDF of the current page">
            <Printer class="h-4 w-4" /> Print Current
          </button>
          <button @click="printAllCompanies" class="btn-glass btn-glass-sm">
            <Printer class="h-4 w-4" /> Print All
          </button>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <label for="companySearch" class="sr-only">Search companies</label>
          <input
            id="companySearch"
            type="text"
            v-model="search"
            @keyup.enter.prevent
            placeholder="Search companies by name and ..."
            aria-label="Search companies"
            role="searchbox"
            class="shadow-sm bg-gray-50 pl-9 pr-8 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
          />
          <Search class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
          <button
            v-if="search"
            @click="clearSearch"
            type="button"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            aria-label="Clear search"
            title="Clear search"
          >
            ×
          </button>
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-cyan-600 bg-cyan-600 text-white sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-700 dark:border-gray-700">
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Insurance Companies List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('name')">
                Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('contact_person')">
                Contact Person <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('contact_email')">
                Contact Email <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('contact_phone')">
                Contact Phone <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('address')">
                Address <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="company in insuranceCompanies.data || []" :key="company.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ company.name }}</td>
              <td class="px-6 py-4">{{ company.contact_person ?? '-' }}</td>
              <td class="px-6 py-4">{{ company.contact_email ?? '-' }}</td>
              <td class="px-6 py-4">{{ company.contact_phone ?? '-' }}</td>
              <td class="px-6 py-4">{{ company.address ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.insurance-companies.show', company.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.insurance-companies.edit', company.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(company.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="(insuranceCompanies.data || []).length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No insurance companies found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="(insuranceCompanies.data || []).length > 0" :links="insuranceCompanies.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page { size: A4 landscape; margin: 0.5cm; }
  /* Hide app chrome */
  .app-sidebar-header, .app-sidebar { display: none !important; }
  body > header, body > nav, [role="banner"], [role="navigation"] { display: none !important; }
  html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }

  /* Keep table headers and rows intact across pages */
  table { border-collapse: collapse; width: 100%; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }
  img { page-break-inside: avoid; }
}
</style>
