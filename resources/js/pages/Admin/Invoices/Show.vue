<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import { Printer, Share2, Download } from 'lucide-vue-next';

const props = defineProps<{
  invoice: any;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: props.invoice.invoice_number, href: route('admin.invoices.show', props.invoice.id) },
];

const formatCurrency = (value: number | string) => {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const formatDate = (dateString: string) => {
  return format(new Date(dateString), 'MMM dd, yyyy');
};

const copyShareLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard');
  } catch (e) {
    alert('Failed to copy link. You can copy the URL from the address bar.');
  }
};

// Fetch a public signed PDF URL from the backend
const getPublicShareLink = async (invoiceId: number | string): Promise<string> => {
  const url = route('admin.invoices.shareLink', invoiceId as any) as string;
  const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
  if (!res.ok) throw new Error('Failed to generate share link');
  const data = await res.json();
  return data.url as string;
};

const shareViaTelegram = async () => {
  try {
    const publicUrl = await getPublicShareLink(props.invoice.id);
    const text = encodeURIComponent(`Invoice ${props.invoice.invoice_number}`);
    const tgUrl = `https://t.me/share/url?url=${encodeURIComponent(publicUrl)}&text=${text}`;
    window.open(tgUrl, '_blank');
  } catch (e) {
    alert('Could not open Telegram share.');
  }
};

const copyPublicLink = async () => {
  try {
    const publicUrl = await getPublicShareLink(props.invoice.id);
    await navigator.clipboard.writeText(publicUrl);
    alert('Public link copied to clipboard');
  } catch (e) {
    alert('Failed to generate public link');
  }
};

const downloadUrl = computed(() => `${route('admin.invoices.print', props.invoice.id)}?download=1`);

// Share dropdown state
const showShare = ref(false);
const toggleShare = () => (showShare.value = !showShare.value);
const closeShare = () => (showShare.value = false);

const printCurrent = () => {
  setTimeout(() => window.print(), 30);
};
</script>

<template>
  <Head :title="`Invoice ${invoice.invoice_number}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">
      <!-- compact liquid glass header -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Invoice Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Invoice: {{ invoice.invoice_number }}</p>
          </div>
          <!-- top actions removed to avoid duplication; see bottom action bar -->
        </div>
      </div>

      <div class="p-6 space-y-6">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">Invoice</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h4 class="font-semibold mb-2">Bill To:</h4>
            <div class="text-sm text-gray-600 dark:text-gray-300">
              <p>{{ invoice.patient?.first_name }} {{ invoice.patient?.last_name }}</p>
              <p>{{ invoice.patient?.address }}</p>
              <p>{{ invoice.patient?.phone }}</p>
              <p>{{ invoice.patient?.email }}</p>
            </div>
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

      <div class="p-6 border-t border-gray-200 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.invoices.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="printCurrent" class="btn-glass btn-glass-sm">
            <Printer class="icon" />
            <span class="hidden sm:inline">Print Current</span>
          </button>
          <a :href="downloadUrl" class="btn-glass btn-glass-sm">
            <Download class="icon" />
            <span class="hidden sm:inline">Download PDF</span>
          </a>
        </div>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>
    </div>
  </AppLayout>
</template>

<style>
@page { size: A4 portrait; margin: 12mm; }
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