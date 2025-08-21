<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import { Plus, Printer, Download, Check } from 'lucide-vue-next';

defineProps<{
  invoices: any;
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
            <Link :href="route('admin.invoices.incoming')" class="btn-glass btn-glass-sm">
              <span>Incoming</span>
            </Link>
            <a :href="route('admin.invoices.export')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </a>
            <a :href="route('admin.invoices.printAll')" class="btn-glass btn-glass-sm" target="_blank">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print All</span>
            </a>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Invoice List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
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
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <button
                    v-if="invoice.status === 'Issued' || invoice.status === 'Pending'"
                    @click.prevent="approveInvoice(invoice.id)"
                    class="inline-flex items-center p-2 rounded-md text-green-600 hover:bg-green-50 dark:text-green-300 dark:hover:bg-gray-700"
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

    </div>
  </AppLayout>
</template>