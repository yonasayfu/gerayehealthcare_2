<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import StaffPayoutForm from './Form.vue';
import type { BreadcrumbItemType } from '@/types';
import type { StaffPayoutData } from '@/types/staff-payout';

const props = defineProps<{
  staffMembers: Array<{ id: number; first_name: string; last_name: string }>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
  { title: 'Create', href: route('admin.staff-payouts.create') },
];

const form = useForm<StaffPayoutData>({
  staff_id: null,
  amount: 0,
  payout_date: new Date().toISOString().split('T')[0],
  status: 'Pending',
});

const submit = () => {
  form.post(route('admin.staff-payouts.store'));
};
</script>

<template>
  <Head title="Create Staff Payout" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-xl font-semibold">Create New Staff Payout</h1>

      <form @submit.prevent="submit">
        <StaffPayoutForm
          :form="form"
          :staffMembers="staffMembers"
        />

        <div class="flex justify-end pt-4">
          <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50">
            Create Payout
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
