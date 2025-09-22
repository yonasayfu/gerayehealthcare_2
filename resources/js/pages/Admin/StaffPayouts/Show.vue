<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Patient {
  id: number;
  first_name: string;
  last_name: string;
  full_name?: string;
}

interface Service {
  id: number;
  name: string;
}

interface VisitService {
  id: number;
  patient?: Patient;
  service?: Service;
  cost: number | string;
}

interface Staff {
  id: number;
  first_name: string;
  last_name: string;
}

interface StaffPayout {
  id: number;
  staff?: Staff;
  total_amount: number | string;
  payout_date: string;
  status: string;
  notes: string;
  visitServices?: VisitService[];
}

const props = defineProps<{ 
  staffPayout: StaffPayout 
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
  { title: props.staffPayout?.id ? `Payout #${props.staffPayout.id}` : 'Details', href: props.staffPayout?.id ? route('admin.staff-payouts.show', props.staffPayout.id) : '' },
];

const formatCurrency = (value: number | string) => {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

// Revert functionality
const showRevertModal = ref(false);
const reasonInput = ref<HTMLTextAreaElement | null>(null);

function openRevertModal() {
  showRevertModal.value = true;
  setTimeout(() => {
    if (reasonInput.value) {
      reasonInput.value.focus();
    }
  }, 100);
}

function confirmRevert() {
  const reason = reasonInput.value?.value || '';
  
  router.post(route('admin.staff-payouts.revert', props.staffPayout.id), {
    reason: reason
  }, {
    onSuccess: () => {
      showRevertModal.value = false;
    }
  });
}
</script>

<template>
  <Head :title="staffPayout?.id ? `Payout #${staffPayout.id}` : 'Staff Payout Details'" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ staffPayout?.id ? `Staff Payout Details` : 'Loading...' }}
              </h1>
              <p v-if="staffPayout?.id" class="text-gray-600 dark:text-gray-400">
                Payout #{{ staffPayout.id }}
              </p>
            </div>
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.staff-payouts.index')" class="btn-glass btn-glass-sm">
                Back
              </Link>
              <Link v-if="staffPayout?.id" :href="route('admin.staff-payouts.edit', staffPayout.id)" class="btn-glass btn-glass-sm bg-blue-600 hover:bg-blue-700 text-white">
                Edit
              </Link>
              <button v-if="staffPayout?.id && staffPayout.status === 'Completed'" @click="openRevertModal" class="btn-glass btn-glass-sm bg-red-600 hover:bg-red-700 text-white">
                Revert
              </button>
            </div>
          </div>
        </div>

        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">Staff Payout Details</p>
          <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <div class="p-6 space-y-6">
          <div v-if="!staffPayout?.id" class="text-center py-10">
            <p class="text-gray-500">Loading payout details...</p>
          </div>
          
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
              <p class="text-sm text-muted-foreground">Staff</p>
              <p class="font-medium">{{ staffPayout.staff?.first_name }} {{ staffPayout.staff?.last_name }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
              <p class="text-sm text-muted-foreground">Total Amount</p>
              <p class="font-medium text-green-600">{{ formatCurrency(staffPayout.total_amount) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
              <p class="text-sm text-muted-foreground">Payout Date</p>
              <p class="font-medium">{{ staffPayout.payout_date ? format(new Date(staffPayout.payout_date), 'PPP') : '-' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
              <p class="text-sm text-muted-foreground">Status</p>
              <p class="font-medium">
                <span :class="{
                  'px-2 py-1 rounded-full text-xs': true,
                  'bg-green-100 text-green-800': staffPayout.status === 'Completed',
                  'bg-yellow-100 text-yellow-800': staffPayout.status === 'Pending',
                  'bg-red-100 text-red-800': staffPayout.status === 'Voided',
                  'bg-blue-100 text-blue-800': staffPayout.status === 'Processing'
                }">
                  {{ staffPayout.status || '-' }}
                </span>
              </p>
            </div>
            <div class="md:col-span-2 lg:col-span-3 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
              <p class="text-sm text-muted-foreground">Notes</p>
              <p class="font-medium whitespace-pre-line">{{ staffPayout.notes || '-' }}</p>
            </div>
          </div>

          <div v-if="staffPayout?.visitServices?.length" class="pt-4">
            <h3 class="font-semibold mb-2">Included Visit Services</h3>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm print-table">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
                  <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Patient</th>
                    <th class="px-4 py-2">Service</th>
                    <th class="px-4 py-2">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(vs, idx) in staffPayout.visitServices || []" :key="vs?.id ?? idx" class="border-b dark:border-gray-700">
                    <td class="px-4 py-2">{{ vs.id }}</td>
                    <td class="px-4 py-2">{{ vs.patient?.first_name }} {{ vs.patient?.last_name }}</td>
                    <td class="px-4 py-2">{{ vs.service?.name || '-' }}</td>
                    <td class="px-4 py-2">{{ formatCurrency(vs.cost) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div v-else-if="staffPayout?.id && (!staffPayout.visitServices || staffPayout.visitServices.length === 0)" class="pt-4 text-center py-6">
            <p class="text-gray-500">No visit services included in this payout.</p>
          </div>
        </div>

        <!-- Removed duplicate buttons from footer -->
      </div>
      
      <div v-if="showRevertModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
          <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Revert Payout</h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-300">Provide a reason for the reversal (optional).</p>
                    <div class="mt-4">
                      <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason</label>
                      <textarea 
                        id="reason" 
                        ref="reasonInput"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        rows="3"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button 
                type="button" 
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                @click="confirmRevert"
              >
                Revert
              </button>
              <button 
                type="button" 
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-white dark:border-gray-500"
                @click="showRevertModal = false"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>