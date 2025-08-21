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

const form = useForm({
  patient_id: props.selectedPatientId || '',
  visit_ids: [],
  invoice_date: new Date().toISOString().split('T')[0],
  due_date: new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0], // Default due date 30 days from now
});

watch(() => form.patient_id, (newPatientId) => {
  router.get(route('admin.invoices.create'), { patient_id: newPatientId }, {
    preserveState: true,
    replace: true,
  });
});

function submit() {
  console.log('Submitting form with data:', form.data()); // Add this line
  form.post(route('admin.invoices.store'), {
    onSuccess: () => {
      console.log('Invoice created successfully!');
      // Optionally, redirect or show a success message
    },
    onError: (errors) => {
      console.error('Error creating invoice:', errors);
      let errorMessage = 'An unknown error occurred.';
      if (typeof errors === 'object' && errors !== null) {
        errorMessage = Object.values(errors).flat().join('\n');
      }
      alert('Error creating invoice:\n' + errorMessage);
    },
  });
}
</script>

<template>
  <Head title="Create New Invoice" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Invoice</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a invoice.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.invoices.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Invoice' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>