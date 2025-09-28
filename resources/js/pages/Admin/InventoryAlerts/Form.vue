<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue'

const props = defineProps({
  form: Object, // Inertia form object
  inventoryItems: { type: Array, default: () => [] },
  alertTypes: { type: Array, default: () => [] },
});

const emit = defineEmits(['submit']);

// Fallbacks if controller data not provided (dev/test only)
const fallbackInventoryItems = [
  { id: 1, name: 'Stethoscope' },
  { id: 2, name: 'Wheelchair' },
];
const fallbackAlertTypes = ['Low Stock', 'Maintenance Due', 'Warranty Expiry', 'Overdue Return'];

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="space-y-6">
      <!-- Alert Details -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Alert Details</h2>
        <p class="mt-1 text-sm text-muted-foreground">Define the conditions for this inventory alert.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="item_id" class="block text-sm font-medium text-gray-900 dark:text-white">Item (Optional)</label>
            <select id="item_id" v-model="form.item_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
              <option :value="null">All Items</option>
              <option v-for="item in (props.inventoryItems?.length ? props.inventoryItems : fallbackInventoryItems)" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
            <InputError class="mt-1" :message="form.errors.item_id" />
          </div>

          <div class="sm:col-span-3">
            <label for="alert_type" class="block text-sm font-medium text-gray-900 dark:text-white">Alert Type</label>
            <select id="alert_type" v-model="form.alert_type" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" required>
              <option :value="null">Select Alert Type</option>
              <option v-for="type in (props.alertTypes?.length ? props.alertTypes : fallbackAlertTypes)" :key="type" :value="type">{{ type }}</option>
            </select>
            <InputError class="mt-1" :message="form.errors.alert_type" />
          </div>

          <div class="col-span-full">
            <label for="message" class="block text-sm font-medium text-gray-900 dark:text-white">Alert Message</label>
            <textarea id="message" v-model="form.message" rows="3" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" required></textarea>
            <InputError class="mt-1" :message="form.errors.message" />
          </div>

          <div class="sm:col-span-3">
            <label for="threshold_value" class="block text-sm font-medium text-gray-900 dark:text-white">Threshold Value (if applicable)</label>
            <input type="text" id="threshold_value" v-model="form.threshold_value" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" placeholder="e.g., 5 (for low stock), 2025-12-31 (for expiry)" />
            <InputError class="mt-1" :message="form.errors.threshold_value" />
          </div>

          <div class="sm:col-span-3">
            <label for="triggered_at" class="block text-sm font-medium text-gray-900 dark:text-white">Triggered At (Optional)</label>
            <input type="datetime-local" id="triggered_at" v-model="form.triggered_at" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
            <InputError class="mt-1" :message="form.errors.triggered_at" />
          </div>

          <div class="sm:col-span-3 flex items-center">
            <input type="checkbox" id="is_active" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
            <label for="is_active" class="ml-2 block text-sm font-medium text-gray-900 dark:text-white">Is Active</label>
            <InputError class="mt-1" :message="form.errors.is_active" />
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
