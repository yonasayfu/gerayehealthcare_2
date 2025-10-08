<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { BreadcrumbItemType } from '@/types';
import { DollarSign, Printer, Edit, Eye, FileText, Search } from 'lucide-vue-next';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';
import { useExport } from '@/composables/useExport';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

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
      pending_payout_requests_count?: number;
      latest_payout_id?: number | null;
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
const sortField = ref('first_name');
const sortDirection = ref<'asc'|'desc'>('asc');

const form = useForm({
  staff_id: null as number | null,
  per_page: perPage.value,
  notes: '' as string,
});

const processingStaffId = ref<number | null>(null)
const alertMessage = ref<string | null>(null)
const alertType = ref<'success' | 'error' | null>(null)

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch(searchQuery, (val) => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get(route('admin.staff-payouts.index'), { per_page: perPage.value, search: val, sort: sortField.value, direction: sortDirection.value }, { preserveState: true, replace: true });
  }, 300);
});

// Per-page change triggers reload preserving search
watch(perPage, (value) => {
  router.get(route('admin.staff-payouts.index'), { per_page: value, search: searchQuery.value, sort: sortField.value, direction: sortDirection.value }, { preserveState: true, replace: true });
});

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
  router.get(route('admin.staff-payouts.index'), { per_page: perPage.value, search: searchQuery.value, sort: sortField.value, direction: sortDirection.value }, { preserveState: true, replace: true });
}

const showProcessModal = ref(false)
const pendingProcess: any = ref({ staffId: null, staffName: '', amount: 0 })
function openProcessModal(staffId: number, staffName: string, amount: unknown) {
  pendingProcess.value = { staffId, staffName, amount }
  form.notes = ''
  showProcessModal.value = true
}
function confirmProcess(notes: string) {
  const { staffId, staffName, amount } = pendingProcess.value
  alertMessage.value = null
  alertType.value = null
  processingStaffId.value = staffId
  form.transform(() => ({ staff_id: staffId, notes }))
    .post(route('admin.staff-payouts.store'), {
      preserveScroll: true,
      onError: (errors) => {
        alertMessage.value = Object.values(errors)[0] as string ?? 'Failed to process payout.'
        alertType.value = 'error'
      },
      onFinish: () => {
        processingStaffId.value = null
        showProcessModal.value = false
      }
    })
}

const formatCurrency = (value: unknown) => {
  const amount = parseFloat(String(value ?? '0'));
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

// Use the export composable for print functionality
const { printCurrentView } = useExport({ 
  routeName: 'admin.staff-payouts', 
  filters: { preview: true } 
});

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
  indexAxis: 'y' as const, // This makes it horizontal
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
  scales: {
    x: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'Earnings (USD)'
      }
    },
    y: {
      title: {
        display: true,
        text: 'Staff Members'
      }
    }
  }
};

// Pagination functions
function changePage(url: string) {
  if (!url) return;
  router.get(url, {}, { preserveState: true });
}
</script>

<template>
  <Head title="Staff Payouts" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Header with title and print button -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex flex-col sm:flex-row sm:items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Staff Payouts</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage staff payouts and track earnings</p>
            </div>
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <button @click="printCurrentView" class="btn-glass btn-glass-sm flex items-center gap-2">
              <Printer class="icon w-4 h-4" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Alert messages -->
      <div v-if="alertMessage" :class="['rounded-md p-4 text-sm', alertType==='success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200']">
        {{ alertMessage }}
      </div>

      <!-- Search and filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search staff..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

       <div>
            <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
            <PerPageSelect v-model="perPage" id="perPage" />
          </div>
      </div>

      <!-- Staff table with improved styling -->
      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Staff Payouts (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('first_name')">Staff Member</th>
              <th class="px-6 py-3 text-center">Unpaid Visits</th>
              <th class="px-6 py-3 text-center">Patients Visited</th>
              <th class="px-6 py-3 text-center">Hours Logged</th>
              <th class="px-6 py-3">Total Unpaid Amount</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('pending_payout_requests_count')">Pending Requests</th>
              <th class="px-6 py-3 text-right actions-column print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="staff in staffWithEarnings?.data" :key="staff.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 font-medium">
                <div class="flex items-center gap-2">
                  <span>{{ staff.first_name }} {{ staff.last_name }}</span>
                  <span v-if="(staff.pending_payout_requests_count ?? 0) > 0" class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Requested</span>
                </div>
              </td>
              <td class="px-6 py-4 text-center">{{ staff.unpaid_visits_count }}</td>
              <td class="px-6 py-4 text-center">{{ staff.unique_patients_count }}</td>
              <td class="px-6 py-4 text-center">{{ staff.total_hours_logged }}</td>
              <td class="px-6 py-4 font-semibold text-green-600">{{ formatCurrency(staff.total_unpaid_cost) }}</td>
              <td class="px-6 py-4 text-center">{{ staff.pending_payout_requests_count ?? 0 }}</td>
              <td class="px-6 py-4 text-right actions-column print:hidden">
                <div class="flex justify-end gap-2">
                  <button
                    v-if="staff.unpaid_visits_count > 0"
                    @click="openProcessModal(staff.id, `${staff.first_name} ${staff.last_name}`, staff.total_unpaid_cost)"
                    :disabled="processingStaffId === staff.id"
                    class="inline-flex items-center gap-2 text-sm px-3 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md transition disabled:opacity-50"
                  >
                    <svg v-if="processingStaffId === staff.id" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <DollarSign v-else class="h-4 w-4" />
                    <span class="hidden sm:inline">{{ processingStaffId === staff.id ? 'Processing...' : 'Process Payout' }}</span>
                  </button>
                  <!-- View the most recent payout for this staff member, or create a new one if none exists -->
                  <Link 
                    v-if="staff.latest_payout_id"
                    :href="route('admin.staff-payouts.show', staff.latest_payout_id)"
                    class="inline-flex items-center gap-2 text-sm px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition"
                  >
                    <Eye class="h-4 w-4" />
                    <span class="hidden sm:inline">View</span>
                  </Link>
                  <Link 
                    v-else
                    :href="route('admin.staff-payouts.create') + '?staff_id=' + staff.id" 
                    class="inline-flex items-center gap-2 text-sm px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition"
                  >
                    <Eye class="h-4 w-4" />
                    <span class="hidden sm:inline">View</span>
                  </Link>
                </div>
                <div v-if="staff.unpaid_visits_count === 0" class="text-xs text-gray-400">
                  No pending payments
                </div>
              </td>
            </tr>
            <tr v-if="staffWithEarnings?.data?.length === 0">
              <td colspan="7" class="text-center px-6 py-8 text-gray-400">
                <FileText class="mx-auto h-12 w-12 text-gray-400" />
                <div class="mt-2">No staff with unpaid visits found.</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row justify-between items-center mt-6 print:hidden">
        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2 sm:mb-0">
          Showing {{ staffWithEarnings?.meta?.from || 0 }} to {{ staffWithEarnings?.meta?.to || 0 }} of {{ staffWithEarnings?.meta?.total || 0 }} results
        </div>
        <div v-if="staffWithEarnings?.data?.length > 0" class="flex items-center space-x-1">
          <button 
            v-for="link in staffWithEarnings?.links" 
            :key="link.label"
            @click="changePage(link.url)"
            :disabled="!link.url || link.active"
            :class="[
              'px-3 py-1 rounded text-sm',
              link.active 
                ? 'bg-blue-600 text-white' 
                : link.url 
                  ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600' 
                  : 'text-gray-400 dark:text-gray-500 cursor-not-allowed'
            ]"
            v-html="link.label"
          >
          </button>
        </div>
      </div>

      <!-- Performance Chart -->
      <div v-if="performanceData.length > 0" class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow h-80 chart-container print:hidden">
        <Bar :data="chartData" :options="chartOptions" />
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ new Date().toLocaleDateString() }}</p>
      </div>
    </div>
    
    <!-- Process Payout Modal -->
    <div v-if="showProcessModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Process Payout</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 dark:text-gray-300">Add notes for this payout (optional).</p>
                  <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea 
                      id="notes" 
                      v-model="form.notes"
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
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-cyan-600 text-base font-medium text-white hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 sm:ml-3 sm:w-auto sm:text-sm"
              @click="confirmProcess(form.notes)"
            >
              Process
            </button>
            <button 
              type="button" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-white dark:border-gray-500"
              @click="showProcessModal = false"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page {
    size: A4 landscape;
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .print\:hidden {
    display: none !important;
  }

  .hidden.print\:block {
    display: block !important;
  }

  /* Print header styling */
  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
  }

  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .print-clinic-name {
    font-size: 1.8rem !important;
    margin-bottom: 0.2rem !important;
    line-height: 1.2 !important;
  }

  .print-document-title {
    font-size: 0.9rem !important;
    color: #555 !important;
  }

  /* Table styling for print */
  .print-table {
    width: 100% !important;
    border-collapse: collapse !important;
    margin: 0 !important;
  }

  .print-table th,
  .print-table td {
    border: 1px solid #ccc !important;
    padding: 4px 6px !important;
    font-size: 10px !important;
    text-align: left !important;
  }

  .print-table thead {
    background-color: #f3f4f6 !important;
  }
}
</style>