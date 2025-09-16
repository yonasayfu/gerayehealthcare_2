<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import MarketingAnalyticsDashboard from '@/components/MarketingAnalyticsDashboard.vue';
import Tooltip from '@/components/Tooltip.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { formatDistanceToNow } from 'date-fns';
import axios from 'axios';

import { DollarSign, Users, CreditCard, Activity, Bell } from 'lucide-vue-next';
// Removed RecentSales as it's no longer directly used in the template
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip as ChartTooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, ChartTooltip, Legend, BarElement, CategoryScale, LinearScale)

const currentTab = ref('Overview'); // State for active tab

function handleTabChange(tab: string) {
  currentTab.value = tab;
}

// Global date range presets (Today, MTD, Last 30, YTD)
type RangePreset = 'TODAY' | 'MTD' | 'LAST_30' | 'YTD';
const rangePreset = ref<RangePreset>('MTD');
const rangeStart = ref<string>('');
const rangeEnd = ref<string>('');

function applyRange(preset: RangePreset) {
  const now = new Date();
  const start = new Date();
  if (preset === 'TODAY') {
    start.setHours(0,0,0,0);
  } else if (preset === 'MTD') {
    start.setDate(1); start.setHours(0,0,0,0);
  } else if (preset === 'LAST_30') {
    start.setDate(now.getDate() - 29); start.setHours(0,0,0,0);
  } else if (preset === 'YTD') {
    start.setMonth(0,1); start.setHours(0,0,0,0);
  }
  rangePreset.value = preset;
  rangeStart.value = start.toISOString().slice(0,10);
  rangeEnd.value = now.toISOString().slice(0,10);
  // If we're on Overview, refresh data immediately
  if (currentTab.value === 'Overview') {
    refreshOverview();
  }
}

// initialize preset on mount to ensure all functions are defined
onMounted(() => {
  applyRange('MTD');
});

function refreshOverview() {
  fetchOverviewData();
  fetchOverviewSeries();
  fetchRecentAppointments();
  fetchTopServices();
  fetchTopStaffHours();
}

// Overview series (monthly registrations/visits) for the Overview tab bar chart
const overviewSeries = ref<{ labels: string[]; datasets: any[] }>({ labels: [], datasets: [] });
const barChartData = computed(() => ({
  labels: overviewSeries.value.labels,
  datasets: overviewSeries.value.datasets,
}));

const hasChartData = computed(() => {
  const ds = overviewSeries.value.datasets || [];
  if (!ds.length) return false;
  // any dataset has any non-zero value
  return ds.some((d: any) => Array.isArray(d.data) && d.data.some((n: any) => Number(n) > 0));
});

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

// Pie chart removed from Overview; keep marketing pie usage inside MarketingAnalyticsDashboard component

// Recent appointments: fetched dynamically
interface Appointment {
  patient: string | { first_name: string | null; last_name: string | null } | null;
  date: string;
  time: string;
  status: string;
  date_label?: string;
}
const recentAppointments = ref<Appointment[]>([]);
const upcomingVisits = ref<Appointment[]>([]);

// Sorting & pagination for Recent Appointments
type SortKey = 'patient' | 'date' | 'time' | 'status' | 'staff' | 'service';
const sortKey = ref<SortKey>('date');
const sortDir = ref<'asc' | 'desc'>('desc');
const page = ref(1);
const pageSize = ref(8);

const sortedAppointments = computed(() => {
  const copy = [...recentAppointments.value] as any[];
  copy.sort((a, b) => {
    const av = (a as any)[sortKey.value] ?? '';
    const bv = (b as any)[sortKey.value] ?? '';
    const cmp = String(av).localeCompare(String(bv));
    return sortDir.value === 'asc' ? cmp : -cmp;
  });
  return copy;
});

const pagedAppointments = computed(() => {
  const start = (page.value - 1) * pageSize.value;
  return sortedAppointments.value.slice(start, start + pageSize.value);
});

function setSort(key: SortKey) {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortDir.value = 'asc';
  }
  page.value = 1;
}

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
const analyticsLoading = ref(false);

// Reports charts state
const reportServiceChart = ref<{ labels: string[]; datasets: any[] }>({ labels: [], datasets: [] });
const reportRevenueArChart = ref<{ labels: string[]; datasets: any[] }>({ labels: [], datasets: [] });
const topServices = ref<any[]>([]);
const topServicesChartData = computed(() => ({
  labels: topServices.value.map((s: any) => s.name),
  datasets: [
    {
      label: 'Volume',
      data: topServices.value.map((s: any) => Number(s.volume || 0)),
      backgroundColor: 'rgba(59,130,246,0.7)',
      borderRadius: 6,
      barThickness: 'flex',
      maxBarThickness: 22,
    },
    {
      label: 'Revenue',
      data: topServices.value.map((s: any) => Number(s.revenue || 0)),
      backgroundColor: 'rgba(16,185,129,0.7)',
      borderRadius: 6,
      barThickness: 'flex',
      maxBarThickness: 22,
      yAxisID: 'y1',
    },
  ],
}));

const topServicesChartOptions = {
  indexAxis: 'y' as const,
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top' },
    tooltip: { mode: 'index', intersect: false },
  },
  scales: {
    x: { ticks: { autoSkip: true } },
    y: { beginAtZero: true },
    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
  },
};

// Reports tab data (arrays)
const reportsRevenueAR = ref<any[]>([]);
const reportsServiceVolume = ref<any[]>([]);

// Currency formatter (fallback to USD if not provided via global)
const appCurrency = (window as any)?.appCurrency || 'USD';
const money = (v: number) => new Intl.NumberFormat(undefined, { style: 'currency', currency: appCurrency, minimumFractionDigits: 2 }).format(Number(v || 0));

// Notifications tab data
const notifications = ref<any[]>([]);
const unreadCount = ref<number>(0);

// Top Staff by hours (range)
const topStaff = ref<Array<{ staff_id: number; first_name: string; last_name: string; hours: number }>>([])
const fetchTopStaffHours = async () => {
  try {
    const params = { start: rangeStart.value, end: rangeEnd.value } as any
    const { data } = await axios.get(route('admin.top-staff-hours'), { params })
    topStaff.value = data || []
  } catch (e) {
    console.error('Error fetching top staff hours:', e)
  }
}

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
  const params = { start_date: rangeStart.value, end_date: rangeEnd.value } as any;
  const response = await axios.get(route('admin.marketing-analytics.dashboard-data'), { params });
  dashboardStats.value = response.data;
};

// Overview tab data loader
const fetchOverviewData = async () => {
  try {
    const params = { start: rangeStart.value, end: rangeEnd.value, noCache: true } as any;
    const response = await axios.get(route('admin.overview-data'), { params });
    superAdminStats.value = response.data;
  } catch (error) {
    console.error('Error fetching overview data:', error);
  }
};

const fetchOverviewSeries = async () => {
  try {
    const params = { start: rangeStart.value, end: rangeEnd.value, noCache: true } as any;
    const response = await axios.get(route('admin.overview-series'), { params });
    overviewSeries.value = response.data;
  } catch (error) {
    console.error('Error fetching overview series:', error);
  }
};

// Helpers + loader for recent appointments
const formatDateFriendly = (isoDate: string) => {
  const d = new Date(isoDate);
  const today = new Date();
  const tomorrow = new Date();
  tomorrow.setDate(today.getDate() + 1);
  const sameDay = (a: Date, b: Date) => a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  if (sameDay(d, today)) return 'Today';
  if (sameDay(d, tomorrow)) return 'Tomorrow';
  return isoDate;
};

const fetchRecentAppointments = async () => {
  try {
    const params = { start: rangeStart.value, end: rangeEnd.value } as any;
    const response = await axios.get(route('admin.recent-appointments'), { params });
    recentAppointments.value = (response.data || []).map((a: any) => ({ ...a, date_label: formatDateFriendly(a.date) }));
    // If today has no data, fetch upcoming 24h
    if (!recentAppointments.value.length) {
      // fallback 1: upcoming next 24h
      const up = await axios.get(route('admin.upcoming-visits'));
      upcomingVisits.value = (up.data || []).map((a: any) => ({ ...a, date_label: formatDateFriendly(a.date) }));
      // fallback 2: last 30 days if still empty
      if (!upcomingVisits.value.length) {
        const today = new Date();
        const start = new Date(); start.setDate(today.getDate()-29);
        const params30 = { start: start.toISOString().slice(0,10), end: today.toISOString().slice(0,10) } as any;
        const last30 = await axios.get(route('admin.recent-appointments'), { params: params30 });
        recentAppointments.value = (last30.data || []).map((a: any) => ({ ...a, date_label: formatDateFriendly(a.date) }));
      }
    } else {
      upcomingVisits.value = [];
    }
  } catch (error) {
    console.error('Error fetching recent appointments:', error);
  }
};

const fetchCampaignPerformance = async () => {
  try {
    const params = { start_date: rangeStart.value, end_date: rangeEnd.value, per_page: 6 } as any;
    const response = await axios.get(route('admin.marketing-analytics.campaign-performance'), { params });
    campaignPerformanceData.value = response.data.data; // Assuming paginated data
  } catch (error) {
    console.error('Error fetching campaign performance:', error);
  }
};

const fetchTrafficSourceDistribution = async () => {
  try {
    const params = { start_date: rangeStart.value, end_date: rangeEnd.value } as any;
    const response = await axios.get(route('admin.marketing-analytics.traffic-source-distribution'), { params });
    trafficSourceData.value = response.data;
  } catch (error) {
    console.error('Error fetching traffic source distribution:', error);
  }
};

const fetchConversionFunnel = async () => {
  try {
    const params = { start_date: rangeStart.value, end_date: rangeEnd.value } as any;
    const response = await axios.get(route('admin.marketing-analytics.conversion-funnel'), { params });
    conversionFunnelData.value = response.data;
  } catch (error) {
    console.error('Error fetching conversion funnel:', error);
  }
};

// Batch analytics fetch to reduce flicker
const fetchAnalyticsAll = async () => {
  analyticsLoading.value = true;
  try {
    await Promise.all([
      fetchDashboardData(),
      fetchCampaignPerformance(),
      fetchTrafficSourceDistribution(),
      fetchConversionFunnel(),
    ]);
  } catch (e) {
    console.error('Error fetching analytics data:', e);
  } finally {
    analyticsLoading.value = false;
  }
};

const fetchTopServices = async () => {
  try {
    const params = { start: rangeStart.value, end: rangeEnd.value } as any;
    const res = await axios.get(route('admin.top-services'), { params });
    topServices.value = res.data || [];
  } catch (error) {
    console.error('Error fetching top services:', error);
  }
};

// Reports data loaders
const fetchReportsData = async () => {
  const params = { start: rangeStart.value, end: rangeEnd.value } as any;
  const getUrl = (name: string, fallback: string) => {
    try { return route(name); } catch { return fallback; }
  };
  const svcUrl = getUrl('admin.reports.service-volume.data', '/dashboard/reports/service-volume/data');
  const revUrl = getUrl('admin.reports.revenue-ar.data', '/dashboard/reports/revenue-ar/data');
  try {
    const [svc, rev] = await Promise.all([
      axios.get(svcUrl, { params }),
      axios.get(revUrl, { params }),
    ]);
    reportServiceChart.value = (svc.data && svc.data.labels) ? svc.data : { labels: [], datasets: [] };
    reportRevenueArChart.value = (rev.data && rev.data.labels) ? rev.data : { labels: [], datasets: [] };
  } catch (error) {
    console.error('Error fetching reports data:', error);
    reportServiceChart.value = { labels: [], datasets: [] };
    reportRevenueArChart.value = { labels: [], datasets: [] };
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
    fetchAnalyticsAll();
  }
  if (newTab === 'Overview') {
    refreshOverview();
  }
  if (newTab === 'Reports') {
    fetchReportsData();
  }
  if (newTab === 'Notifications') {
    fetchNotifications();
  }
}, { immediate: true }); // Fetch data immediately if the initial tab is 'Analytics' or 'Overview'

// React to date range changes (debounced to reduce flicker)
let rangeTimer: any = null;
watch([rangeStart, rangeEnd], () => {
  if (rangeTimer) clearTimeout(rangeTimer);
  rangeTimer = setTimeout(() => {
    if (currentTab.value === 'Overview') refreshOverview();
    if (currentTab.value === 'Analytics') fetchAnalyticsAll();
    if (currentTab.value === 'Reports') fetchReportsData();
  }, 200);
});
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <div class="flex-1 space-y-4 p-8 pt-6">
      <DashboardTabs @tab-change="handleTabChange" />

      <!-- Date Range Presets -->
      <div class="flex items-center gap-2">
        <Tooltip text="Today: data from 00:00 to now"><button class="px-3 py-1 rounded border" :class="rangePreset==='TODAY' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800'" @click="applyRange('TODAY')">Today</button></Tooltip>
        <Tooltip text="MTD: Month-To-Date starting from the 1st"><button class="px-3 py-1 rounded border" :class="rangePreset==='MTD' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800'" @click="applyRange('MTD')">MTD</button></Tooltip>
        <Tooltip text="Last 30: Rolling last 30 calendar days"><button class="px-3 py-1 rounded border" :class="rangePreset==='LAST_30' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800'" @click="applyRange('LAST_30')">Last 30</button></Tooltip>
        <Tooltip text="YTD: Year-To-Date starting Jan 1"><button class="px-3 py-1 rounded border" :class="rangePreset==='YTD' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800'" @click="applyRange('YTD')">YTD</button></Tooltip>
        <span class="ml-3 text-sm text-muted-foreground">Range: {{ rangeStart }} → {{ rangeEnd }}</span>
      </div>

      <div v-if="currentTab === 'Overview'">
        <!-- Row 1: Super Admin Stat Cards -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
          <Tooltip text="All-time count of registered patients">
            <StatCard
              title="Total Patients"
              :value="superAdminStats.totalPatients.toLocaleString()"
              change=""
              :icon="Users"
              color="bg-blue-100"
            />
          </Tooltip>
          <Tooltip text="Patients created within the selected date range">
            <StatCard
              title="Patients In Range"
              :value="(superAdminStats.patientsInRange ?? superAdminStats.newPatientsThisMonth).toLocaleString()"
              change=""
              :icon="Activity"
              color="bg-green-100"
            />
          </Tooltip>
          <!-- Combined Visits: Today + Upcoming 24h in one card -->
          <Tooltip text="Today vs. upcoming visits (next 24h)">
            <div class="p-4 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 shadow flex items-center gap-4">
              <Activity class="h-8 w-8" />
              <div class="flex-1">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Visits Today</div>
                    <div class="text-2xl font-semibold">{{ superAdminStats.visitsToday.toLocaleString() }}</div>
                  </div>
                  <div class="text-gray-400 mx-3">|</div>
                  <div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Upcoming 24h</div>
                    <div class="text-2xl font-semibold">{{ (superAdminStats.upcomingVisitsCount ?? 0).toLocaleString() }}</div>
                  </div>
                </div>
              </div>
            </div>
          </Tooltip>
          <Tooltip text="Total visits scheduled within the selected range">
            <StatCard
              title="Service Volume (MTD)"
              :value="superAdminStats.serviceVolumeThisMonth.toLocaleString()"
              change=""
              :icon="Activity"
              color="bg-purple-100"
            />
          </Tooltip>
          <Tooltip text="Sum of paid invoice amounts within the range">
            <StatCard
              title="Total Revenue"
              :value="money(superAdminStats.totalRevenue)"
              change=""
              :icon="CreditCard"
              color="bg-teal-100"
            />
          </Tooltip>
          <Tooltip text="Unpaid invoice amounts within the range">
            <StatCard
              title="Accounts Receivable"
              :value="money(superAdminStats.accountsReceivable)"
              change=""
              :icon="DollarSign"
              color="bg-orange-100"
            />
          </Tooltip>
          <Tooltip text="Insurance claims awaiting payment within the range">
            <StatCard
              title="Claims Pending"
              :value="superAdminStats.claimsPending.toLocaleString()"
              change=""
              :icon="CreditCard"
              color="bg-pink-100"
            />
          </Tooltip>
          <Tooltip text="All-time active staff on the platform">
            <StatCard
              title="Active Staff"
              :value="superAdminStats.activeStaff.toLocaleString()"
              change=""
              :icon="Users"
              color="bg-indigo-100"
            />
          </Tooltip>
          <!-- Removed standalone upcoming visits card (merged above) -->
        </div>

        <!-- Row 2: Bar Chart, Pie Chart, and Table -->
        <div class="grid gap-4 grid-cols-1 lg:grid-cols-3 mt-4">
          <div class="col-span-full lg:col-span-1">
            <h3 class="text-lg font-medium">Monthly Patient Registrations Overview</h3>
            <div class="relative h-[420px] mt-4 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
              <Bar :data="barChartData" :options="barChartOptions" />
              <div v-if="!hasChartData" class="absolute inset-0 flex items-center justify-center text-gray-500">
                No data for selected range
              </div>
            </div>
          </div>

          <!-- Make table take remaining space next to chart -->
          <div class="col-span-full lg:col-span-2">
            <h3 class="text-lg font-medium">Recent Appointments</h3>
            <div class="mt-4 overflow-x-auto">
              <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden print-table">
                <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 cursor-pointer select-none" @click="setSort('patient')">Patient <span v-if="sortKey==='patient'">{{ sortDir==='asc' ? '▲' : '▼' }}</span></th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 cursor-pointer select-none" @click="setSort('date')">Date <span v-if="sortKey==='date'">{{ sortDir==='asc' ? '▲' : '▼' }}</span></th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 cursor-pointer select-none" @click="setSort('time')">Time <span v-if="sortKey==='time'">{{ sortDir==='asc' ? '▲' : '▼' }}</span></th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 cursor-pointer select-none" @click="setSort('status')">Status <span v-if="sortKey==='status'">{{ sortDir==='asc' ? '▲' : '▼' }}</span></th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 cursor-pointer select-none" @click="setSort('staff')">Staff <span v-if="sortKey==='staff'">{{ sortDir==='asc' ? '▲' : '▼' }}</span></th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300 cursor-pointer select-none" @click="setSort('service')">Service <span v-if="sortKey==='service'">{{ sortDir==='asc' ? '▲' : '▼' }}</span></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(appointment, index) in pagedAppointments" :key="index" :class="{'bg-gray-50 dark:bg-gray-700': index % 2 === 0}">
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">
                      {{ typeof appointment.patient === 'string'
                        ? (appointment.patient || 'N/A')
                        : (appointment.patient
                            ? `${appointment.patient.first_name || 'N/A'} ${appointment.patient.last_name || 'N/A'}`
                            : 'N/A')
                      }}
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">
                      <span class="font-medium" v-if="appointment.date_label === 'Today' || appointment.date_label === 'Tomorrow'">{{ appointment.date_label }}</span>
                      <span v-else>{{ appointment.date }}</span>
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">{{ appointment.time }}</td>
                    <td class="py-2 px-4 text-sm">
                      <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="{
                          'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200': (appointment.status || '').toLowerCase() === 'confirmed',
                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200': (appointment.status || '').toLowerCase() === 'pending',
                          'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200': ['cancelled','canceled'].includes((appointment.status || '').toLowerCase()),
                          'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200': ['completed','done','finished','checked-in'].includes((appointment.status || '').toLowerCase()),
                        }"
                      >
                        {{ appointment.status || '—' }}
                      </span>
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">{{ (appointment as any).staff || '—' }}</td>
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">{{ (appointment as any).service || '—' }}</td>
                  </tr>
                </tbody>
              </table>
              <div v-if="!pagedAppointments.length && !upcomingVisits.length" class="py-8 text-center text-sm text-muted-foreground">No appointments found for the selected range.</div>
              <!-- Pagination Controls -->
              <div class="flex items-center justify-between py-2">
                <div class="text-sm text-muted-foreground">Page {{ page }} of {{ Math.max(1, Math.ceil(sortedAppointments.length / pageSize)) }}</div>
                <div class="space-x-2">
                  <button class="px-3 py-1 rounded border" :disabled="page<=1" @click="page = Math.max(1, page-1)">Prev</button>
                  <button class="px-3 py-1 rounded border" :disabled="page>=Math.ceil(sortedAppointments.length / pageSize)" @click="page = Math.min(Math.ceil(sortedAppointments.length / pageSize), page+1)">Next</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 3: Top Staff by Hours -->
        <div class="grid gap-4 grid-cols-1 mt-6">
          <div class="col-span-full">
            <h3 class="text-lg font-medium">Top Staff by Hours ({{ rangeStart }} → {{ rangeEnd }})</h3>
            <div class="mt-4 overflow-x-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
              <table class="min-w-full print-table">
                <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">#</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Staff</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Hours</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, idx) in topStaff" :key="row.staff_id" :class="{'bg-gray-50 dark:bg-gray-700': idx % 2 === 0}">
                    <td class="py-2 px-4 text-sm">{{ idx + 1 }}</td>
                    <td class="py-2 px-4 text-sm">{{ row.first_name }} {{ row.last_name }}</td>
                    <td class="py-2 px-4 text-sm font-semibold">{{ row.hours.toFixed(2) }}</td>
                  </tr>
                  <tr v-if="!topStaff.length">
                    <td colspan="3" class="py-4 px-4 text-center text-gray-500">No data for selected range</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Top Services (by volume/revenue) as bar chart -->
        <div class="mt-6">
          <h3 class="text-lg font-semibold">Top Services ({{ rangeStart }} → {{ rangeEnd }})</h3>
          <div class="relative h-[320px] mt-4 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <Bar :data="topServicesChartData" :options="topServicesChartOptions" />
            <div v-if="!topServices.length" class="absolute inset-0 flex items-center justify-center text-gray-500">
              No service data for selected range
            </div>
          </div>
        </div>

        <!-- AR Aging Summary -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
          <Tooltip text="Accounts Receivable: Invoices outstanding for 0-30 days">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
              <div class="text-sm text-muted-foreground">AR 0–30 days</div>
              <div class="text-2xl font-semibold">{{ (superAdminStats as any).arAging?.['0_30'] ?? 0 }}</div>
            </div>
          </Tooltip>
          <Tooltip text="Accounts Receivable: Invoices outstanding for 31-60 days">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
              <div class="text-sm text-muted-foreground">AR 31–60 days</div>
              <div class="text-2xl font-semibold">{{ (superAdminStats as any).arAging?.['31_60'] ?? 0 }}</div>
            </div>
          </Tooltip>
          <Tooltip text="Accounts Receivable: Invoices outstanding for 61+ days">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
              <div class="text-sm text-muted-foreground">AR 61+ days</div>
              <div class="text-2xl font-semibold">{{ (superAdminStats as any).arAging?.['61_plus'] ?? 0 }}</div>
            </div>
          </Tooltip>
        </div>

        <!-- Upcoming Visits (Next 24h) -->
        <div v-if="!recentAppointments.length && upcomingVisits.length" class="mt-6">
          <h3 class="text-lg font-semibold">Upcoming Visits (Next 24h)</h3>
          <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full print-table">
                <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Patient</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Date</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Time</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Staff</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Service</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(v, i) in upcomingVisits" :key="i" class="hover:bg-gray-50 dark:hover:bg-gray-700/60">
                    <td class="py-2 px-4 text-sm">{{ v.patient }}</td>
                    <td class="py-2 px-4 text-sm">{{ v.date_label || v.date }}</td>
                    <td class="py-2 px-4 text-sm">{{ v.time }}</td>
                    <td class="py-2 px-4 text-sm">{{ (v as any).staff || '—' }}</td>
                    <td class="py-2 px-4 text-sm">{{ (v as any).service || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="currentTab === 'Analytics'">
        <MarketingAnalyticsDashboard
          :dashboardStats="dashboardStats"
          :campaignPerformanceData="campaignPerformanceData"
          :trafficSourceData="trafficSourceData"
          :conversionFunnelData="conversionFunnelData"
          :loading="analyticsLoading"
        />
      </div>

      <div v-else-if="currentTab === 'Reports'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">Operational Reports</h3>
        <p class="text-muted-foreground mb-4">Range: {{ rangeStart }} → {{ rangeEnd }}</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Revenue vs AR -->
          <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
            <h4 class="text-lg font-medium mb-2">Revenue vs Accounts Receivable</h4>
            <div class="relative h-[300px]">
              <Bar :data="reportRevenueArChart" :options="barChartOptions" />
              <div v-if="!(reportRevenueArChart?.datasets?.length && reportRevenueArChart.labels?.length)" class="absolute inset-0 flex items-center justify-center text-gray-500">No data for selected range</div>
            </div>
          </div>

          <!-- Service Volume -->
          <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
            <h4 class="text-lg font-medium mb-2">Service Volume (Visits)</h4>
            <div class="relative h-[300px]">
              <Bar :data="reportServiceChart" :options="barChartOptions" />
              <div v-if="!(reportServiceChart?.datasets?.length && reportServiceChart.labels?.length)" class="absolute inset-0 flex items-center justify-center text-gray-500">No data for selected range</div>
            </div>
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
