<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
  inventoryItem: Object, // The inventory item to edit
});

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Items', href: route('admin.inventory-items.index') },
  { title: 'Edit', href: route('admin.inventory-items.edit', props.inventoryItem.id) },
];

const form = useForm({
  name: props.inventoryItem.name,
  description: props.inventoryItem.description,
  item_category: props.inventoryItem.item_category,
  item_type: props.inventoryItem.item_type,
  serial_number: props.inventoryItem.serial_number,
  purchase_date: props.inventoryItem.purchase_date,
  warranty_expiry: props.inventoryItem.warranty_expiry,
  supplier_id: props.inventoryItem.supplier_id,
  assigned_to_type: props.inventoryItem.assigned_to_type,
  assigned_to_id: props.inventoryItem.assigned_to_id,
  last_maintenance_date: props.inventoryItem.last_maintenance_date,
  next_maintenance_due: props.inventoryItem.next_maintenance_due,
  maintenance_schedule: props.inventoryItem.maintenance_schedule,
  notes: props.inventoryItem.notes,
  status: props.inventoryItem.status,
});

function submit() {
  form.put(route('admin.inventory-items.update', props.inventoryItem.id));
}
</script>

<template>
  <Head title="Edit Inventory Item" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Inventory Item</h1>
        <p class="text-sm text-muted-foreground">Update the details for this inventory item.</p>
      </div>

      <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" @submit="submit" />

        <div class="flex justify-end space-x-3">
          <Link :href="route('admin.inventory-items.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition">
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
