<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

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
  <Head title="Edit Maintenance Record" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Maintenance Record
            </h3>
            <Link :href="route('admin.inventory-maintenance-records.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :inventory-items="props.inventoryItems" :staff-members="props.staffMembers" @submit="submit" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <button @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
        </div>

    </div>
  </AppLayout>
</template>
