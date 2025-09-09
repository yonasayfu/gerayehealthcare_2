<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';
import FormActions from '@/components/FormActions.vue'

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
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Inventory Request</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update inventory request information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <FormActions :cancel-href="route('admin.inventory-requests.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
