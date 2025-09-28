<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue'
import FormActions from '@/components/FormActions.vue'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
  { title: 'Create', href: route('admin.suppliers.create') },
];

const form = useForm({
  name: null,
  contact_person: null,
  email: null,
  phone: null,
  address: null,
});

function submit() {
  form.post(route('admin.suppliers.store'));
}
</script>

<template>
  <Head title="Create New Supplier" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Supplier</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a supplier.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <FormActions :cancel-href="route('admin.suppliers.index')" submit-text="Create Supplier" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
