<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';

const props = defineProps<{
  invoice: any;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: props.invoice.invoice_number },
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
  <Head :title="`Invoice ${invoice.invoice_number}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-8">
        <div class="flex justify-between items-start mb-8">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">INVOICE</h1>
            <p class="text-gray-500">{{ invoice.invoice_number }}</p>
          </div>
          <div class="text-right">
            <h2 class="text-xl font-semibold">Your Company Name</h2>
            <p class="text-sm text-gray-500">123 Health St, Med-City, 1000</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8">
          <div>
            <h3 class="font-semibold text-gray-500">BILL TO</h3>
            <p class="font-bold">{{ invoice.patient.full_name }}</p>
            <p>{{ invoice.patient.address }}</p>
          </div>
          <div class="text-right">
            <p><span class="font-semibold">Invoice Date:</span> {{ formatDate(invoice.invoice_date) }}</p>
            <p><span class="font-semibold">Due Date:</span> {{ formatDate(invoice.due_date) }}</p>
          </div>
        </div>

        <table class="w-full mb-8">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2 text-left">Service Description</th>
              <th class="p-2 text-right">Cost</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in invoice.items" :key="item.id" class="border-b">
              <td class="p-2">{{ item.description }}</td>
              <td class="p-2 text-right">{{ formatCurrency(item.cost) }}</td>
            </tr>
          </tbody>
        </table>

        <div class="flex justify-end">
          <div class="w-full md:w-1/3 space-y-2">
            <div class="flex justify-between">
              <span class="font-semibold">Subtotal:</span>
              <span>{{ formatCurrency(invoice.subtotal) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-semibold">Tax (15%):</span>
              <span>{{ formatCurrency(invoice.tax_amount) }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
              <span>Grand Total:</span>
              <span>{{ formatCurrency(invoice.grand_total) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>