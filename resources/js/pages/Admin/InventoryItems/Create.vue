<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Items', href: route('admin.inventory-items.index') },
  { title: 'Create', href: route('admin.inventory-items.create') },
];

const form = useForm({
  name: null,
  description: null,
  item_category: null,
  item_type: null,
  serial_number: null,
  purchase_date: null,
  warranty_expiry: null,
  supplier_id: null,
  assigned_to_type: null,
  assigned_to_id: null,
  last_maintenance_date: null,
  next_maintenance_due: null,
  maintenance_schedule: null,
  notes: null,
  status: 'Available',
});

function submit() {
  form.post(route('admin.inventory-items.store'));
}
</script>

<template>
  <Head title="Create Inventory Item" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Inventory Item</h1>
        <p class="text-sm text-muted-foreground">Add a new item to your inventory.</p>
      </div>

      <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" @submit="submit" />

        <div class="flex justify-end space-x-3">
          <Link :href="route('admin.inventory-items.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition">
            Cancel
          </Link>
          <button @click="submit" :disabled="form.processing" class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition">
            {{ form.processing ? 'Creating...' : 'Create Item' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
