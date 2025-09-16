<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ShowHeader from '@/components/ShowHeader.vue'
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import { Printer, Shield, Stethoscope, Eye } from 'lucide-vue-next';
import { useToast } from '@/components/ui/toast';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog';

const props = defineProps<{
  invoice: any;
  partners?: Array<{ id: number; name: string }>;
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

// removed unused share/download logic to satisfy linters

const printCurrent = () => {
  setTimeout(() => window.print(), 30);
};

// Derive providers (doctors) from invoice items' visit service relations (robust to snake/camel case)
const providerNames = computed(() => {
  const set = new Set<string>();
  try {
    (props.invoice?.items || []).forEach((item: any) => {
      const vs = item?.visitService || item?.visit_service;
      const staff = vs?.staff;
      const name = staff?.full_name || [staff?.first_name, staff?.last_name].filter(Boolean).join(' ');
      if (name) set.add(name);
    });
  } catch {}
  return Array.from(set);
});

const sharedList = computed(() => {
  const rows = (props.invoice?.shared_invoices || props.invoice?.sharedInvoices || []).map((s: any) => {
    const partner = s?.partner;
    const sharedBy = s?.shared_by || s?.sharedBy;
    return {
      id: s?.id,
      status: s?.status || '-',
      partnerName: partner?.name || '—',
      partnerId: partner?.id || s?.partner_id,
      shareDate: s?.share_date || s?.created_at,
      expiresAt: s?.share_expires_at || null,
      views: s?.share_views ?? 0,
      sharedByName: sharedBy?.full_name || [sharedBy?.first_name, sharedBy?.last_name].filter(Boolean).join(' ') || '—',
      sharedById: sharedBy?.id || s?.shared_by_staff_id,
    };
  });
  return rows;
});

// Quick Share form
const shareForm = useForm({
  partner_id: null as number | null,
  notes: '' as string,
});

const isShareOpen = ref(false);
const { toast } = useToast();

function quickShare() {
  if (!shareForm.partner_id) return;
  const payload = {
    invoice_id: props.invoice.id,
    partner_id: shareForm.partner_id,
    share_date: format(new Date(), 'yyyy-MM-dd'),
    status: 'Active',
    notes: shareForm.notes || undefined,
  } as any;
  shareForm.post(route('admin.shared-invoices.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast({ title: 'Invoice shared', description: 'Partner has access to this invoice.', variant: 'default' });
      isShareOpen.value = false;
      shareForm.reset();
      router.reload({ only: ['invoice'] });
    },
    onError: (errors) => {
      const message = errors?.partner_id || errors?.invoice_id || 'Failed to share invoice.';
      toast({ title: 'Share failed', description: String(message), variant: 'destructive' });
    },
  });
}
</script>

<template>
  <Head :title="`Invoice ${invoice.invoice_number}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">
      <ShowHeader title="Invoice Details" :subtitle="`Invoice: ${invoice.invoice_number}`">
        <template #actions>
          <Button as-child variant="outline" size="sm" class="mr-2">
            <Link :href="route('admin.invoices.index')">Back</Link>
          </Button>
          <Button size="sm" @click="isShareOpen = true">Share Invoice</Button>
        </template>
      </ShowHeader>

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

        <table class="w-full mb-8 border-collapse">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2 text-left border">Service Description</th>
              <th class="p-2 text-right border">Cost</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in invoice.items" :key="item.id" class="border-b">
              <td class="p-2 border">{{ item.description }}</td>
              <td class="p-2 text-right border">{{ formatCurrency(item.cost) }}</td>
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
            <div>
              <label class="block text-sm font-medium mb-1">Partner</label>
              <select v-model.number="shareForm.partner_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                <option :value="null">Select partner…</option>
                <option v-for="p in (partners || [])" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
              <div v-if="shareForm.errors.partner_id" class="text-xs text-red-500 mt-1">{{ shareForm.errors.partner_id }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Notes (optional)</label>
              <input v-model="shareForm.notes" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800" placeholder="Add a note" />
              <div v-if="shareForm.errors.notes" class="text-xs text-red-500 mt-1">{{ shareForm.errors.notes }}</div>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" @click="isShareOpen = false" :disabled="shareForm.processing">Cancel</Button>
            <Button @click="quickShare" :disabled="shareForm.processing || !shareForm.partner_id">Share</Button>
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
  table { border-collapse: collapse; font-size: 12px; }
  th, td { border: 1px solid #d1d5db !important; }
  hr { display: none !important; }
  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: none !important; }
}
</style>
