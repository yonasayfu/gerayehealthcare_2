<script setup lang="ts">
import { computed } from 'vue';
import type { StaffPayoutFormType } from '@/types/staff-payout';

const props = defineProps<{
  form: StaffPayoutFormType;
  staffMembers: Array<{ id: number; first_name: string; last_name: string }>;
  isEdit?: boolean;
}>();

const staffSelectDisabled = computed(() => props.isEdit && props.form.staff_id !== null);
</script>

<template>
  <div class="p-4 bg-white rounded-lg shadow space-y-4">
    <div>
      <label for="staff_id" class="block text-sm font-medium text-gray-700">Staff Member</label>
      <select
        id="staff_id"
        v-model="form.staff_id"
        :disabled="staffSelectDisabled"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
      >
        <option disabled :value="null">-- Select Staff Member --</option>
        <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">
          {{ staff.first_name }} {{ staff.last_name }}
        </option>
      </select>
      <div v-if="form.errors.staff_id" class="text-red-500 text-xs mt-1">{{ form.errors.staff_id }}</div>
    </div>

    <div>
      <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
      <input
        type="number"
        id="amount"
        v-model.number="form.amount"
        step="0.01"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
      />
      <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
    </div>

    <div>
      <label for="payout_date" class="block text-sm font-medium text-gray-700">Payout Date</label>
      <input
        type="date"
        id="payout_date"
        v-model="form.payout_date"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
      />
      <div v-if="form.errors.payout_date" class="text-red-500 text-xs mt-1">{{ form.errors.payout_date }}</div>
    </div>

    <div v-if="isEdit">
      <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
      <select
        id="status"
        v-model="form.status"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
      >
        <option value="Pending">Pending</option>
        <option value="Processed">Processed</option>
        <option value="Cancelled">Cancelled</option>
      </select>
      <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
