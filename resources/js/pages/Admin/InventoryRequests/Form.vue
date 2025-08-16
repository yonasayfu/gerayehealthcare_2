<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  form: Object, // Inertia form object
  staffList: Array,
  inventoryItems: Array,
});

const emit = defineEmits(['submit']);
const priorities = ['Low', 'Normal', 'High', 'Urgent'];
const statuses = ['Pending', 'Approved', 'Rejected', 'Fulfilled', 'Partially Fulfilled'];

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="space-y-6">
      <!-- Request Details -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Request Details</h2>
        <p class="mt-1 text-sm text-muted-foreground">Information about the requested item and purpose.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="requester_id" class="block text-sm font-medium text-gray-900 dark:text-white">Requester</label>
            <select id="requester_id" v-model.number="form.requester_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
              <option :value="null">Select Requester</option>
              <option v-for="staff in staffList" :key="staff.id" :value="staff.id">{{ staff.first_name }} {{ staff.last_name }}</option>
            </select>
            <div v-if="form.errors.requester_id" class="text-red-500 text-sm mt-1">{{ form.errors.requester_id }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="item_id" class="block text-sm font-medium text-gray-900 dark:text-white">Item</label>
            <select id="item_id" v-model.number="form.item_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
              <option :value="null">Select Item</option>
              <option v-for="item in inventoryItems" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
            <div v-if="form.errors.item_id" class="text-red-500 text-sm mt-1">{{ form.errors.item_id }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="quantity_requested" class="block text-sm font-medium text-gray-900 dark:text-white">Quantity Requested</label>
            <input type="number" id="quantity_requested" v-model.number="form.quantity_requested" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" min="1" required />
            <div v-if="form.errors.quantity_requested" class="text-red-500 text-sm mt-1">{{ form.errors.quantity_requested }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="needed_by_date" class="block text-sm font-medium text-gray-900 dark:text-white">Needed By Date</label>
            <input type="date" id="needed_by_date" v-model="form.needed_by_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.needed_by_date" class="text-red-500 text-sm mt-1">{{ form.errors.needed_by_date }}</div>
          </div>

          <div class="col-span-full">
            <label for="reason" class="block text-sm font-medium text-gray-900 dark:text-white">Reason for Request</label>
            <textarea id="reason" v-model="form.reason" rows="3" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
            <div v-if="form.errors.reason" class="text-red-500 text-sm mt-1">{{ form.errors.reason }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="priority" class="block text-sm font-medium text-gray-900 dark:text-white">Priority</label>
            <select id="priority" v-model="form.priority" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option v-for="p in priorities" :key="p" :value="p">{{ p }}</option>
            </select>
            <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
          </div>

          <div class="sm:col-span-3" v-if="form.status">
            <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
            <select id="status" v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
          </div>

          <div class="sm:col-span-3" v-if="form.status === 'Approved' || form.status === 'Partially Fulfilled' || form.status === 'Fulfilled'">
            <label for="quantity_approved" class="block text-sm font-medium text-gray-900 dark:text-white">Quantity Approved</label>
            <input type="number" id="quantity_approved" v-model.number="form.quantity_approved" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" :max="form.quantity_requested" min="0" />
            <div v-if="form.errors.quantity_approved" class="text-red-500 text-sm mt-1">{{ form.errors.quantity_approved }}</div>
          </div>

          <div class="sm:col-span-3" v-if="form.status === 'Approved' || form.status === 'Rejected'">
            <label for="approver_id" class="block text-sm font-medium text-gray-900 dark:text-white">Approver</label>
            <select id="approver_id" v-model.number="form.approver_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option :value="null">Select Approver</option>
              <option v-for="staff in staffList" :key="staff.id" :value="staff.id">{{ staff.first_name }} {{ staff.last_name }}</option>
            </select>
            <div v-if="form.errors.approver_id" class="text-red-500 text-sm mt-1">{{ form.errors.approver_id }}</div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
