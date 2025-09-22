<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import type { BreadcrumbItemType } from '@/types';
import { Eye, Printer, Shield, Stethoscope } from 'lucide-vue-next';

// Import the components correctly (they use script setup)
import AppLayout from '@/layouts/AppLayout.vue';
import ShowHeader from '@/components/ShowHeader.vue';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

interface Partner {
  id: number;
  name: string;
}

interface SharedInvoice {
  id: number;
  partnerId: number;
  partnerName: string;
  status: string;
  sharedByName: string;
  shareDate: string;
  expiresAt: string;
  views: number;
}

const props = defineProps<{
  invoice: any;
  partners: Partner[];
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: props.invoice.invoice_number, href: route('admin.invoices.show', props.invoice.id) },
];

const isShareOpen = ref(false);
const isSharing = ref(false);
const shareSuccessMessage = ref('');
const shareErrorMessage = ref('');

// Create a proper form interface
interface ShareForm {
  invoice_id: number;
  partner_id: number | null;
  share_date: string;
  status: string;
  notes: string;
}

const shareForm = useForm<ShareForm>({
  invoice_id: props.invoice.id,
  partner_id: null,
  share_date: new Date().toISOString().split('T')[0],
  status: 'Shared',
  notes: ''
});

const sharedList = ref<SharedInvoice[]>([]);

onMounted(() => {
  fetchSharedList();
});

function printCurrent() {
  window.print();
}

async function quickShare() {
  if (!shareForm.partner_id) {
    shareErrorMessage.value = 'Please select a partner';
    return;
  }
  
  isSharing.value = true;
  shareErrorMessage.value = '';
  shareSuccessMessage.value = '';
  
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    const response = await fetch(route('admin.shared-invoices.store'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        invoice_id: shareForm.invoice_id,
        partner_id: shareForm.partner_id,
        share_date: shareForm.share_date,
        status: shareForm.status,
        notes: shareForm.notes,
      }),
    });
    
    const data = await response.json();
    
    if (response.ok) {
      shareSuccessMessage.value = 'Invoice shared successfully with the partner!';
      isShareOpen.value = false;
      shareForm.reset();
      // Refresh the shared list
      fetchSharedList();
      
      // Show success message to user
      alert('Invoice shared successfully with the partner!');
    } else {
      const errorMessage = data.message || data.errors?.partner_id || data.errors?.notes || 'Failed to share invoice. Please try again.';
      shareErrorMessage.value = errorMessage;
      alert('Error: ' + errorMessage);
    }
  } catch (error) {
    console.error('Share error:', error);
    const errorMessage = 'An error occurred while sharing the invoice. Please try again.';
    shareErrorMessage.value = errorMessage;
    alert('Error: ' + errorMessage);
  } finally {
    isSharing.value = false;
  }
}

// Add function to fetch shared list
async function fetchSharedList() {
  try {
    const response = await fetch(route('admin.shared-invoices.index', { invoice_id: props.invoice.id }), {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    });
    
    if (response.ok) {
      const data = await response.json();
      // Transform the data to match our SharedInvoice interface
      sharedList.value = data.sharedInvoices.data.map((item: any) => ({
        id: item.id,
        partnerId: item.partner_id,
        partnerName: item.partner?.name || 'N/A',
        status: item.status,
        sharedByName: item.shared_by ? `${item.shared_by.first_name} ${item.shared_by.last_name}` : 'N/A',
        shareDate: item.share_date,
        expiresAt: item.share_expires_at,
        views: item.share_views
      }));
    } else {
      console.error('Failed to fetch shared invoices');
    }
  } catch (error) {
    console.error('Error fetching shared invoices:', error);
  }
}

function formatDate(dateString: string) {
  if (!dateString) return '—';
  return format(new Date(dateString), 'PPP');
}

function formatCurrency(amount: number) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'ETB',
  }).format(amount);
}

const providerNames = ref<string[]>([]);
</script>

<template>
  <Head :title="`Invoice ${invoice.invoice_number}`" />
  <!-- Use AppSidebarLayout instead of AppLayout to match the import -->
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative">
        <!-- Use ShowHeader component correctly -->
        <ShowHeader title="Invoice Details" :subtitle="`Invoice: ${invoice.invoice_number}`">
          <template #actions>
            <Button as-child variant="outline" size="sm" class="mr-2">
              <Link :href="route('admin.invoices.index')">Back</Link>
            </Button>
            <Button size="sm" @click="isShareOpen = true">Share Invoice</Button>
          </template>
        </ShowHeader>

        <div class="p-6 space-y-6">
          <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0 print:print-document">
            <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
              <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
              <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
              <p class="text-gray-600 dark:text-gray-400 print-document-title">Invoice</p>
              <hr class="my-3 border-gray-300 print:my-2">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold mb-2">Bill To:</h4>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                  <p>{{ invoice.patient?.first_name }} {{ invoice.patient?.last_name }}</p>
                  <p>{{ invoice.patient?.address }}</p>
                  <p>{{ invoice.patient?.phone_number }}</p>
                  <p>{{ invoice.patient?.email }}</p>
                </div>
              </div>
              <div class="text-right space-y-1">
                <p><span class="font-semibold">Invoice Date:</span> {{ formatDate(invoice.invoice_date) }}</p>
                <p><span class="font-semibold">Due Date:</span> {{ formatDate(invoice.due_date) }}</p>
                <p v-if="invoice.status"><span class="font-semibold">Status:</span> {{ invoice.status }}</p>
                <p v-if="invoice.insurance_company">
                  <span class="inline-flex items-center gap-1 font-semibold"><Shield class="h-4 w-4" /> Insurance:</span>
                  <span class="ml-1">{{ invoice.insurance_company.name }}</span>
                </p>
                <p v-if="providerNames.length">
                  <span class="inline-flex items-center gap-1 font-semibold"><Stethoscope class="h-4 w-4" /> Provider(s):</span>
                  <span class="ml-1">{{ providerNames.join(', ') }}</span>
                </p>
              </div>
            </div>

            <table class="w-full mb-8 border-collapse print:mb-4 print:print-table">
              <thead class="bg-gray-100 print:bg-gray-200">
                <tr>
                  <th class="p-2 text-left border print:p-1">Service Description</th>
                  <th class="p-2 text-right border print:p-1">Cost</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in invoice.items" :key="item.id" class="border-b print:border-gray-300">
                  <td class="p-2 border print:p-1">{{ item.description }}</td>
                  <td class="p-2 text-right border print:p-1">{{ formatCurrency(item.cost) }}</td>
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
                <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2 print:border-gray-300 print:pt-1 print:mt-1">
                  <span>Grand Total:</span>
                  <span>{{ formatCurrency(invoice.grand_total) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
          <div class="flex justify-end gap-2">
            <button @click="printCurrent" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>

        <!-- Quick Share to Partner -->
        <Dialog :open="isShareOpen" @update:open="isShareOpen = $event">
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Share Invoice</DialogTitle>
              <DialogDescription>Select a partner and optionally add a note.</DialogDescription>
            </DialogHeader>
            <div class="space-y-4">
              <div v-if="shareSuccessMessage" class="text-sm text-green-600 bg-green-50 p-2 rounded">
                {{ shareSuccessMessage }}
              </div>
              <div v-if="shareErrorMessage" class="text-sm text-red-600 bg-red-50 p-2 rounded">
                {{ shareErrorMessage }}
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Partner</label>
                <select v-model.number="shareForm.partner_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                  <option :value="null">Select partner…</option>
                  <option v-for="p in (partners || [])" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
                <div v-if="shareForm.errors?.partner_id" class="text-xs text-red-500 mt-1">{{ shareForm.errors.partner_id }}</div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Notes (optional)</label>
                <input v-model="shareForm.notes" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800" placeholder="Add a note" />
                <div v-if="shareForm.errors && shareForm.errors.notes" class="text-xs text-red-500 mt-1">{{ shareForm.errors.notes }}</div>
              </div>
            </div>
            <DialogFooter>
              <Button variant="outline" @click="isShareOpen = false" :disabled="isSharing">Cancel</Button>
              <Button @click="quickShare" :disabled="isSharing || !shareForm.partner_id">
                {{ isSharing ? 'Sharing...' : 'Share' }}
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>

        <!-- Partner Shares -->
        <div v-if="sharedList.length" class="px-6 pb-6 print:hidden">
          <h3 class="text-md font-semibold mb-3">Partner Shares</h3>
          <div class="overflow-x-auto bg-white dark:bg-gray-800 border rounded-md">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr>
                  <th class="p-2">Partner</th>
                  <th class="p-2">Status</th>
                  <th class="p-2">Shared By</th>
                  <th class="p-2">Share Date</th>
                  <th class="p-2">Expires</th>
                  <th class="p-2">Views</th>
                  <th class="p-2 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in sharedList" :key="s.id" class="border-t">
                  <td class="p-2">
                    <Link v-if="s.partnerId" :href="route('admin.partners.show', s.partnerId)" class="text-indigo-600 hover:underline">{{ s.partnerName }}</Link>
                    <span v-else>{{ s.partnerName }}</span>
                  </td>
                  <td class="p-2">{{ s.status }}</td>
                  <td class="p-2">{{ s.sharedByName }}</td>
                  <td class="p-2">{{ s.shareDate ? format(new Date(s.shareDate), 'PPP p') : '—' }}</td>
                  <td class="p-2">{{ s.expiresAt ? format(new Date(s.expiresAt), 'PPP p') : '—' }}</td>
                  <td class="p-2">{{ s.views }}</td>
                  <td class="p-2 text-right">
                    <Link v-if="s.id" :href="route('admin.shared-invoices.show', s.id)" class="inline-flex items-center gap-1 text-indigo-600">
                      <Eye class="h-4 w-4" /> View
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
          <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<style>
@media print {
  @page {
    size: A4 portrait;
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }

  /* Hide application chrome */
  .app-sidebar-header, .app-sidebar,
  body > header, body > nav,
  [role="banner"], [role="navigation"] {
    display: none !important;
  }

  .hidden.print\:block { display: block !important; }

  /* Print header styling - using caregiver print style */
  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
    text-align: center !important;
    page-break-after: avoid !important;
  }

  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .print-clinic-name { 
    font-size: 1.8rem !important; 
    margin-bottom: 0.2rem !important; 
    line-height: 1.2 !important; 
    font-weight: bold !important;
  }
  
  .print-document-title { 
    font-size: 0.9rem !important; 
    color: #555 !important; 
  }

  /* Main content styling */
  .bg-white.dark\:bg-gray-800.border.border-gray-200.dark\:border-gray-700.rounded-lg.shadow.relative {
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    padding: 1cm !important;
    margin: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    height: auto !important;
    overflow: visible !important;
    page-break-inside: avoid !important;
  }

  .p-6.space-y-6 { 
    padding: 0 !important; 
    margin: 0 !important; 
  }

  /* Print document container */
  .print-document {
    transform: none !important;
    transform-origin: top left !important;
    max-width: 100% !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
    border-radius: 0 !important;
  }

  /* Grid layout for print */
  .grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 1rem !important;
    page-break-inside: avoid !important;
  }

  /* Table styling */
  table {
    border-collapse: collapse;
    width: 100% !important;
    margin-bottom: 1cm !important;
    page-break-inside: auto !important;
  }

  thead {
    display: table-header-group !important;
  }

  th, td {
    border: 1px solid #d1d5db !important;
    padding: 8px !important;
    text-align: left !important;
    page-break-inside: avoid !important;
  }

  th {
    background-color: #f3f4f6 !important;
    font-weight: bold !important;
  }

  tr {
    page-break-inside: avoid !important;
  }

  /* Print table class */
  .print-table {
    border-collapse: collapse !important;
    width: 100% !important;
    margin-bottom: 1cm !important;
  }

  .print-table th, .print-table td {
    border: 1px solid #d1d5db !important;
    padding: 6px !important;
    text-align: left !important;
  }

  .print-table thead {
    background-color: #f3f4f6 !important;
    display: table-header-group !important;
  }

  .print-table tr {
    page-break-inside: avoid !important;
  }

  /* Footer styling */
  .print-footer {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    text-align: center !important;
    padding: 10px !important;
    font-size: 0.8rem !important;
    color: #666 !important;
    break-inside: avoid !important;
    border-top: 1px solid #d1d5db !important;
    page-break-inside: avoid !important;
  }

  /* Add page margin for footer */
  @page {
    size: A4 portrait;
    margin: 0.5cm;
    margin-bottom: 2cm !important;
  }

  /* Ensure content doesn't get cut off */
  .bg-white.dark\:bg-gray-900.shadow.rounded-lg.p-8.space-y-8 {
    overflow: visible !important;
    page-break-inside: avoid !important;
  }
  
  /* Remove unnecessary elements */
  .print\:hidden { display: none !important; }
  
  /* Adjust spacing */
  .space-y-6 > div {
    margin-bottom: 1rem !important;
    page-break-inside: avoid !important;
  }
  
  /* Ensure proper page breaks */
  .page-break-before {
    page-break-before: always !important;
  }
  
  .page-break-after {
    page-break-after: always !important;
  }
  
  .page-break-inside {
    page-break-inside: avoid !important;
  }
  
  /* Remove transform scaling to prevent content cutting */
  .bg-white.dark\:bg-gray-900.shadow.rounded-lg.p-8.space-y-8,
  .print-document {
    transform: none !important;
    transform-origin: top left !important;
  }
  
  /* Ensure proper width */
  .w-full {
    width: 100% !important;
  }
  
  /* Font adjustments for better print readability */
  body, p, div, span, td, th {
    font-size: 12pt !important;
    line-height: 1.4 !important;
  }
  
  h1, h2, h3, h4 {
    font-size: 14pt !important;
    line-height: 1.3 !important;
  }
  
  /* Ensure proper text color for print */
  .text-gray-600, .text-gray-700, .text-gray-800 {
    color: #000 !important;
  }
  
  /* Remove background colors that might not print well */
  .bg-gray-100, .bg-gray-200 {
    background-color: transparent !important;
  }
}
</style>