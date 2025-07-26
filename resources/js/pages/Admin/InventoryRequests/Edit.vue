<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
  inventoryRequest: Object, // The inventory request to edit
  staffList: Array,
  inventoryItems: Array,
});

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Requests', href: route('admin.inventory-requests.index') },
  { title: 'Edit', href: route('admin.inventory-requests.edit', props.inventoryRequest.id) },
];

const form = useForm({
  requester_id: props.inventoryRequest.requester_id,
  approver_id: props.inventoryRequest.approver_id,
  item_id: props.inventoryRequest.item_id,
  quantity_requested: props.inventoryRequest.quantity_requested,
  quantity_approved: props.inventoryRequest.quantity_approved,
  reason: props.inventoryRequest.reason,
  status: props.inventoryRequest.status,
  priority: props.inventoryRequest.priority,
  needed_by_date: props.inventoryRequest.needed_by_date,
});

function submit() {
  form.put(route('admin.inventory-requests.update', props.inventoryRequest.id));
}
</script>

<template>
  <Head title="Edit Inventory Request" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Inventory Request</h1>
        <p class="text-sm text-muted-foreground">Update the details for this inventory request.</p>
      </div>

      <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" @submit="submit" />

        <div class="flex justify-end space-x-3">
          <Link :href="route('admin.inventory-requests.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition">
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
