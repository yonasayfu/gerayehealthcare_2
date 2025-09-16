<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import { Plus, Printer, Download, Check, Eye, Search } from 'lucide-vue-next';
import { computed } from 'vue';
import Pagination from '@/components/Pagination.vue'
import { useTableFilters } from '@/composables/useTableFilters'

const props = defineProps<{
  invoices: any;
  filters?: { search?: string; sort?: string; direction?: 'asc'|'desc'; per_page?: number }
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
];

const formatCurrency = (value: number | string) => {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const formatDate = (dateString: string) => {
  return format(new Date(dateString), 'MMM dd, yyyy');
};

const formattedGeneratedDate = computed(() => format(new Date(), 'PPP p'));

// Search + pagination (non-breaking; URL params preserved)
const { search, perPage, toggleSort } = useTableFilters({
  routeName: 'admin.invoices.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort,
    direction: props.filters?.direction,
    per_page: props.filters?.per_page ?? props.invoices?.per_page ?? 10,
  }
})

function printCurrentView() {
  setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100);
}

function approveInvoice(id: number) {
  const form = useForm({});
  form.post(route('admin.invoices.approve', id));
}

</script>

<template>
  <Head title="Invoices" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Invoices</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Review and manage all patient invoices</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.invoices.create')" class="btn-glass">
              <span>Create Invoice</span>
            </Link>
            <a :href="route('admin.invoices.export')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </a>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
            <a :href="route('admin.invoices.printAll')" class="btn-glass btn-glass-sm" target="_blank">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print All</span>
            </a>
            <Link :href="route('admin.invoices.incoming')" class="btn-glass btn-glass-sm">
              <span>Incoming</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search invoices..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>
        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Invoice List (Current View)</p>
        </div>

        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-100 print-table">
          <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-600 dark:text-gray-300 print-table-header">
            <tr>
              <th class="px-6 py-3">Invoice #</th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Invoice Date</th>
              <th class="px-6 py-3">Due Date</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 print-table-row">
              <td class="px-6 py-4 text-gray-700 dark:text-gray-200">
                <Link :href="route('admin.invoices.show', invoice.id)" class="text-indigo-600 hover:underline font-semibold">{{ invoice.invoice_number }}</Link>
              </td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ invoice.patient.full_name }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ formatDate(invoice.invoice_date) }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ formatDate(invoice.due_date) }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ formatCurrency(invoice.grand_total) }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                  'bg-yellow-100 text-yellow-800': invoice.status === 'Pending',
                  'bg-blue-100 text-blue-800': invoice.status === 'Issued',
                  'bg-green-100 text-green-800': invoice.status === 'Paid',
                  'bg-purple-100 text-purple-800': invoice.status === 'Approved',
                  'bg-red-100 text-red-800': invoice.status === 'Overdue',
                }">
                  {{ invoice.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.invoices.show', invoice.id)"
                    class="btn-icon text-indigo-600"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <button
                    v-if="invoice.status === 'Issued' || invoice.status === 'Pending'"
                    @click.prevent="approveInvoice(invoice.id)"
                    class="btn-icon text-green-600 hover:bg-green-50 dark:hover:bg-green-900"
                    title="Approve Invoice"
                  >
                    <Check class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="invoices.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400 dark:text-gray-400">No invoices found.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between gap-3 print:hidden">
        <div class="text-sm text-gray-600 dark:text-gray-300">
          <span v-if="invoices && (invoices.from !== undefined && invoices.to !== undefined && invoices.total !== undefined)">
            Showing {{ invoices.from }}–{{ invoices.to }} of {{ invoices.total }}
          </span>
        </div>
        <Pagination :links="invoices.links" />
      </div>
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
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

  html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
  .print-header-content { page-break-inside: avoid; }
  .print-logo { display: inline-block; margin: 0 auto 6px auto; max-width: 100%; height: auto; }
  .print-clinic-name { font-size: 16px; margin: 0; }
  .print-document-title { font-size: 12px; margin: 2px 0 0 0; }

  /* Table */
  .print-table { font-size: 11px; border-collapse: collapse; }
  .print-table th, .print-table td { border: 1px solid #d1d5db; padding: 6px 8px; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }

  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: none !important; }
  hr { display: none !important; }
}
</style>
