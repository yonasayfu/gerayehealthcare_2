<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import { Plus, Printer, Download } from 'lucide-vue-next';

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
</script>

<template>
  <Head title="Invoices" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold">Invoices</h1>
          <p class="text-sm text-muted-foreground">Review and manage all patient invoices.</p>
        </div>
        <div class="flex items-center gap-2">
          <a :href="route('admin.invoices.export')" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-3 py-2 rounded-md transition">
            <Download class="h-4 w-4" /> Export CSV
          </a>
          <a :href="route('admin.invoices.printAll')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-3 py-2 rounded-md transition" target="_blank">
            <Printer class="h-4 w-4" /> Print All
          </a>
          <Link :href="route('admin.invoices.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            <Plus class="h-4 w-4" /> Create Invoice
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
            <tr>
              <th class="px-6 py-3">Invoice #</th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Invoice Date</th>
              <th class="px-6 py-3">Due Date</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b dark:border-gray-700">
              <td class="px-6 py-4">
                <Link :href="route('admin.invoices.show', invoice.id)" class="text-indigo-600 hover:underline font-semibold">{{ invoice.invoice_number }}</Link>
              </td>
              <td class="px-6 py-4">{{ invoice.patient.full_name }}</td>
              <td class="px-6 py-4">{{ formatDate(invoice.invoice_date) }}</td>
              <td class="px-6 py-4">{{ formatDate(invoice.due_date) }}</td>
              <td class="px-6 py-4">{{ formatCurrency(invoice.grand_total) }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                  'bg-yellow-100 text-yellow-800': invoice.status === 'Pending',
                  'bg-green-100 text-green-800': invoice.status === 'Paid',
                  'bg-red-100 text-red-800': invoice.status === 'Overdue',
                }">
                  {{ invoice.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-3">
                <Link :href="route('admin.invoices.show', invoice.id)" class="text-sm text-indigo-600">View</Link>
                <a :href="route('admin.invoices.print', invoice.id)" class="text-sm text-gray-600" target="_blank">Print</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>