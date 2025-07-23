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
