<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import type { InventoryItem } from '@/types';

const props = defineProps<{
  form: any; // Inertia form object
  inventoryItems: InventoryItem[]; // Available inventory items
}>();

const emit = defineEmits(['submit']);

const maintenanceStatuses = ['Scheduled', 'Completed', 'Overdue', 'Cancelled'];

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="space-y-6">
      <!-- Maintenance Details -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Maintenance Details</h2>
        <p class="mt-1 text-sm text-muted-foreground">Record the details of an item's maintenance.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="item_id" class="block text-sm font-medium text-gray-900 dark:text-white">Item</label>
            <select id="item_id" v-model="form.item_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
              <option :value="null">Select Item</option>
              <option v-for="item in inventoryItems" :key="item.id" :value="Number(item.id)">{{ item.name }}</option>
            </select>
            <div v-if="form.errors.item_id" class="text-red-500 text-sm mt-1">{{ form.errors.item_id }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="scheduled_date" class="block text-sm font-medium text-gray-900 dark:text-white">Scheduled Date</label>
            <input type="date" id="scheduled_date" v-model="form.scheduled_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.scheduled_date" class="text-red-500 text-sm mt-1">{{ form.errors.scheduled_date }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="actual_date" class="block text-sm font-medium text-gray-900 dark:text-white">Actual Date</label>
            <input type="date" id="actual_date" v-model="form.actual_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.actual_date" class="text-red-500 text-sm mt-1">{{ form.errors.actual_date }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="performed_by" class="block text-sm font-medium text-gray-900 dark:text-white">Performed By</label>
            <input type="text" id="performed_by" v-model="form.performed_by" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.performed_by" class="text-red-500 text-sm mt-1">{{ form.errors.performed_by }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="cost" class="block text-sm font-medium text-gray-900 dark:text-white">Cost ($)</label>
            <input type="number" id="cost" v-model="form.cost" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" step="0.01" min="0" />
            <div v-if="form.errors.cost" class="text-red-500 text-sm mt-1">{{ form.errors.cost }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="next_due_date" class="block text-sm font-medium text-gray-900 dark:text-white">Next Due Date</label>
            <input type="date" id="next_due_date" v-model="form.next_due_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.next_due_date" class="text-red-500 text-sm mt-1">{{ form.errors.next_due_date }}</div>
          </div>

          <div class="col-span-full">
            <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Description of Work</label>
            <textarea id="description" v-model="form.description" rows="3" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
            <select id="status" v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option v-for="s in maintenanceStatuses" :key="s" :value="s">{{ s }}</option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
