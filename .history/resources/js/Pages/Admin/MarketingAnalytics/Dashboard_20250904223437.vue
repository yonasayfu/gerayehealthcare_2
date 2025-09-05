<script setup lang="ts">
import { defineProps, ref, onMounted, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
import StatCard from '@/components/StatCard.vue';
import Tooltip from '@/components/Tooltip.vue';
import { Bar, Pie, Chart } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip as ChartTooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, LineElement, PointElement } from 'chart.js';
import axios, { AxiosResponse } from 'axios';
import { ChartData, ChartDataset } from 'chart.js';

ChartJS.register(Title, ChartTooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, LineElement, PointElement);

interface DashboardStats {
  totalLeads: number;
  convertedLeads: number;
  conversionRate: number;
  totalMarketingSpend: number; // Changed to number
  patientsAcquired: number;
  cpa: number;
  revenueGenerated: number;
  roi: number;
}

interface CampaignPerformanceItem {
  date: string;
  impressions: number;
  clicks: number;
  conversions: number;
  revenue_generated: number;
  total_cost: number;
}

interface TrafficSourceItem {
  utm_source: string;
  lead_count: number;
}

interface ConversionFunnelData {
  New: number;
  Contacted: number;
  Qualified: number;
  Converted: number;
}

const props = defineProps<{
  dashboardStats: DashboardStats;
  campaignPerformanceData: CampaignPerformanceItem[];
  trafficSourceData: TrafficSourceItem[];
  conversionFunnelData: ConversionFunnelData;
  loading: boolean; // Added loading prop
}>();

// Tabs
const currentTab = ref<'Overview' | 'Budget' | 'Staff' | 'SLA'>('Overview');

// Range presets (align with main dashboard)
type RangePreset = 'TODAY' | 'MTD' | 'LAST_30' | 'YTD';
const rangePreset = ref<RangePreset>('MTD');
const rangeStart = ref<string>('');
const rangeEnd = ref<string>('');

// This component will emit range changes to its parent (Admin/Dashboard/Index.vue)

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
  fetchAnalyticsExtras(); // This will fetch budget, staff, SLA data
}

// New analytics state (these are fetched internally by this component)
const budgetPacing = ref<{ range: { start: string; end: string }; monthly: any[]; totals: any } | null>(null);
const staffPerformance = ref<Array<any>>([]);
const taskSla = ref<any>(null);
// Staff sorting + paging
type StaffSortKey = 'name' | 'leads' | 'contact' | 'conversion' | 'tasks' | 'ontime' | 'overdue';
const staffSortKey = ref<StaffSortKey>('name');
const staffSortDir = ref<'asc'|'desc'>('asc');
const staffPage = ref(1);
const staffPageSize = ref(8);
const staffTotal = computed(() => staffPerformance.value.length);
const staffSorted = computed(() => {
  const list = [...staffPerformance.value];
  list.sort((a, b) => {
    const av = staffSortKey.value === 'name' ? (a.staff?.name || '')
      : staffSortKey.value === 'leads' ? (a.leads?.total || 0)
      : staffSortKey.value === 'contact' ? (a.leads?.contact_rate || 0)
      : staffSortKey.value === 'conversion' ? (a.leads?.conversion_rate || 0)
      : staffSortKey.value === 'tasks' ? (a.tasks?.tasks_completed || 0)
      : staffSortKey.value === 'ontime' ? (a.tasks?.on_time_rate || 0)
      : (a.tasks?.tasks_overdue_open || 0);
    const bv = staffSortKey.value === 'name' ? (b.staff?.name || '')
      : staffSortKey.value === 'leads' ? (b.leads?.total || 0)
      : staffSortKey.value === 'contact' ? (b.leads?.contact_rate || 0)
      : staffSortKey.value === 'conversion' ? (b.leads?.conversion_rate || 0)
      : staffSortKey.value === 'tasks' ? (b.tasks?.tasks_completed || 0)
      : staffSortKey.value === 'ontime' ? (b.tasks?.on_time_rate || 0)
      : (b.tasks?.tasks_overdue_open || 0);
    const cmp = typeof av === 'string' ? String(av).localeCompare(String(bv)) : (av as number) - (bv as number);
    return staffSortDir.value === 'asc' ? cmp : -cmp;
  });
  return list;
});
const staffPaged = computed(() => {
  const start = (staffPage.value - 1) * staffPageSize.value;
  return staffSorted.value.slice(start, start + staffPageSize.value);
});
function setStaffSort(key: StaffSortKey) {
  if (staffSortKey.value === key) {
    staffSortDir.value = staffSortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    staffSortKey.value = key;
    staffSortDir.value = 'asc';
  }
  staffPage.value = 1;
}

// Counter animations (adapted from provided HTML)
function animateValue(id: string, start: number, end: number, duration: number, prefix = '') {
  const obj = document.getElementById(id);
  if (!obj) return;

  let startTimestamp: number | null = null;
  const step = (timestamp: number) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    const value = Math.floor(progress * (end - start) + start);
    obj.innerHTML = prefix + value.toLocaleString() + (id.includes('percent') ? '%' : '');
    if (progress < 1) {
      window.requestAnimationFrame(step);
    }
  };
  window.requestAnimationFrame(step);
}

// Animate progress bars (keeping this function, but the progress bars themselves are removed for Traffic Sources)
function animateProgressBars() {
  document.querySelectorAll<HTMLElement>('.progress-bar').forEach(bar => {
    const targetWidth = bar.getAttribute('data-width');
    if (targetWidth) {
      bar.style.width = targetWidth;
    }
  });
}

onMounted(() => {
  // Animate counters
  animateValue('total-leads-counter', 0, props.dashboardStats.totalLeads, 2000);
  animateValue('converted-leads-counter', 0, props.dashboardStats.convertedLeads, 1500);
  animateValue('conversion-rate-counter', 0, props.dashboardStats.conversionRate, 1000);
  animateValue('total-marketing-spend-counter', 0, props.dashboardStats.totalMarketingSpend, 2000, '$');
  animateValue('patients-acquired-counter', 0, props.dashboardStats.patientsAcquired, 1500);
  animateValue('cpa-counter', 0, props.dashboardStats.cpa, 1000, '$');
  animateValue('revenue-generated-counter', 0, props.dashboardStats.revenueGenerated, 2000, '$');
  animateValue('roi-counter', 0, props.dashboardStats.roi, 1500);

  // Animate progress bars (only if any are left)
  setTimeout(animateProgressBars, 500);

  // Add hover effect to cards
  document.querySelectorAll('.card-hover').forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.classList.add('shadow-lg');
    });
    card.addEventListener('mouseleave', () => {
      card.classList.remove('shadow-lg');
    });
  });

  // Initialize range and fetch
  applyRange('MTD');
});

watch(() => props.dashboardStats, (newStats: DashboardStats) => {
  if (newStats) {
    animateValue('total-leads-counter', 0, newStats.totalLeads, 2000);
    animateValue('converted-leads-counter', 0, newStats.convertedLeads, 1500);
    animateValue('conversion-rate-counter', 0, newStats.conversionRate, 1000);
    animateValue('total-marketing-spend-counter', 0, newStats.totalMarketingSpend, 2000, '$');
    animateValue('patients-acquired-counter', 0, newStats.patientsAcquired, 1500);
    animateValue('cpa-counter', 0, newStats.cpa, 1000, '$');
    animateValue('revenue-generated-counter', 0, newStats.revenueGenerated, 2000, '$');
    animateValue('roi-counter', 0, newStats.roi, 1500);
  }
}, { deep: true });

watch(currentTab, () => {
  fetchAnalyticsExtras();
});

function fetchAnalyticsExtras() {
  const params = { start_date: rangeStart.value, end_date: rangeEnd.value } as any;

  // Fetch budget pacing
  axios.get(route('admin.marketing-analytics.budget-pacing'), { params })
    .then((res: AxiosResponse<any>) => budgetPacing.value = res.data)
    .catch(() => budgetPacing.value = null);

  // Fetch staff performance
  axios.get(route('admin.marketing-analytics.staffPerformance'), { params })
    .then((res: AxiosResponse<any[]>) => staffPerformance.value = res.data || [])
    .catch(() => staffPerformance.value = []);

  // Fetch task SLA
  axios.get(route('admin.marketing-analytics.taskSla'), { params })
    .then((res: AxiosResponse<any>) => taskSla.value = res.data)
    .catch(() => taskSla.value = { total: 0, completed: 0, on_time: 0, overdue_open: 0, overdue_completed: 0, on_time_rate: 0 });
}

// Chart data (using computed properties for reactivity)
const campaignBarChartData = computed(() => ({
  labels: props.campaignPerformanceData.map((item: CampaignPerformanceItem) => item.date),
  datasets: [
    {
      label: 'Impressions',
      backgroundColor: '#6366F1', // Indigo-500 for a subtle but distinct blue
      data: props.campaignPerformanceData.map((item: CampaignPerformanceItem) => item.impressions),
    },
    {
      label: 'Clicks',
      backgroundColor: '#22C55E', // Green-500 for a fresh green
      data: props.campaignPerformanceData.map((item: CampaignPerformanceItem) => item.clicks),
    },
  ],
}));

const trafficPieChartData = computed(() => ({
  labels: props.trafficSourceData.map((item: TrafficSourceItem) => item.utm_source),
  datasets: [
    {
      // Using a palette that leans into the admin dashboard's vibrant yet balanced tones
      backgroundColor: ['#6366F1', '#3B82F6', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6'],
      data: props.trafficSourceData.map((item: TrafficSourceItem) => item.lead_count),
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

const getConversionFunnelPercentage = (step: keyof ConversionFunnelData) => {
  const total: number = Object.values(props.conversionFunnelData).reduce((sum: number, value: number) => sum + value, 0);
  return total > 0 ? (props.conversionFunnelData[step] / total) * 100 : 0;
};

// Budget pacing chart (stacked bars + projected line)
const budgetPacingChartData = computed<ChartData<'bar' | 'line'>>(() => {
  const months = budgetPacing.value?.monthly?.map((m: any) => m.month) || [];
  const allocated = budgetPacing.value?.monthly?.map((m: any) => m.allocated) || [];
  const spent = budgetPacing.value?.monthly?.map((m: any) => m.spent) || [];
  const projected = budgetPacing.value?.monthly?.map((m: any) => m.projected_spend) || [];
  return {
    labels: months,
    datasets: [
      { type: 'bar', label: 'Allocated', backgroundColor: '#e5e7eb', data: allocated, stack: 'budget' } as ChartDataset<'bar'>,
      { type: 'bar', label: 'Spent', backgroundColor: '#60a5fa', data: spent, stack: 'budget' } as ChartDataset<'bar'>,
      { type: 'line', label: 'Projected Spend', borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.2)', data: projected, yAxisID: 'y' } as ChartDataset<'line'>,
    ],
  };
});
const budgetPacingChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { position: 'top' as const } },
  scales: {
    x: { stacked: true },
    y: { stacked: true, beginAtZero: true, position: 'left' as const, id: 'y-bar' }, // For bars
    y1: { beginAtZero: true, position: 'right' as const, id: 'y-line', grid: { drawOnChartArea: false } }, // For line
  },
};
</script>

<template>
  <AppLayout title="Marketing Analytics Dashboard">
    <Head title="Marketing Analytics Dashboard" />

    <div class="container mx-auto p-4 md:p-6">
      <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4 animate-fadeIn" style="animation-delay: 0.1s">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Marketing Analytics</h1>
        <div class="flex items-center gap-2">
          <button type="button" class="px-3 py-1 rounded border" :class="rangePreset==='TODAY' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="applyRange('TODAY')">Today</button>
          <button type="button" class="px-3 py-1 rounded border" :class="rangePreset==='MTD' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="applyRange('MTD')">MTD</button>
          <button type="button" class="px-3 py-1 rounded border" :class="rangePreset==='LAST_30' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="applyRange('LAST_30')">Last 30</button>
          <button type="button" class="px-3 py-1 rounded border" :class="rangePreset==='YTD' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="applyRange('YTD')">YTD</button>
          <span class="ml-2 text-sm text-gray-500">Range: {{ rangeStart }} → {{ rangeEnd }}</span>
        </div>
      </header>

      <!-- Tabs -->
      <div class="flex flex-wrap gap-2 mb-4">
        <button type="button" class="px-3 py-1 rounded border" :class="currentTab==='Overview' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="currentTab='Overview'">Overview</button>
        <button type="button" class="px-3 py-1 rounded border" :class="currentTab==='Budget' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="currentTab='Budget'">Budget</button>
        <button type="button" class="px-3 py-1 rounded border" :class="currentTab==='Staff' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="currentTab='Staff'">Staff</button>
        <button type="button" class="px-3 py-1 rounded border" :class="currentTab==='SLA' ? 'bg-blue-600 text-white' : 'bg-white'" @click.stop="currentTab='SLA'">SLA</button>
      </div>

      <div v-if="currentTab==='Overview'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        <Tooltip text="Total marketing leads in the selected range">
          <StatCard title="Total Leads" :value="props.dashboardStats.totalLeads.toLocaleString()" change="" :icon="Users" color="bg-blue-100" />
        </Tooltip>
        <Tooltip text="Leads turned into patients">
          <StatCard title="Converted Leads" :value="props.dashboardStats.convertedLeads.toLocaleString()" change="" :icon="Activity" color="bg-green-100" />
        </Tooltip>
        <Tooltip text="Lead to patient conversion rate">
          <StatCard title="Conversion Rate" :value="`${props.dashboardStats.conversionRate.toFixed?.(2) ?? props.dashboardStats.conversionRate}%`" change="" :icon="CreditCard" color="bg-yellow-100" />
        </Tooltip>
        <Tooltip text="Total marketing spend">
          <StatCard title="Total Spend" :value="`$${Number(props.dashboardStats.totalMarketingSpend || 0).toLocaleString()}`" change="" :icon="CreditCard" color="bg-orange-100" />
        </Tooltip>
        <Tooltip text="Patients acquired via marketing">
          <StatCard title="Patients Acquired" :value="props.dashboardStats.patientsAcquired.toLocaleString()" change="" :icon="Users" color="bg-indigo-100" />
        </Tooltip>
        <Tooltip text="Cost per acquisition">
          <StatCard title="CPA" :value="`$${Number(props.dashboardStats.cpa || 0).toLocaleString()}`" change="" :icon="DollarSign" color="bg-purple-100" />
        </Tooltip>
        <Tooltip text="Revenue attributed to marketing">
          <StatCard title="Revenue Generated" :value="`$${Number(props.dashboardStats.revenueGenerated || 0).toLocaleString()}`" change="" :icon="CreditCard" color="bg-teal-100" />
        </Tooltip>
        <Tooltip text="Return on investment">
          <StatCard title="ROI" :value="`${props.dashboardStats.roi.toFixed?.(2) ?? props.dashboardStats.roi}%`" change="" :icon="Activity" color="bg-pink-100" />
        </Tooltip>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.0s">
          <h3 class="text-lg font-medium text-gray-700 mb-4">Social Media Performance</h3>
          <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-black/5 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                <div class="flex items-center space-x-3">
                    <div class="bg-black text-white p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                        <i class="fab fa-tiktok text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-700">TikTok</span>
                </div>
                <div class="text-right">
                    <p class="text-green-500 font-medium">+15/20 50.2K</p>
                    <p class="text-xs text-gray-500">Views: 75.8K</p>
                </div>
            </div>

            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
              <div class="flex items-center space-x-3">
                <div class="bg-red-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                  <i class="fab fa-youtube text-red-600 text-xl"></i>
                </div>
                <span class="font-medium text-gray-700">YouTube</span>
              </div>
              <div class="text-right">
                <p class="text-green-500 font-medium">+1/8 REVX</p>
                <p class="text-xs text-gray-500">Views: 12.4K</p>
              </div>
            </div>

            <div class="flex justify-between items-center p-3 bg-pink-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                <div class="flex items-center space-x-3">
                    <div class="bg-pink-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                        <i class="fab fa-instagram text-pink-600 text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-700">Instagram</span>
                </div>
                <div class="text-right">
                    <p class="text-green-500 font-medium">+7/10 30.1K</p>
                    <p class="text-xs text-gray-500">Reach: 45.3K</p>
                </div>
            </div>

            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
              <div class="flex items-center space-x-3">
                <div class="bg-blue-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                  <i class="fab fa-facebook-f text-blue-600 text-xl"></i>
                </div>
                <span class="font-medium text-gray-700">Facebook</span>
              </div>
              <div class="text-right">
                <p class="text-green-500 font-medium">+4/5 26.9K</p>
                <p class="text-xs text-gray-500">Engagement: 8.2%</p>
              </div>
            </div>

            <div class="flex justify-between items-center p-3 bg-sky-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
              <div class="flex items-center space-x-3">
                <div class="bg-sky-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                  <i class="fab fa-twitter text-sky-500 text-xl"></i>
                </div>
                <span class="font-medium text-gray-700">Twitter</span>
              </div>
              <div class="text-right">
                <p class="text-red-500 font-medium">-6/10 6.9K</p>
                <p class="text-xs text-gray-500">Impressions: 24.1K</p>
              </div>
            </div>

            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                <div class="flex items-center space-x-3">
                    <div class="bg-gray-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                        <i class="fas fa-ellipsis-h text-gray-600 text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-700">Others</span>
                </div>
                <div class="text-right">
                    <p class="text-gray-500 font-medium">Various</p>
                    <p class="text-xs text-gray-500">Total Activity: 5.5K</p>
                </div>
            </div>
          </div>
        </div>

        <div v-if="currentTab==='Overview'" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.1s">
          <h3 class="text-lg font-medium text-gray-700 mb-4">Traffic Sources</h3>
          <div class="relative h-[300px] flex items-center justify-center">
            <Pie :data="trafficPieChartData" :options="chartOptions" />
            <div v-if="!(props.trafficSourceData && props.trafficSourceData.length)" class="absolute inset-0 flex items-center justify-center text-gray-500">No data for selected range</div>
          </div>
        </div>

        <div v-if="currentTab==='Overview'" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.2s">
          <h3 class="text-lg font-medium text-gray-700 mb-4">Conversion Funnel</h3>
          <div class="space-y-4">
            <div v-for="(value, key) in props.conversionFunnelData" :key="key" class="flex justify-between items-center p-3 rounded-lg"
                 :class="{
                   'bg-blue-50': key === 'New',
                   'bg-indigo-50': key === 'Contacted',
                   'bg-green-50': key === 'Qualified',
                   'bg-purple-50': key === 'Converted',
                 }">
              <span class="font-medium text-gray-700">{{ key }} Leads</span>
              <div class="text-right">
                <p class="font-medium text-gray-800">{{ value }}</p>
                <p class="text-xs text-gray-500">{{ getConversionFunnelPercentage(key).toFixed(1) }}% of total</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="currentTab==='SLA'" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.3s">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Campaign Performance
          <a :href="`/dashboard/marketing-analytics/campaign-performance/print-all`" target="_blank" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Print All
          </a>
          <a :href="`/dashboard/marketing-analytics/campaign-performance/print-current`" target="_blank" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus::ring-offset-2 focus:ring-indigo-500">
            Print Current
          </a>
        </h3>
        <div class="relative h-[350px] mt-4">
          <Bar :data="campaignBarChartData" :options="chartOptions" />
          <div v-if="!(props.campaignPerformanceData && props.campaignPerformanceData.length)" class="absolute inset-0 flex items-center justify-center text-gray-500">No data for selected range</div>
        </div>
      </div>

      <!-- Budget vs Actuals + Projected -->
      <div v-if="currentTab==='Budget'" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.35s">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-700">Budget vs Actuals + Projected</h3>
          <div v-if="budgetPacing?.range" class="text-sm text-gray-500">{{ budgetPacing.range.start }} → {{ budgetPacing.range.end }}</div>
        </div>
        <div class="h-[360px]">
          <Chart v-if="budgetPacing" :data="budgetPacingChartData" :options="budgetPacingChartOptions" :type="'bar'" />
          <div v-else class="text-gray-400 text-sm">Loading budget pacing...</div>
        </div>
        <div v-if="budgetPacing" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">Allocated (Total)</div>
            <div class="font-semibold">${{ Number(budgetPacing.totals.allocated || 0).toLocaleString() }}</div>
          </div>
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">Projected Spend (Total)</div>
            <div class="font-semibold">${{ Number(budgetPacing.totals.projected_spend || 0).toLocaleString() }}</div>
          </div>
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">Pacing</div>
            <div class="font-semibold" :class="budgetPacing.totals.overrun ? 'text-red-600' : 'text-green-600'">{{ (budgetPacing.totals.pacing * 100).toFixed(0) }}%</div>
          </div>
        </div>
      </div>

      <!-- Staff Performance Table -->
      <div v-if="currentTab==='Staff'" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.4s">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Staff Performance</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-600">
                <th class="py-2 pr-4">Staff</th>
                <th class="py-2 pr-4">Leads</th>
                <th class="py-2 pr-4">Contact Rate</th>
                <th class="py-2 pr-4">Conversion Rate</th>
                <th class="py-2 pr-4">Tasks Completed</th>
                <th class="py-2 pr-4">On-time Rate</th>
                <th class="py-2 pr-4">Overdue (Open)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in staffPerformance" :key="row.staff?.id ?? `unassigned-${Math.random()}`" class="border-t">
                <td class="py-2 pr-4">{{ row.staff?.name || 'Unassigned' }}</td>
                <td class="py-2 pr-4">{{ row.leads.total }}</td>
                <td class="py-2 pr-4">{{ (row.leads.contact_rate * 100).toFixed(0) }}%</td>
                <td class="py-2 pr-4">{{ (row.leads.conversion_rate * 100).toFixed(0) }}%</td>
                <td class="py-2 pr-4">{{ row.tasks.tasks_completed }}</td>
                <td class="py-2 pr-4" :class="row.tasks.on_time_rate >= 0.8 ? 'text-green-600' : 'text-yellow-600'">{{ (row.tasks.on_time_rate * 100).toFixed(0) }}%</td>
                <td class="py-2 pr-4" :class="row.tasks.tasks_overdue_open > 0 ? 'text-red-600' : ''">{{ row.tasks.tasks_overdue_open }}</td>
              </tr>
              <tr v-if="!staffPerformance.length">
                <td colspan="7" class="py-4 text-center text-gray-400">No data</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- SLA Widget -->
      <div v-if="currentTab==='SLA'" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.45s">
        <h3 class="text-lg font-medium text-gray-700 mb-4">SLA Summary</h3>
        <div v-if="taskSla && taskSla.total > 0" class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm">
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">Total Tasks</div>
            <div class="font-semibold">{{ taskSla.total }}</div>
          </div>
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">Completed</div>
            <div class="font-semibold">{{ taskSla.completed }}</div>
          </div>
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">On-time</div>
            <div class="font-semibold">{{ taskSla.on_time }}</div>
          </div>
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">On-time Rate</div>
            <div class="font-semibold" :class="taskSla.on_time_rate >= 0.8 ? 'text-green-600' : 'text-yellow-600'">{{ (taskSla.on_time_rate * 100).toFixed(0) }}%</div>
          </div>
          <div class="p-3 bg-gray-50 rounded">
            <div class="text-gray-500">Overdue (Open)</div>
            <div class="font-semibold" :class="taskSla.overdue_open > 0 ? 'text-red-600' : ''">{{ taskSla.overdue_open }}</div>
          </div>
        </div>
        <div v-else class="py-4 text-center text-gray-400">No data</div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&display=swap');

body {
  font-family: 'IBM Plex Mono', sans-serif;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.6s ease-out forwards;
}
.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}
.pulse {
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
  100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
}
</style>
