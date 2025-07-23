<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InvoiceForm from './Form.vue';
import type { BreadcrumbItemType } from '@/types';

const props = defineProps<{
  invoice: {
    id: number;
    patient_id: number;
    invoice_date: string;
    due_date: string;
    visit_services: Array<{ id: number; service_description: string; scheduled_at: string; cost: number }>;
  };
  patients: Array<{ id: number; full_name: string }>;
  billableVisits: Array<any>; // Visits that can be added to this invoice
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: `Edit Invoice #${props.invoice.id}`, href: route('admin.invoices.edit', props.invoice.id) },
];

const form = useForm({
  patient_id: props.invoice.patient_id,
  visit_ids: props.invoice.visit_services.map(visit => visit.id),
  invoice_date: props.invoice.invoice_date,
  due_date: props.invoice.due_date,
});

const handlePatientSelected = (newPatientId: string | null) => {
  // In edit mode, patient selection is disabled in the form,
  // so this handler might not be strictly necessary for patient_id change,
  // but it's good to keep for consistency if the form ever allows it.
  // For now, we just ensure the form's patient_id is consistent.
  if (newPatientId !== null) {
    form.patient_id = newPatientId;
  }
};

const submit = () => {
  form.put(route('admin.invoices.update', props.invoice.id));
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
        </div>
      </form>
    </div>
