<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
  inventoryAlert: Object, // The inventory alert to edit
});

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Alerts', href: route('admin.inventory-alerts.index') },
  { title: 'Edit', href: route('admin.inventory-alerts.edit', props.inventoryAlert.id) },
];

const form = useForm({
  item_id: props.inventoryAlert.item_id,
  alert_type: props.inventoryAlert.alert_type,
  threshold_value: props.inventoryAlert.threshold_value,
  message: props.inventoryAlert.message,
  is_active: props.inventoryAlert.is_active,
});

function submit() {
  form.put(route('admin.inventory-alerts.update', props.inventoryAlert.id));
}
</script>

<template>
  <Head title="Edit Inventory Alert" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Inventory Alert</h1>
        <p class="text-sm text-muted-foreground">Update the details for this inventory alert.</p>
      </div>

      <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" @submit="submit" />

        <div class="flex justify-end space-x-3">
          <Link :href="route('admin.inventory-alerts.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition">
            Cancel
          </Link>
          <button @click="submit" :disabled="form.processing" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition">
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
