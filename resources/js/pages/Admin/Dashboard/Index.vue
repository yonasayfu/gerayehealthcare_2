<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import axios from 'axios';
import MarketingAnalyticsDashboard from '@/components/MarketingAnalyticsDashboard.vue';

const currentTab = ref('Analytics');

function handleTabChange(tab: string) {
  currentTab.value = tab;
}

/* Theme-aware colors for charts and dynamic updates */
const themeColors = ref({
  text: '#374151',       // light default
  grid: 'rgba(0,0,0,0.06)', // light default
});

const updateThemeColors = () => {
  const isDark = document.documentElement.classList.contains('dark');
  if (isDark) {
    themeColors.value.text = '#E5E7EB'; // gray-200
    themeColors.value.grid = 'rgba(255,255,255,0.06)';
  } else {
    themeColors.value.text = '#374151'; // gray-700
    themeColors.value.grid = 'rgba(0,0,0,0.06)';
  }
};

let htmlObserver: MutationObserver | null = null;
onMounted(() => {
  updateThemeColors();
  htmlObserver = new MutationObserver(() => updateThemeColors());
  htmlObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
onUnmounted(() => {
  if (htmlObserver) htmlObserver.disconnect();
});

/* Data / computed chart payloads */
const campaignPerformanceData = ref([]);
const trafficSourceData = ref([]);
const conversionFunnelData = ref({});

const barChartData = computed(() => ({
  labels: campaignPerformanceData.value.map((item: any) => item.date),
  datasets: [
    {
      label: 'Impressions',
      backgroundColor: '#3b82f6',
      data: campaignPerformanceData.value.map((item: any) => item.impressions),
    },
    {
      label: 'Clicks',
      backgroundColor: '#16a34a',
      data: campaignPerformanceData.value.map((item: any) => item.clicks),
    },
  ],
}));

const barChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      labels: { color: themeColors.value.text },
    },
    title: {
      display: false,
      color: themeColors.value.text,
    },
    tooltip: {
      titleColor: themeColors.value.text,
      bodyColor: themeColors.value.text,
    },
  },
  scales: {
    x: {
      ticks: { color: themeColors.value.text },
      grid: { color: themeColors.value.grid },
    },
    y: {
      ticks: { color: themeColors.value.text },
      grid: { color: themeColors.value.grid },
    },
  },
}));

const pieChartData = computed(() => ({
  labels: trafficSourceData.value.map((item: any) => item.utm_source),
  datasets: [
    {
      backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#EF5350', '#9C27B0', '#FFC107'],
      data: trafficSourceData.value.map((item: any) => item.lead_count),
    },
  ],
}));

const pieChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      labels: { color: themeColors.value.text },
    },
    tooltip: {
      titleColor: themeColors.value.text,
      bodyColor: themeColors.value.text,
    },
  },
}));

/* Static / placeholder content */
const recentAppointments = [
  { patient: 'John Doe', date: '2025-07-15', time: '10:00 AM', status: 'Completed' },
  { patient: 'Jane Smith', date: '2025-07-16', time: '02:30 PM', status: 'Pending' },
  { patient: 'Peter Jones', date: '2025-07-17', time: '09:00 AM', status: 'Completed' },
  { patient: 'Alice Brown', date: '2025-07-17', time: '04:00 PM', status: 'Cancelled' },
];

const props = defineProps({
  stats: {
    type: Object,
    required: false,
  },
});

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

/* API fetchers */
const fetchDashboardData = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.dashboard-data'));
    dashboardStats.value = response.data;
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

const fetchCampaignPerformance = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.campaign-performance'));
    campaignPerformanceData.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching campaign performance:', error);
  }
};

const fetchTrafficSourceDistribution = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.traffic-source-distribution'));
    trafficSourceData.value = response.data || [];
  } catch (error) {
    console.error('Error fetching traffic source distribution:', error);
  }
};

const fetchConversionFunnel = async () => {
  try {
    const response = await axios.get(route('admin.marketing-analytics.conversion-funnel'));
    conversionFunnelData.value = response.data || {};
  } catch (error) {
    console.error('Error fetching conversion funnel:', error);
  }
};

watch(currentTab, (newTab) => {
  if (newTab === 'Analytics') {
    fetchDashboardData();
    fetchCampaignPerformance();
    fetchTrafficSourceDistribution();
    fetchConversionFunnel();
  }
}, { immediate: true });
</script>

<template>
  <AppLayout>
    <Head title="Dashboard" />

    <div class="p-6 space-y-6">

      <div>
        <MarketingAnalyticsDashboard
          :dashboardStats="dashboardStats"
          :campaignPerformanceData="campaignPerformanceData"
          :trafficSourceData="trafficSourceData"
          :conversionFunnelData="conversionFunnelData"
        />
      </div>

      <!-- Only marketing analytics content remains -->

    </div>
  </AppLayout>
</template>
