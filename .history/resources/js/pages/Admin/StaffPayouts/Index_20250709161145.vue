<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { DollarSign } from 'lucide-vue-next';

defineProps<{
  staffWithEarnings: Array<{
    id: number;
    first_name: string;
    last_name: string;
    visit_services_count: number;
    visit_services_sum_cost: string | null;
  }>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
];

const form = useForm({
  staff_id: null,
});

const processPayout = (staffId: number, staffName: string, amount: string) => {
  if (confirm(`Are you sure you want to process a payout of $${amount} for ${staffName}?`)) {
    form.transform(() => ({
      staff_id: staffId,
    })).post(route('admin.staff-payouts.store'), {
      preserveScroll: true,
    });
  }
};

const formatCurrency = (value: string | null) => {
  const amount = parseFloat(value || '0');
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
  <Head title="Staff Payouts" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff Payouts</h1>
        <p class="text-sm text-muted-foreground">Review and process payments for staff based on completed, unpaid visits.</p>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">Staff Member</th>
              <th class="px-6 py-3 text-center">Unpaid Visits</th>
              <th class="px-6 py-3">Total Unpaid Amount</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="staff in staffWithEarnings" :key="staff.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 font-medium">{{ staff.first_name }} {{ staff.last_name }}</td>
              <td class="px-6 py-4 text-center">{{ staff.visit_services_count }}</td>
              <td class="px-6 py-4 font-semibold text-green-600">{{ formatCurrency(staff.visit_services_sum_cost) }}</td>
              <td class="px-6 py-4 text-right">
                <button
                  v-if="staff.visit_services_count > 0"
                  @click="processPayout(staff.id, `${staff.first_name} ${staff.last_name}`, staff.visit_services_sum_cost)"
                  :disabled="form.processing && form.staff_id === staff.id"
                  class="inline-flex items-center gap-2 text-sm px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition disabled:opacity-50"
                >
                  <DollarSign class="h-4 w-4" />
                  {{ (form.processing && form.staff_id === staff.id) ? 'Processing...' : 'Process Payout' }}
                </button>
                <span v-else class="text-xs text-gray-400">No pending payments</span>
              </td>
            </tr>
            <tr v-if="staffWithEarnings.length === 0">
              <td colspan="4" class="text-center px-6 py-4 text-gray-400">No staff found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
