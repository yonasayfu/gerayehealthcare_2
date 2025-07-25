<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
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

watch(selectedPatient, (newPatientId) => {
  router.get(route('admin.invoices.create'), { patient_id: newPatientId }, {
    preserveState: true,
    replace: true,
  });
});

const submit = () => {
  form.post(route('admin.invoices.store'));
};
</script>

<template>
  <Head title="Create Invoice" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-xl font-semibold">Create New Invoice</h1>

      <div class="p-4 bg-white rounded-lg shadow">
        <label for="patient" class="block text-sm font-medium text-gray-700">Step 1: Select a Patient</label>
        <select id="patient" v-model="selectedPatient" class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm">
          <option disabled value="">-- Select a Patient --</option>
          <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
        </select>
      </div>

      <form v-if="selectedPatient" @submit.prevent="submit">
        <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
          <h2 class="text-lg font-semibold">Step 2: Select Billable Visits</h2>
          
          <div v-if="billableVisits.length === 0" class="text-center text-gray-500 py-4">
            No billable visits found for this patient.
          </div>
          
          <div v-else class="space-y-2 border rounded-md p-2">
            <label v-for="visit in billableVisits" :key="visit.id" class="flex items-center gap-4 p-2 hover:bg-gray-50 rounded-md">
              <input type="checkbox" :value="visit.id" v-model="form.visit_ids" class="rounded" />
              <span class="flex-1">{{ visit.service_description || 'Standard Visit' }} on {{ new Date(visit.scheduled_at).toLocaleDateString() }}</span>
              <span class="font-mono">${{ visit.cost }}</span>
            </label>
          </div>

          <div class="grid md:grid-cols-2 gap-4 pt-4 border-t">
              <div>
                  <label for="invoice_date" class="block text-sm font-medium">Invoice Date</label>
                  <input type="date" v-model="form.invoice_date" id="invoice_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
              </div>
              <div>
                  <label for="due_date" class="block text-sm font-medium">Due Date</label>
                  <input type="date" v-model="form.due_date" id="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
              </div>
          </div>

          <div class="flex justify-end pt-4">
            <button type="submit" :disabled="form.processing || form.visit_ids.length === 0" class="px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50">
              Generate Invoice
            </button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>