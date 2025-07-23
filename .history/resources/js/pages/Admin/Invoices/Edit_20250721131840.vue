<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InvoiceForm from './Form.vue';
import type { BreadcrumbItemType } from '@/types';
import type { InvoiceFormData } from '@/types/invoice';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios'; // Import axios

declare const ChapaPop: any; // Declare ChapaPop to avoid TypeScript errors

const props = defineProps<{
  invoice: {
    id: number;
    patient_id: number;
    invoice_number: string;
    invoice_date: string;
    due_date: string;
    subtotal: number;
    tax_amount: number;
    grand_total: number;
    status: string;
    paid_at: string | null;
    chapa_tx_ref: string | null;
    chapa_checkout_url: string | null;
    chapa_payment_status: string | null;
    chapa_transaction_id: string | null;
    chapa_payment_method: string | null;
    chapa_paid_at: string | null;
    patient: {
      id: number;
      full_name: string;
      email: string;
    };
    visit_services: Array<{ id: number; service_description: string; scheduled_at: string; cost: number }>;
  };
  patients: Array<{ id: number; full_name: string }>;
  billableVisits: Array<any>; // Visits that can be added to this invoice
  chapaPublicKey: string; // Passed from backend
}>();

const page = usePage();
// const user = page.props.auth.user; // Removed for now to simplify and avoid type issues

const form = useForm<InvoiceFormData>({
  patient_id: props.invoice.patient_id,
  visit_ids: props.invoice.visit_services.map((visit: any) => visit.id), // Added explicit type for visit
  invoice_date: props.invoice.invoice_date,
  due_date: props.invoice.due_date,
});

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: `Edit Invoice #${props.invoice.id}`, href: route('admin.invoices.edit', props.invoice.id) },
];


const handlePatientSelected = (newPatientId: string | null) => {
  // In edit mode, patient selection is disabled in the form,
  // so this handler might not be strictly necessary for patient_id change,
  // but it's good to keep for consistency if the form ever allows it.
  // For now, we just ensure the form's patient_id is consistent.
  if (newPatientId !== null) {
    form.patient_id = parseInt(newPatientId);
  }
};

const submit = () => {
  form.put(route('admin.invoices.update', props.invoice.id));
};

const initiateChapaPayment = async () => {
  if (!props.invoice.patient || !props.invoice.patient.email) {
    alert('Patient email is required to initiate payment.');
    return;
  }

  const tx_ref = `invoice-${props.invoice.id}-${Date.now()}`; // Unique transaction reference

  try {
    const response = await axios.post(route('chapa.initiate'), {
      amount: props.invoice.grand_total,
      currency: 'ETB', // Assuming ETB as default, adjust if needed
      email: props.invoice.patient.email,
      first_name: props.invoice.patient.full_name.split(' ')[0] || 'N/A',
      last_name: props.invoice.patient.full_name.split(' ').slice(1).join(' ') || 'N/A',
      tx_ref: tx_ref,
      callback_url: route('chapa.webhook'),
      return_url: route('chapa.verify', tx_ref),
      invoice_id: props.invoice.id,
    });

    if (response.data && response.data.data && response.data.data.checkout_url) {
      // Redirect to Chapa checkout page
      // window.location.href = response.data.data.checkout_url;

      // Or use ChapaPop for inline payment
      ChapaPop({
        public_key: props.chapaPublicKey,
        tx_ref: tx_ref,
        amount: props.invoice.grand_total,
        currency: 'ETB',
        email: props.invoice.patient.email,
        first_name: props.invoice.patient.full_name.split(' ')[0] || 'N/A',
        last_name: props.invoice.patient.full_name.split(' ').slice(1).join(' ') || 'N/A',
        title: `Payment for Invoice #${props.invoice.invoice_number}`,
        description: `Payment for services on invoice #${props.invoice.invoice_number}`,
        callback: (response: any) => {
          console.log('Chapa callback response:', response);
          if (response.status === 'success') {
            // Verify payment on your backend
            window.location.href = route('chapa.verify', response.tx_ref);
          } else {
            alert('Payment was not successful. Please try again.');
          }
        },
        onClose: () => {
          console.log('Chapa payment modal closed.');
        }
      });

    } else {
      alert('Failed to initiate payment: ' + (response.data.message || 'Unknown error'));
    }
  } catch (error: any) {
    console.error('Error initiating Chapa payment:', error);
    alert('Error initiating payment: ' + (error.response?.data?.message || error.message));
  }
};
</script>

<template>
  <Head :title="`Edit Invoice #${invoice.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-xl font-semibold">Edit Invoice #{{ invoice.id }}</h1>

      <form @submit.prevent="submit">
        <InvoiceForm
          :form="form"
          :patients="patients"
          :selectedPatientId="invoice.patient_id.toString()"
          :billableVisits="billableVisits"
          :isEdit="true"
          @patient-selected="handlePatientSelected"
        />

        <div class="flex justify-end pt-4">
          <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md disabled:opacity-50">
            Update Invoice
          </button>
          <button
            v-if="invoice.status !== 'paid'"
            @click="initiateChapaPayment"
            :disabled="form.processing"
            type="button"
            class="ml-4 px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50"
          >
            Pay with Chapa
          </button>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
