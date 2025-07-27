<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  form: Object, // Inertia form object
});

const emit = defineEmits(['submit']);

// These would typically come from backend props
const inventoryItems = [
  { id: 1, name: 'Stethoscope' },
  { id: 2, name: 'Wheelchair' },
];
const alertTypes = ['Low Stock', 'Maintenance Due', 'Warranty Expiry', 'Overdue Return'];

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
            <select id="item_id" v-model="form.item_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option :value="null">All Items</option>
              <option v-for="item in inventoryItems" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
            <div v-if="form.errors.item_id" class="text-red-500 text-sm mt-1">{{ form.errors.item_id }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="alert_type" class="block text-sm font-medium text-gray-900 dark:text-white">Alert Type</label>
            <select id="alert_type" v-model="form.alert_type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
              <option :value="null">Select Alert Type</option>
              <option v-for="type in alertTypes" :key="type" :value="type">{{ type }}</option>
            </select>
            <div v-if="form.errors.alert_type" class="text-red-500 text-sm mt-1">{{ form.errors.alert_type }}</div>
          </div>

          <div class="col-span-full">
            <label for="message" class="block text-sm font-medium text-gray-900 dark:text-white">Alert Message</label>
            <textarea id="message" v-model="form.message" rows="3" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required></textarea>
            <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="threshold_value" class="block text-sm font-medium text-gray-900 dark:text-white">Threshold Value (if applicable)</label>
            <input type="text" id="threshold_value" v-model="form.threshold_value" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" placeholder="e.g., 5 (for low stock), 2025-12-31 (for expiry)" />
            <div v-if="form.errors.threshold_value" class="text-red-500 text-sm mt-1">{{ form.errors.threshold_value }}</div>
          </div>

          <div class="sm:col-span-3 flex items-center">
            <input type="checkbox" id="is_active" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
            <label for="is_active" class="ml-2 block text-sm font-medium text-gray-900 dark:text-white">Is Active</label>
            <div v-if="form.errors.is_active" class="text-red-500 text-sm mt-1">{{ form.errors.is_active }}</div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
