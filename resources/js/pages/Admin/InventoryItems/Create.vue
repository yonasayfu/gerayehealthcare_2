<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
  suppliers: {
    type: Array,
    default: () => [],
  },
});

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
  <Head title="Create New Inventory Item" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Inventory Item</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a inventory item.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.inventory-items.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Inventory Item' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
