<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Requests', href: route('admin.inventory-requests.index') },
  { title: 'Create', href: route('admin.inventory-requests.create') },
];

const form = useForm({
  requester_id: null,
  item_id: null,
  quantity_requested: 1,
  reason: null,
  priority: 'Normal',
  needed_by_date: null,
});

function submit() {
  form.post(route('admin.inventory-requests.store'));
}
</script>

<template>
  <Head title="Create Inventory Request" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Inventory Request</h1>
        <p class="text-sm text-muted-foreground">Submit a request for an inventory item.</p>
      </div>

      <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" @submit="submit" />

        <div class="flex justify-end space-x-3">
          <Link :href="route('admin.inventory-requests.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition">
            Cancel
          </Link>
          <button @click="submit" :disabled="form.processing" class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition">
            {{ form.processing ? 'Submitting...' : 'Submit Request' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
