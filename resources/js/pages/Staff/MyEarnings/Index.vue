<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import TextPromptModal from '@/components/TextPromptModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';

const props = defineProps<{
  payoutHistory: any;
  pendingVisits: any;
  pendingTotal: number;
  leaveDaysThisMonth?: number;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'My Earnings', href: route('staff.my-earnings.index') },
];

const formatCurrency = (value: number | string) => {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const formatDate = (dateString: string) => {
  return format(new Date(dateString), 'MMM dd, yyyy');
};

const showRequestModal = ref(false);
</script>

<template>
  <Head title="My Earnings" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">My Earnings</h1>
        <p class="text-sm text-muted-foreground">View your payout history and current pending earnings.</p>
      </div>

      <div class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-2">Pending Payout</h2>
          <p class="text-3xl font-bold text-green-600">{{ formatCurrency(pendingTotal) }}</p>
          <p class="text-sm text-muted-foreground mt-1">This is the total from your completed work that will be on your next payout.</p>
          <p v-if="props.leaveDaysThisMonth !== undefined" class="text-xs text-muted-foreground mt-1">Approved leave days this month: <strong>{{ props.leaveDaysThisMonth }}</strong></p>
          
          <div v-if="pendingVisits.data.length > 0" class="mt-4 border-t pt-4">
              <h3 class="font-semibold mb-2">Included Visits:</h3>
              <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                  <li v-for="visit in pendingVisits.data" :key="visit.id" class="flex justify-between">
                      <span>Visit on {{ formatDate(visit.scheduled_at) }}</span>
                      <span>{{ formatCurrency(visit.cost) }}</span>
                  </li>
              </ul>
          </div>

          <div class="mt-4">
            <button :disabled="pendingTotal <= 0" @click="showRequestModal = true" class="btn-glass btn-glass-sm disabled:opacity-50">Request Payout</button>
          </div>
      </div>
      <TextPromptModal
        :open="showRequestModal"
        @update:open="(v:boolean)=> showRequestModal = v"
        title="Request Payout"
        description="Add notes for your payout request (optional)."
        label="Notes"
        confirm-text="Submit Request"
        cancel-text="Cancel"
        @confirm="(n:string)=> { router.post(route('staff.my-earnings.request'), { notes: n }); showRequestModal = false }"
      />

      <div class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">Payout History</h2>
          <div class="overflow-x-auto">
              <table class="w-full text-left text-sm print-table">
                  <thead class="bg-gray-50 dark:bg-gray-800">
                      <tr>
                          <th class="px-4 py-2">Payout Date</th>
                          <th class="px-4 py-2">Total Amount</th>
                          <th class="px-4 py-2">Status</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr v-for="payout in payoutHistory.data" :key="payout.id" class="border-b dark:border-gray-700">
                          <td class="px-4 py-3">{{ formatDate(payout.payout_date) }}</td>
                          <td class="px-4 py-3 font-medium text-green-600">{{ formatCurrency(payout.total_amount) }}</td>
                          <td class="px-4 py-3">
                              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ payout.status }}</span>
                          </td>
                      </tr>
                      <tr v-if="payoutHistory.data.length === 0">
                          <td colspan="3" class="text-center py-4 text-gray-500">You have no payout history.</td>
                      </tr>
                  </tbody>
              </table>
          </div>
      </div>
    </div>
  </AppLayout>
</template>
