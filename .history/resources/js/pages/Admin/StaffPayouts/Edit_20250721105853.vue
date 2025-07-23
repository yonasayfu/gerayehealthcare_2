<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import StaffPayoutForm from './Form.vue';
import type { BreadcrumbItemType } from '@/types';
import type { StaffPayout, StaffPayoutData } from '@/types/staff-payout';

const props = defineProps<{
  staffPayout: StaffPayout;
  staffMembers: Array<{ id: number; first_name: string; last_name: string }>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
  { title: `Edit Payout #${props.staffPayout.id}`, href: route('admin.staff-payouts.edit', props.staffPayout.id) },
];

const form = useForm<StaffPayoutData>({
  staff_id: props.staffPayout.staff_id,
  amount: parseFloat(props.staffPayout.amount),
  payout_date: props.staffPayout.payout_date,
  status: props.staffPayout.status,
});

const submit = () => {
  form.put(route('admin.staff-payouts.update', props.staffPayout.id));
};
</script>

<template>
  <Head :title="`Edit Staff Payout #${staffPayout.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-xl font-semibold">Edit Staff Payout #{{ staffPayout.id }}</h1>

      <form @submit.prevent="submit">
        <StaffPayoutForm
          :form="form"
          :staffMembers="staffMembers"
          :isEdit="true"
        />

        <div class="flex justify-end pt-4">
          <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md disabled:opacity-50">
            Update Payout
          </button>
        </div>
      </form>
    </div>
