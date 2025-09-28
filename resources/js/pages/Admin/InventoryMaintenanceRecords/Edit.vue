<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';
import FormActions from '@/components/FormActions.vue'

import type { InventoryItem, InventoryMaintenanceRecord, Staff } from '@/types';

const props = defineProps<{
  maintenanceRecord: InventoryMaintenanceRecord; // The maintenance record to edit
  inventoryItems: InventoryItem[]; // Available inventory items
  staffMembers: Staff[]; // Available staff members
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Maintenance Records', href: route('admin.inventory-maintenance-records.index') },
  { title: 'Edit', href: route('admin.inventory-maintenance-records.edit', props.maintenanceRecord.id) },
];

const form = useForm({
  item_id: props.maintenanceRecord.item_id,
  scheduled_date: props.maintenanceRecord.scheduled_date,
  actual_date: props.maintenanceRecord.actual_date,
  performed_by_staff_id: props.maintenanceRecord.performed_by_staff_id, // Changed to staff ID
  cost: props.maintenanceRecord.cost,
  description: props.maintenanceRecord.description,
  next_due_date: props.maintenanceRecord.next_due_date,
  status: props.maintenanceRecord.status,
});

function submit() {
  form.put(route('admin.inventory-maintenance-records.update', props.maintenanceRecord.id));
}
</script>

<template>
  <Head title="Edit Inventory Maintenance Record" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Inventory Maintenance Record</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update inventory maintenance record information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <FormActions :cancel-href="route('admin.inventory-maintenance-records.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
