<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InvoiceForm from './Form.vue';
import type { BreadcrumbItemType } from '@/types';

const props = defineProps<{
  patients: Array<{ id: number; full_name: string }>;
  selectedPatientId: string | null;
  billableVisits: Array<any>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: 'Create', href: route('admin.invoices.create') },
];

const selectedPatient = ref(props.selectedPatientId);

const form = useForm({
  patient_id: props.selectedPatientId,
  visit_ids: [],
  invoice_date: new Date().toISOString().split('T')[0],
  due_date: new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0], // Default due date 30 days from now
});

const handlePatientSelected = (newPatientId: string | null) => {
  selectedPatient.value = newPatientId;
  router.get(route('admin.invoices.create'), { patient_id: newPatientId }, {
    preserveState: true,
    replace: true,
  });
};

const submit = () => {
  form.post(route('admin.invoices.store'));
};
</script>

<template>
  <Head title="Create Invoice" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-xl font-semibold">Create New Invoice</h1>

      <form @submit.prevent="submit">
        <InvoiceForm
          :form="form"
          :patients="patients"
          :selectedPatientId="selectedPatient"
          :billableVisits="billableVisits"
          @patient-selected="handlePatientSelected"
        />

        <div class="flex justify-end pt-4">
          <button type="submit" :disabled="form.processing || form.visit_ids.length === 0" class="px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50">
            Generate Invoice
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
