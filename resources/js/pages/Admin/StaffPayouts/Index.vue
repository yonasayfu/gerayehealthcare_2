<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { DollarSign } from 'lucide-vue-next';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

import Pagination from '@/components/Pagination.vue';

const props = defineProps<{
  staffWithEarnings: {
    data: Array<{
      id: number;
      first_name: string;
      last_name: string;
      unpaid_visits_count: number;
      unique_patients_count: number;
      total_hours_logged: number;
      total_unpaid_cost: string | null;
    }>;
    links: Array<any>;
    meta: { // Add meta for pagination details
      current_page: number;
      from: number;
      last_page: number;
      per_page: 10,
      to: number;
      total: number;
    };
  };
  performanceData: Array<{ // New prop for chart data
    first_name: string;
    last_name: string;
    payouts_sum_total_amount: string;
  }>
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
];

const perPage = ref(5);
const searchQuery = ref('');

const form = useForm({
  staff_id: null as number | null,
  per_page: perPage.value,
});

const processingStaffId = ref<number | null>(null)
const alertMessage = ref<string | null>(null)
const alertType = ref<'success' | 'error' | null>(null)

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch(searchQuery, (val) => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get(route('admin.staff-payouts.index'), { per_page: perPage.value, search: val }, { preserveState: true, replace: true });
  }, 300);
});

// Per-page change triggers reload preserving search
watch(perPage, (value) => {
  router.get(route('admin.staff-payouts.index'), { per_page: value, search: searchQuery.value }, { preserveState: true, replace: true });
});

const processPayout = (staffId: number, staffName: string, amount: string) => {
  if (!confirm(`Process payout of ${formatCurrency(amount)} for ${staffName}?`)) return
  alertMessage.value = null
  alertType.value = null
  processingStaffId.value = staffId
  form.transform(() => ({ staff_id: staffId }))
    .post(route('admin.staff-payouts.store'), {
      preserveScroll: true,
      onSuccess: () => {
        alertMessage.value = `Payout processed successfully for ${staffName}.`
        alertType.value = 'success'
        // reload current page to refresh unpaid counts while preserving filters
        router.get(route('admin.staff-payouts.index'), { per_page: perPage.value, search: searchQuery.value }, { preserveState: true, replace: true })
      },
      onError: (errors) => {
        alertMessage.value = Object.values(errors)[0] as string ?? 'Failed to process payout.'
        alertType.value = 'error'
      },
      onFinish: () => {
        processingStaffId.value = null
      }
    })
}

const formatCurrency = (value: string | null) => {
  const amount = parseFloat(value || '0');
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

// Updated Chart Data to use performanceData
const chartData = computed(() => ({
  labels: props.performanceData.map(s => `${s.first_name} ${s.last_name}`),
  datasets: [
    {
      label: 'Total Paid Earnings ($)',
      backgroundColor: '#16a34a', // Green color for "paid"
      borderColor: '#16a34a',
      data: props.performanceData.map(s => parseFloat(s.payouts_sum_total_amount || '0')),
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
    title: {
        display: true,
        text: 'Staff Performance: Total Earnings Paid Out'
    }
  },
};
</script>

<template>
  <Head title="Staff Payouts" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Staff Payouts</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage staff payouts</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.staff-payouts.create')" class="btn-glass">
              <span>Add Staff Payout</span>
            </Link>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <div v-if="alertMessage" :class="['rounded-md p-3 text-sm', alertType==='success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200']">
        {{ alertMessage }}
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
           <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">Staff Member</th>
              <th class="px-6 py-3 text-center">Unpaid Visits</th>
              <th class="px-6 py-3 text-center">Patients Visited</th>
              <th class="px-6 py-3 text-center">Hours Logged</th>
              <th class="px-6 py-3">Total Unpaid Amount</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="staff in staffWithEarnings?.data" :key="staff.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 font-medium">{{ staff.first_name }} {{ staff.last_name }}</td>
              <td class="px-6 py-4 text-center">{{ staff.unpaid_visits_count }}</td>
              <td class="px-6 py-4 text-center">{{ staff.unique_patients_count }}</td>
              <td class="px-6 py-4 text-center">{{ staff.total_hours_logged }}</td>
              <td class="px-6 py-4 font-semibold text-green-600">{{ formatCurrency(staff.total_unpaid_cost) }}</td>
              <td class="px-6 py-4 text-right">
                <button
                  v-if="staff.unpaid_visits_count > 0"
                  @click="processPayout(staff.id, `${staff.first_name} ${staff.last_name}`, staff.total_unpaid_cost)"
                  :disabled="processingStaffId === staff.id"
                  class="inline-flex items-center gap-2 text-sm px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md transition disabled:opacity-50"
                >
                  <svg v-if="processingStaffId === staff.id" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                  </svg>
                  <DollarSign v-else class="h-4 w-4" />
                  {{ processingStaffId === staff.id ? 'Processing...' : 'Process Payout' }}
                </button>
                <span v-else class="text-xs text-gray-400">No pending payments</span>
              </td>
            </tr>
            <tr v-if="staffWithEarnings?.data?.length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No staff with unpaid visits found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-6">
        <Pagination v-if="staffWithEarnings?.data?.length > 0" :links="staffWithEarnings.links" />
      </div>

       <div v-if="performanceData.length > 0" class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow h-80">
          <Bar :data="chartData" :options="chartOptions" />
      </div>

    </div>
  </AppLayout>
</template>