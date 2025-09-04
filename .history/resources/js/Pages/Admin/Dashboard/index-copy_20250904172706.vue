Dashboard 

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import MarketingAnalyticsDashboard from '@/components/MarketingAnalyticsDashboard.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { formatDistanceToNow } from 'date-fns';
import axios from 'axios';

import { DollarSign, Users, CreditCard, Activity, Bell } from 'lucide-vue-next';
// Removed RecentSales as it's no longer directly used in the template
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const currentTab = ref('Overview'); // State for active tab

function handleTabChange(tab: string) {
  currentTab.value = tab;
}

// Overview series (monthly registrations/visits) for the Overview tab bar chart
const overviewSeries = ref({ labels: [], datasets: [] });
const barChartData = computed(() => ({
  labels: overviewSeries.value.labels,
  datasets: overviewSeries.value.datasets,
}));

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top', labels: { boxWidth: 12 } },
    tooltip: { mode: 'index', intersect: false },
    title: { display: false },
  },
  scales: {
    x: { ticks: { autoSkip: true, maxRotation: 0 } },
    y: { beginAtZero: true, ticks: { precision: 0 } },
  },
};

interface Appointment {
  patient: string | { first_name: string | null; last_name: string | null } | null; // support API returning full name string
  date: string;
  time: string;
  status: string;
}

const recentAppointments = ref<Appointment[]>([]);

const props = defineProps({
  stats: {
    type: Object,
    required: false,
  },
});

// Super Admin Overview KPIs
const superAdminStats = ref({
  totalPatients: 0,
  newPatientsThisMonth: 0,
  visitsToday: 0,
  serviceVolumeThisMonth: 0,
  totalRevenue: 0,
  accountsReceivable: 0,
  claimsPending: 0,
  activeStaff: 0,
  lowStockAlerts: 0,
  openTasks: 0,
});

const campaignPerformanceData = ref([]);
const trafficSourceData = ref([]);
const conversionFunnelData = ref({});

// Reports tab data (arrays)
const reportsRevenueAR = ref<any[]>([]);
const reportsServiceVolume = ref<any[]>([]);

// Currency formatter (fallback to USD if not provided via global)
const appCurrency = (window as any)?.appCurrency || 'USD';
const money = (v: number) => new Intl.NumberFormat(undefined, { style: 'currency', currency: appCurrency, minimumFractionDigits: 2 }).format(Number(v || 0));

// Notifications tab data
const notifications = ref<any[]>([]);
const unreadCount = ref<number>(0);

// Marketing analytics data loaders (Analytics tab)
const dashboardStats = ref({
  totalLeads: 0,
  convertedLeads: 0,
  conversionRate: 0,
  totalMarketingSpend: 0,
  patientsAcquired: 0,
  cpa: 0,
  revenueGenerated: 0,
  roi: 0,
});

const fetchDashboardData = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.dashboard-data'));
    dashboardStats.value = response.data;
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

// Overview tab data loader
const fetchOverviewData = async () => {
  try {
    const response = await axios.get(route('admin.overview-data'));
    superAdminStats.value = response.data;
  } catch (error) {
    console.error('Error fetching overview data:', error);
  }
};

const fetchOverviewSeries = async () => {
  try {
    const response = await axios.get(route('admin.overview-series'));
    const data = response.data || {};
    const labels = Array.isArray(data.labels) ? data.labels : [];
    const rawDatasets = Array.isArray(data.datasets) ? data.datasets : [];

    const palette = [
      'rgba(59,130,246,0.7)', // blue-500
      'rgba(16,185,129,0.7)', // emerald-500
      'rgba(234,179,8,0.7)',  // yellow-500
      'rgba(168,85,247,0.7)', // purple-500
      'rgba(236,72,153,0.7)', // pink-500
      'rgba(249,115,22,0.7)', // orange-500
    ];

    const datasets = rawDatasets.map((ds: any, i: number) => ({
      label: ds.label || `Series ${i + 1}`,
      data: Array.isArray(ds.data) ? ds.data : [],
      backgroundColor: ds.backgroundColor || palette[i % palette.length],
      borderColor: ds.borderColor || palette[i % palette.length].replace('0.7', '1'),
      borderWidth: 1,
      borderRadius: 6,
      barThickness: 'flex',
      maxBarThickness: 40,
    }));

    overviewSeries.value = { labels, datasets };
  } catch (error) {
    console.error('Error fetching overview series:', error);
  }
};

const formatDateFriendly = (isoDate: string) => {
  const d = new Date(isoDate);
  const today = new Date();
  const tomorrow = new Date();
  tomorrow.setDate(today.getDate() + 1);
  const sameDay = (a: Date, b: Date) => a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  if (sameDay(d, today)) return 'Today';
  if (sameDay(d, tomorrow)) return 'Tomorrow';
  return d.toISOString().slice(0,10);
};

const fetchRecentAppointments = async () => {
  try {
    const response = await axios.get(route('admin.recent-appointments'));
    recentAppointments.value = (response.data || []).map((a: any) => ({
      ...a,
      date_label: formatDateFriendly(a.date),
    }));
  } catch (error) {
    console.error('Error fetching recent appointments:', error);
  }
};

const fetchCampaignPerformance = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.campaign-performance'));
    campaignPerformanceData.value = response.data.data; // Assuming paginated data
  } catch (error) {
    console.error('Error fetching campaign performance:', error);
  }
};

const fetchTrafficSourceDistribution = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.traffic-source-distribution'));
    trafficSourceData.value = response.data;
  } catch (error) {
    console.error('Error fetching traffic source distribution:', error);
  }
};

const fetchConversionFunnel = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.conversion-funnel'));
    conversionFunnelData.value = response.data;
  } catch (error) {
    console.error('Error fetching conversion funnel:', error);
  }
};

// Reports data loaders
const fetchReportsData = async () => {
  try {
    const [svc, rev] = await Promise.all([
      axios.get(route('admin.reports.service-volume.data')),
      axios.get(route('admin.reports.revenue-ar.data')),
    ]);
    reportsServiceVolume.value = Array.isArray(svc.data?.data) ? svc.data.data : [];
    reportsRevenueAR.value = Array.isArray(rev.data?.data) ? rev.data.data : [];
  } catch (error) {
    console.error('Error fetching reports data:', error);
  }
};

// Notifications loader
const fetchNotifications = async () => {
  try {
    const response = await axios.get(route('notifications.index'), { headers: { Accept: 'application/json' } });
    unreadCount.value = response.data.unread_count ?? 0;
    notifications.value = response.data.notifications ?? [];
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

watch(currentTab, (newTab) => {
  if (newTab === 'Analytics') {
    fetchDashboardData();
    fetchCampaignPerformance();
    fetchTrafficSourceDistribution();
    fetchConversionFunnel();
  }
  if (newTab === 'Overview') {
    fetchOverviewData();
    fetchOverviewSeries();
    fetchRecentAppointments();
  }
  if (newTab === 'Reports') {
    fetchReportsData();
  }
  if (newTab === 'Notifications') {
    fetchNotifications();
  }
}, { immediate: true }); // Fetch data immediately if the initial tab is 'Analytics' or 'Overview'
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <div class="flex-1 space-y-4 p-8 pt-6">
      <DashboardTabs @tab-change="handleTabChange" />

      <div v-if="currentTab === 'Overview'">
        <!-- Row 1: Super Admin Stat Cards -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
          <StatCard
            title="Total Patients"
            :value="superAdminStats.totalPatients.toLocaleString()"
            change=""
            :icon="Users"
            color="bg-blue-100"
          />
          <StatCard
            title="New Patients (MTD)"
            :value="superAdminStats.newPatientsThisMonth.toLocaleString()"
            change=""
            :icon="Activity"
            color="bg-green-100"
          />
          <StatCard
            title="Visits Today"
            :value="superAdminStats.visitsToday.toLocaleString()"
            change=""
            :icon="Activity"
            color="bg-yellow-100"
          />
          <StatCard
            title="Service Volume (MTD)"
            :value="superAdminStats.serviceVolumeThisMonth.toLocaleString()"
            change=""
            :icon="Activity"
            color="bg-purple-100"
          />
          <StatCard
            title="Total Revenue"
            :value="money(superAdminStats.totalRevenue)"
            change=""
            :icon="CreditCard"
            color="bg-teal-100"
          />
          <StatCard
            title="Accounts Receivable"
            :value="money(superAdminStats.accountsReceivable)"
            change=""
            :icon="DollarSign"
            color="bg-orange-100"
          />
          <StatCard
            title="Claims Pending"
            :value="superAdminStats.claimsPending.toLocaleString()"
            change=""
            :icon="CreditCard"
            color="bg-pink-100"
          />
          <StatCard
            title="Active Staff"
            :value="superAdminStats.activeStaff.toLocaleString()"
            change=""
            :icon="Users"
            color="bg-indigo-100"
          />
        </div>

        <!-- Row 2: Chart and Recent Appointments -->
        <div class="grid gap-6 grid-cols-1 lg:grid-cols-2 mt-4">
          <!-- Chart: make taller and wider on desktop -->
          <div class="col-span-1">
            <h3 class="text-lg font-semibold">Monthly Patient Registrations Overview</h3>
            <div class="h-[420px] mt-4 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
              <template v-if="barChartData?.datasets?.length && barChartData.labels?.length">
                <Bar :data="barChartData" :options="barChartOptions" />
              </template>
              <template v-else>
                <div class="h-full flex items-center justify-center text-gray-500">
                  No data available for this period.
                </div>
              </template>
            </div>
          </div>

          <!-- Recent Appointments: increase size and style -->
          <div class="col-span-1">
            <h3 class="text-lg font-semibold">Recent Appointments</h3>
            <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Patient</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Date</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Time</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr
                      v-for="(appointment, index) in recentAppointments"
                      :key="index"
                      class="hover:bg-gray-50 dark:hover:bg-gray-700/60 transition-colors"
                    >
                      <td class="py-3 px-6 text-base text-gray-900 dark:text-gray-100">
                        {{ typeof appointment.patient === 'string'
                          ? (appointment.patient || 'N/A')
                          : (appointment.patient
                              ? `${appointment.patient.first_name || 'N/A'} ${appointment.patient.last_name || 'N/A'}`
                              : 'N/A')
                        }}
                      </td>
                      <td class="py-3 px-6 text-base text-gray-900 dark:text-gray-100">
                        <span class="font-medium" v-if="appointment.date_label === 'Today' || appointment.date_label === 'Tomorrow'">{{ appointment.date_label }}</span>
                        <span v-else>{{ appointment.date }}</span>
                      </td>
                      <td class="py-3 px-6 text-base text-gray-900 dark:text-gray-100">{{ appointment.time }}</td>
                      <td class="py-3 px-6">
                        <span
                          class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                          :class="{
                            'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200': (appointment.status || '').toLowerCase() === 'confirmed',
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200': (appointment.status || '').toLowerCase() === 'pending',
                            'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200': (appointment.status || '').toLowerCase() === 'cancelled' || (appointment.status || '').toLowerCase() === 'canceled',
                            'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200': ['completed','done','finished','checked-in'].includes((appointment.status || '').toLowerCase()),
                          }"
                        >
                          {{ appointment.status || '—' }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="currentTab === 'Analytics'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <MarketingAnalyticsDashboard
          :dashboardStats="dashboardStats"
          :campaignPerformanceData="campaignPerformanceData"
          :trafficSourceData="trafficSourceData"
          :conversionFunnelData="conversionFunnelData"
        />
      </div>

      <div v-else-if="currentTab === 'Reports'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">Operational Reports</h3>
        <p class="text-muted-foreground mb-4">Generate and view detailed reports for various operational aspects.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Revenue & AR -->
          <div>
            <h4 class="text-lg font-medium mb-2">Revenue & Accounts Receivable</h4>
            <template v-if="reportsRevenueAR.length">
              <ul class="list-disc list-inside space-y-1 text-gray-800 dark:text-gray-200">
                <li>Total Billed: {{ money(reportsRevenueAR.reduce((s, r) => s + Number(r.total_billed || 0), 0)) }}</li>
                <li>Total Received: {{ money(reportsRevenueAR.reduce((s, r) => s + Number(r.total_received || 0), 0)) }}</li>
                <li>AR Outstanding: {{ money(reportsRevenueAR.reduce((s, r) => s + Number(r.ar_outstanding || 0), 0)) }}</li>
                <li>Invoices: {{ reportsRevenueAR.reduce((s, r) => s + Number(r.invoices_count || 0), 0).toLocaleString() }} (Paid: {{ reportsRevenueAR.reduce((s, r) => s + Number(r.paid_invoices || 0), 0).toLocaleString() }}, Unpaid: {{ reportsRevenueAR.reduce((s, r) => s + Number(r.unpaid_invoices || 0), 0).toLocaleString() }})</li>
              </ul>
            </template>
            <p v-else class="text-sm text-muted-foreground">No data loaded yet.</p>
          </div>

          <!-- Service Volume -->
          <div>
            <h4 class="text-lg font-medium mb-2">Service Volume</h4>
            <template v-if="reportsServiceVolume.length">
              <ul class="list-disc list-inside space-y-1 text-gray-800 dark:text-gray-200">
                <li>Total Visits: {{ reportsServiceVolume.reduce((s, r) => s + Number(r.total_visits || 0), 0).toLocaleString() }}</li>
                <li>Unique Patients: {{ reportsServiceVolume.reduce((s, r) => s + Number(r.unique_patients || 0), 0).toLocaleString() }}</li>
              </ul>
            </template>
            <p v-else class="text-sm text-muted-foreground">No data loaded yet.</p>
          </div>
        </div>
      </div>

      <div v-else-if="currentTab === 'Notifications'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">System Notifications</h3>
        <p class="text-muted-foreground mb-4">Unread: {{ unreadCount }}</p>

        <ul class="mt-4 space-y-3" v-if="notifications.length">
          <li v-for="n in notifications" :key="n.id" class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm">
            <div class="flex items-start space-x-3">
              <Bell class="h-5 w-5 text-blue-500 mt-0.5" />
              <div>
                <p class="font-medium">{{ (n.data && (n.data.title || n.data.subject)) || n.type }}</p>
                <p v-if="n.data && (n.data.message || n.data.body)" class="text-sm text-gray-700 dark:text-gray-300">
                  {{ n.data.message || n.data.body }}
                </p>
                <p class="text-xs text-muted-foreground mt-1">{{ formatDistanceToNow(new Date(n.created_at), { addSuffix: true }) }}</p>
              </div>
            </div>
          </li>
        </ul>
        <p v-else class="text-sm text-muted-foreground">No unread notifications.</p>
      </div>
    </div>
    
  </AppLayout>
</template>

