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
const staffList = [
  { id: 1, first_name: 'John', last_name: 'Doe' },
  { id: 2, first_name: 'Jane', last_name: 'Smith' },
];
const transactionTypes = ['Issue', 'Return', 'Transfer', 'Maintenance Out', 'Maintenance In', 'Disposal'];

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="space-y-6">
      <!-- Transaction Details -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Transaction Details</h2>
        <p class="mt-1 text-sm text-muted-foreground">Record the movement or status change of an inventory item.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="item_id" class="block text-sm font-medium text-gray-900 dark:text-white">Item</label>
            <select id="item_id" v-model="form.item_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
              <option :value="null">Select Item</option>
              <option v-for="item in inventoryItems" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
            <div v-if="form.errors.item_id" class="text-red-500 text-sm mt-1">{{ form.errors.item_id }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="transaction_type" class="block text-sm font-medium text-gray-900 dark:text-white">Transaction Type</label>
            <select id="transaction_type" v-model="form.transaction_type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
              <option :value="null">Select Type</option>
              <option v-for="type in transactionTypes" :key="type" :value="type">{{ type }}</option>
            </select>
            <div v-if="form.errors.transaction_type" class="text-red-500 text-sm mt-1">{{ form.errors.transaction_type }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="quantity" class="block text-sm font-medium text-gray-900 dark:text-white">Quantity</label>
            <input type="number" id="quantity" v-model="form.quantity" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required min="1" />
            <div v-if="form.errors.quantity" class="text-red-500 text-sm mt-1">{{ form.errors.quantity }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="performed_by_id" class="block text-sm font-medium text-gray-900 dark:text-white">Performed By</label>
            <select id="performed_by_id" v-model="form.performed_by_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
              <option :value="null">Select Staff</option>
              <option v-for="staff in staffList" :key="staff.id" :value="staff.id">{{ staff.first_name }} {{ staff.last_name }}</option>
            </select>
            <div v-if="form.errors.performed_by_id" class="text-red-500 text-sm mt-1">{{ form.errors.performed_by_id }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="from_location" class="block text-sm font-medium text-gray-900 dark:text-white">From Location</label>
            <input type="text" id="from_location" v-model="form.from_location" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.from_location" class="text-red-500 text-sm mt-1">{{ form.errors.from_location }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="to_location" class="block text-sm font-medium text-gray-900 dark:text-white">To Location</label>
            <input type="text" id="to_location" v-model="form.to_location" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.to_location" class="text-red-500 text-sm mt-1">{{ form.errors.to_location }}</div>
          </div>

          <div class="col-span-full">
            <label for="notes" class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
            <textarea id="notes" v-model="form.notes" rows="3" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
            <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
