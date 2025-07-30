<script setup lang="ts">
import { defineProps, ref, onMounted, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
import { Bar, Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
import axios from 'axios'; // Import axios

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

interface DashboardStats {
  totalLeads: number;
  convertedLeads: number;
  conversionRate: number;
  totalMarketingSpend: string; // Or number if it's always numeric
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
}>();

// Counter animations
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

// Animate progress bars
function animateProgressBars() {
  document.querySelectorAll<HTMLElement>('.progress-bar').forEach(bar => {
    const targetWidth = bar.getAttribute('data-width');
    if (targetWidth) {
      bar.style.width = targetWidth;

      // Animate the percentage counters
      const percent = parseInt(targetWidth);
      const counterElement = bar.parentElement?.parentElement?.nextElementSibling as HTMLElement;
      if (counterElement) {
        animateValue(counterElement.id, 0, percent, 1500);
      }
    }
  });
}

const localDashboardStats = ref<DashboardStats>({
  totalLeads: 0,
  convertedLeads: 0,
  conversionRate: 0,
  totalMarketingSpend: '0',
  patientsAcquired: 0,
  cpa: 0,
  revenueGenerated: 0,
  roi: 0,
});

const localCampaignPerformanceData = ref<CampaignPerformanceItem[]>([]);
const localTrafficSourceData = ref<TrafficSourceItem[]>([]);
const localConversionFunnelData = ref<ConversionFunnelData>({
  New: 0,
  Contacted: 0,
  Qualified: 0,
  Converted: 0,
});

// Data fetching logic (from the original template, adapted for local refs)
const fetchDashboardData = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.dashboardData'));
    localDashboardStats.value = response.data;
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

const fetchCampaignPerformance = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.campaignPerformance'));
    localCampaignPerformanceData.value = response.data.data; // Assuming paginated data
  } catch (error) {
    console.error('Error fetching campaign performance:', error);
  }
};

const fetchTrafficSourceDistribution = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.trafficSourceDistribution'));
    localTrafficSourceData.value = response.data;
  } catch (error) {
    console.error('Error fetching traffic source distribution:', error);
  }
};

const fetchConversionFunnel = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.conversionFunnel'));
    localConversionFunnelData.value = response.data;
  } catch (error) {
    console.error('Error fetching conversion funnel:', error);
  }
};

onMounted(async () => {
  // Fetch data on component mount
  await Promise.all([
    fetchDashboardData(),
    fetchCampaignPerformance(),
    fetchTrafficSourceDistribution(),
    fetchConversionFunnel(),
  ]);

  // Animate counters
  animateValue('total-leads-counter', 0, localDashboardStats.value.totalLeads, 2000);
  animateValue('converted-leads-counter', 0, localDashboardStats.value.convertedLeads, 1500);
  animateValue('conversion-rate-counter', 0, localDashboardStats.value.conversionRate, 1000);
  animateValue('total-marketing-spend-counter', 0, parseFloat(localDashboardStats.value.totalMarketingSpend), 2000, '$');
  animateValue('patients-acquired-counter', 0, localDashboardStats.value.patientsAcquired, 1500);
  animateValue('cpa-counter', 0, localDashboardStats.value.cpa, 1000, '$');
  animateValue('revenue-generated-counter', 0, localDashboardStats.value.revenueGenerated, 2000, '$');
  animateValue('roi-counter', 0, localDashboardStats.value.roi, 1500);

  // Animate progress bars after a slight delay
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
});

// Chart data (using computed properties for reactivity)
const campaignBarChartData = computed(() => ({
  labels: localCampaignPerformanceData.value.map((item: CampaignPerformanceItem) => item.date),
  datasets: [
    {
      label: 'Impressions',
      backgroundColor: '#3b82f6', // Indigo-600 inspired
      data: localCampaignPerformanceData.value.map((item: CampaignPerformanceItem) => item.impressions),
    },
    {
      label: 'Clicks',
      backgroundColor: '#66BB6A', // Green-600 inspired
      data: localCampaignPerformanceData.value.map((item: CampaignPerformanceItem) => item.clicks),
    },
  ],
}));

const trafficPieChartData = computed(() => ({
  labels: localTrafficSourceData.value.map((item: TrafficSourceItem) => item.utm_source),
  datasets: [
    {
      backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#EF5350', '#9C27B0', '#FFC107'], // Admin dashboard colors
      data: localTrafficSourceData.value.map((item: TrafficSourceItem) => item.lead_count),
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

const getConversionFunnelPercentage = (step: keyof ConversionFunnelData) => {
  const total = Object.values(localConversionFunnelData.value).reduce((sum: number, value: number) => sum + value, 0);
  return total > 0 ? (localConversionFunnelData.value[step] / total) * 100 : 0;
};
</script>

<template>
  <AppLayout title="Marketing Analytics Dashboard">
    <Head title="Marketing Analytics Dashboard" />

    <div class="container mx-auto p-4 md:p-6">
      <header class="flex justify-between items-center mb-8 animate-fadeIn" style="animation-delay: 0.1s">
        <h1 class="text-3xl font-bold text-indigo-700 transform transition duration-500 hover:scale-105">Marketing Analytics</h1>
        <div class="flex items-center space-x-4">
          <button class="p-2 rounded-full bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-colors duration-300 relative">
            <i class="fas fa-bell"></i>
            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500 pulse"></span>
          </button>
          <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold hover:bg-indigo-600 transition-colors duration-300 cursor-pointer">
            MA
          </div>
        </div>
      </header>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.2s">
          <h2 class="text-lg font-medium mb-1">Total Leads</h2>
          <p class="text-3xl font-bold mb-4" id="total-leads-counter">0</p>
          <div class="flex justify-between items-center">
            <span class="text-indigo-100">Overall leads generated</span>
            <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-medium transform transition duration-300 hover:scale-110">
              <Activity class="inline-block h-4 w-4 mr-1" />
              <span id="total-leads-indicator"></span>
            </span>
          </div>
        </div>

        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.3s">
          <h2 class="text-lg font-medium mb-1">Converted Leads</h2>
          <p class="text-3xl font-bold mb-4" id="converted-leads-counter">0</p>
          <div class="flex justify-between items-center">
            <span class="text-green-100">Leads turned into patients</span>
            <span class="bg-white text-green-600 px-3 py-1 rounded-full text-sm font-medium transform transition duration-300 hover:scale-110">
              <Users class="inline-block h-4 w-4 mr-1" />
              <span id="converted-leads-indicator"></span>
            </span>
          </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-600 to-orange-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.4s">
          <h2 class="text-lg font-medium mb-1">Conversion Rate</h2>
          <p class="text-3xl font-bold mb-4" id="conversion-rate-counter">0%</p>
          <div class="flex justify-between items-center">
            <span class="text-yellow-100">Success rate of leads</span>
            <span class="bg-white text-yellow-600 px-3 py-1 rounded-full text-sm font-medium transform transition duration-300 hover:scale-110">
              <CreditCard class="inline-block h-4 w-4 mr-1" />
              <span id="conversion-rate-indicator"></span>
            </span>
          </div>
        </div>

        <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.5s">
          <h2 class="text-lg font-medium mb-1">Total Marketing Spend</h2>
          <p class="text-3xl font-bold mb-4" id="total-marketing-spend-counter">$0</p>
          <div class="flex justify-between items-center">
            <span class="text-red-100">Total cost of campaigns</span>
            <span class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-medium transform transition duration-300 hover:scale-110">
              <DollarSign class="inline-block h-4 w-4 mr-1" />
              <span id="total-marketing-spend-indicator"></span>
            </span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.0s">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Campaign Performance
          <a :href="`/dashboard/marketing-analytics/campaign-performance/print-all`" target="_blank" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Print All
          </a>
          <a :href="`/dashboard/marketing-analytics/campaign-performance/print-current`" target="_blank" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus::ring-offset-2 focus:ring-indigo-500">
            Print Current
          </a>
        </h3>
        <div class="h-[350px] mt-4">
          <Bar :data="campaignBarChartData" :options="chartOptions" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.1s">
          <h3 class="text-lg font-medium text-gray-700 mb-4">Traffic Sources</h3>
          <div class="h-[300px] flex items-center justify-center">
            <Pie :data="trafficPieChartData" :options="chartOptions" />
          </div>
          <div class="space-y-3 mt-4">
            <div v-for="(data, index) in localTrafficSourceData" :key="index" class="flex items-center justify-between">
              <span class="w-24 font-medium">{{ data.utm_source }}</span>
              <div class="flex-1 mx-4">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="h-2.5 rounded-full progress-bar"
                    :style="{ width: (data.lead_count / localTrafficSourceData.reduce((sum, item) => sum + item.lead_count, 0) * 100) + '%' }"
                    :data-width="(data.lead_count / localTrafficSourceData.reduce((sum, item) => sum + item.lead_count, 0) * 100) + '%'"
                    :class="{
                      'bg-indigo-600': index % 6 === 0,
                      'bg-blue-500': index % 6 === 1,
                      'bg-green-500': index % 6 === 2,
                      'bg-yellow-500': index % 6 === 3,
                      'bg-purple-500': index % 6 === 4,
                      'bg-orange-500': index % 6 === 5,
                    }"
                  ></div>
                </div>
              </div>
              <span class="w-10 text-right font-medium" :id="`traffic-source-percent-${index}`">{{ (data.lead_count / localTrafficSourceData.reduce((sum, item) => sum + item.lead_count, 0) * 100).toFixed(1) }}%</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.2s">
          <h3 class="text-lg font-medium text-gray-700 mb-4">Conversion Funnel</h3>
          <div class="space-y-4">
            <div v-for="(value, key) in localConversionFunnelData" :key="key" class="flex justify-between items-center p-3 rounded-lg"
                 :class="{
                   'bg-blue-50': key === 'New',
                   'bg-indigo-50': key === 'Contacted',
                   'bg-green-50': key === 'Qualified',
                   'bg-purple-50': key === 'Converted',
                 }">
              <span class="font-medium">{{ key }} Leads</span>
              <div class="text-right">
                <p class="font-medium">{{ value }}</p>
                <p class="text-xs text-gray-500">{{ getConversionFunnelPercentage(key).toFixed(1) }}% of total</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
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
.progress-bar {
  transition: width 1.5s ease-in-out;
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