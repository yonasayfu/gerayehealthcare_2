<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';
import FormActions from '@/components/FormActions.vue'

const props = defineProps({
  inventoryAlert: Object, // The inventory alert to edit
  inventoryItems: { type: Array, default: () => [] },
  alertTypes: { type: Array, default: () => [] },
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
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Inventory Alert</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update inventory alert information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <FormActions :cancel-href="route('admin.inventory-alerts.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
