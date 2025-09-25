<script setup lang="ts">
import { ref, computed } from 'vue';
import { confirmDialog } from '@/lib/confirm'

import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { format } from 'date-fns';
import type { SupplierPagination } from '@/types';
import { useTableFilters } from '@/composables/useTableFilters'

interface SupplierFilters {
  search?: string;
  sort?: string;
  direction?: 'asc' | 'desc';
  per_page?: number;
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
];

const props = defineProps<{
  suppliers: SupplierPagination;
  filters: SupplierFilters;
}>();

const { search, perPage, toggleSort } = useTableFilters({
  routeName: 'admin.suppliers.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort ?? 'name',
    direction: props.filters?.direction ?? 'asc',
    per_page: props.filters?.per_page ?? props.suppliers?.per_page ?? 5,
  }
})

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

// URL updates handled by composable

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Supplier',
    message: 'Are you sure you want to delete this supplier?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.suppliers.destroy', id));
}

function printCurrentView() {
  setTimeout(() => {
    try { window.print(); } catch (e) { console.error('Print failed', e); }
  }, 100);
}

const printAllSuppliers = () => {
    const url = route('admin.suppliers.printAll', { ...props.filters, preview: 1 });
    window.open(url, '_blank');
};

function onToggleSort(field: string) { toggleSort(field) }
</script>

<template>
  <Head title="Suppliers" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Suppliers</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage suppliers</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.suppliers.create')" class="btn-glass">
              <span>Add Supplier</span>
            </Link>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
                <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Items per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-cyan-600 bg-cyan-600 text-white sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-700 dark:border-gray-700">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent print:w-[95%] print:mx-auto print:overflow-visible">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Suppliers List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('name')">
                Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('contact_person')">
                Contact Person <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('email')">
                Email <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('phone')">
                Phone <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="supplier in suppliers.data" :key="supplier.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ supplier.name }}</td>
              <td class="px-6 py-4">{{ supplier.contact_person ?? '-' }}</td>
              <td class="px-6 py-4">{{ supplier.email ?? '-' }}</td>
              <td class="px-6 py-4">{{ supplier.phone ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.suppliers.show', supplier.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.suppliers.edit', supplier.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(supplier.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="suppliers.data.length === 0">
              <td colspan="5" class="text-center px-6 py-4 text-gray-400">No suppliers found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="suppliers.data.length > 0" :links="suppliers.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p>
      </div>

    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page { size: A4 landscape; margin: 0.5cm; }

  /* Hide app chrome */
  .app-sidebar-header, .app-sidebar { display: none !important; }
  body > header, body > nav, [role="banner"], [role="navigation"] { display: none !important; }

  html, body {
    margin: 0 !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* Prevent cutting rows across pages and repeat headers */
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }

  /* Maintain readable sizes and spacing */
  .print-table { font-size: 12px !important; line-height: 1.35; }
  .print-table th, .print-table td { padding: 8px 10px !important; vertical-align: top; }
  .print-table-header { background: #f3f4f6 !important; -webkit-print-color-adjust: exact; }

  /* Word wrapping to prevent overflow */
  .print-table td, .print-table th { word-break: break-word; }

  /* Center main print container */
  .print\:mx-auto { margin-left: auto !important; margin-right: auto !important; }
  .print\:w-\[95\%\] { width: 95% !important; }
}
</style>
