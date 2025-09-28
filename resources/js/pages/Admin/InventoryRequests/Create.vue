<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
  staffList: Array,
  inventoryItems: Array,
});

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
  status: 'Pending',
  needed_by_date: null,
});

function submit() {
  form.post(route('admin.inventory-requests.store'));
}
</script>

<template>
  <Head title="Create New Inventory Request" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Inventory Request</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a inventory request.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.inventory-requests.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Inventory Request' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
