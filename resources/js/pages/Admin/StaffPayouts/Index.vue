<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
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

const perPage = ref(10);

const form = useForm({
  staff_id: null,
  per_page: perPage.value,
});

watch(perPage, (value) => {
  form.per_page = value;
  form.get(route('admin.staff-payouts.index'), { preserveState: true });
});

const processPayout = (staffId: number, staffName: string, amount: string) => {
  if (confirm(`Are you sure you want to process a payout of ${formatCurrency(amount)} for ${staffName}?`)) {
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
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff Payouts & Performance</h1>
        <p class="text-sm text-muted-foreground">Review earnings, performance metrics, and process payments for staff.</p>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
        <div class="relative w-full md:w-1/3">
          <!-- Search input can go here if needed in the future -->
        </div>
        <div class="flex items-center gap-2">
          <label for="per-page" class="text-sm text-gray-600 dark:text-gray-400">Per Page:</label>
          <select id="per-page" v-model="perPage" class="form-select rounded-md shadow-sm text-sm">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
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
                  :disabled="form.processing && form.staff_id === staff.id"
                  class="inline-flex items-center gap-2 text-sm px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md transition disabled:opacity-50"
                >
                  <DollarSign class="h-4 w-4" />
                  {{ (form.processing && form.staff_id === staff.id) ? 'Processing...' : 'Process Payout' }}
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