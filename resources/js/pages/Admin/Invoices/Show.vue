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
  { title: props.invoice.invoice_number },
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
</script>

<template>
  <Head :title="`Invoice ${invoice.invoice_number}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Invoice Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Invoice: {{ item.name || item.title || item.id }}</p>
          </div>
          <!-- top actions intentionally removed to avoid duplication; see footer -->
        </div>
      </div>
                </div>
              </div>
              <Link :href="route('admin.invoices.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
              </Link>
            </div>
        </div>

        <div class="p-6 space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Invoice Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Invoice: {{ item.name || item.title || item.id }}</p>
          </div>
          <!-- top actions intentionally removed to avoid duplication; see footer -->
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
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <!-- Add any buttons or actions here if needed -->
        </div>

    </div>
  </AppLayout>
</template>