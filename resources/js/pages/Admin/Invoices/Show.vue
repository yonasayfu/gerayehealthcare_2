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
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Invoice {{ invoice.invoice_number }}
            </h3>
            <Link :href="route('admin.invoices.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
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

        <div class="p-6 border-t border-gray-200 rounded-b">
            <!-- Add any buttons or actions here if needed -->
        </div>

    </div>
  </AppLayout>
</template>