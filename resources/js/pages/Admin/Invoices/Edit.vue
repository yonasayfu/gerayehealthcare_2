<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import Form from './Form.vue';

const props = defineProps<{
  invoice: any;
  patients: Array<{ id: number; full_name: string }>;
  billableVisits: Array<any>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: props.invoice.invoice_number, href: route('admin.invoices.show', props.invoice.id) },
  { title: 'Edit', href: route('admin.invoices.edit', props.invoice.id) },
];

const form = useForm({
  patient_id: props.invoice.patient_id,
  invoice_date: props.invoice.invoice_date,
  due_date: props.invoice.due_date,
  notes: props.invoice.notes || '',
});

function submit() {
  form.put(route('admin.invoices.update', props.invoice.id));
}
</script>

<template>
  <Head :title="`Edit Invoice ${invoice.invoice_number}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Invoice</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update invoice information.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Save -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.invoices.show', invoice.id)" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>