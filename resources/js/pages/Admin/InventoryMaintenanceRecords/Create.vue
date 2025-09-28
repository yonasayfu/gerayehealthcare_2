<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';
import type { InventoryItem, Staff } from '@/types';

const props = defineProps<{
  inventoryItems: InventoryItem[]; // Available inventory items
  staffMembers: Staff[]; // Available staff members
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Maintenance Records', href: route('admin.inventory-maintenance-records.index') },
  { title: 'Create', href: route('admin.inventory-maintenance-records.create') },
];

const form = useForm({
  item_id: null,
  scheduled_date: null,
  actual_date: null,
  performed_by_staff_id: null, // Changed to staff ID
  cost: null,
  description: null,
  next_due_date: null,
  status: 'Scheduled',
});

function submit() {
  form.post(route('admin.inventory-maintenance-records.store'));
}
</script>

<template>
  <Head title="Create New Inventory Maintenance Record" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Inventory Maintenance Record</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a inventory maintenance record.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.inventory-maintenance-records.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Inventory Maintenance Record' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
