<script setup lang="ts">
import { defineProps, ref, onMounted, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue'; // Adjusted path based on admin dashboard
import DashboardHeader from '@/components/DashboardHeader.vue'; // Assuming this component exists
import DashboardTabs from '@/components/DashboardTabs.vue'; // Assuming this component exists
import StatCard from '@/components/StatCard.vue'; // Assuming this component exists
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
import { Bar, Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
import axios from 'axios'; // Import axios for API calls

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

interface DashboardStats {
  totalLeads: number;
  convertedLeads: number;
  conversionRate: number;
  totalMarketingSpend: number; // Changed to number for consistency with calculations
  patientsAcquired: number;
  cpa: number;
  revenueGenerated: number;
  roi: number;
  allEarnings: number; // From second template
  salesPerDay: number; // From second template
  revenueToday: number; // From second template
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
  initialDashboardStats?: DashboardStats;
  initialCampaignPerformanceData?: CampaignPerformanceItem[];
  initialTrafficSourceData?: TrafficSourceItem[];
  initialConversionFunnelData?: ConversionFunnelData;
}>();

const currentTab = ref('Overview'); // State for active tab

function handleTabChange(tab: string) {
  currentTab.value = tab;
}

// Reactive data for dashboard content
const dashboardStats = ref<DashboardStats>(props.initialDashboardStats || {
  totalLeads: 0,
  convertedLeads: 0,
  conversionRate: 0,
  totalMarketingSpend: 0,
  patientsAcquired: 0,
  cpa: 0,
  revenueGenerated: 0,
  roi: 0,
  allEarnings: 0,
  salesPerDay: 0,
  revenueToday: 0,
});

const campaignPerformanceData = ref<CampaignPerformanceItem[]>(props.initialCampaignPerformanceData || []);
const trafficSourceData = ref<TrafficSourceItem[]>(props.initialTrafficSourceData || []);
const conversionFunnelData = ref<ConversionFunnelData>(props.initialConversionFunnelData || {
  New: 0,
  Contacted: 0,
  Qualified: 0,
  Converted: 0,
});

// Counter animations
function animateValue(id: string, start: number, end: number, duration: number, prefix = '', suffix = '') {
  const obj = document.getElementById(id);
  if (!obj) return;

  let startTimestamp: number | null = null;
  const step = (timestamp: number) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    const value = Math.floor(progress * (end - start) + start);
    obj.innerHTML = prefix + value.toLocaleString() + suffix;
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

      // Animate the percentage counters for traffic sources
      const percent = parseInt(targetWidth);
      const counterElement = bar.parentElement?.parentElement?.nextElementSibling as HTMLElement;
      if (counterElement) {
        animateValue(counterElement.id, 0, percent, 1500, '', '%');
      }
    }
  });
}

onMounted(() => {
  // Animate dashboard counters from original template
  animateValue('total-leads-counter', 0, dashboardStats.value.totalLeads, 2000);
  animateValue('converted-leads-counter', 0, dashboardStats.value.convertedLeads, 1500);
  animateValue('conversion-rate-counter', 0, dashboardStats.value.conversionRate, 1000, '', '%');
  animateValue('total-marketing-spend-counter', 0, dashboardStats.value.totalMarketingSpend, 2000, '$');
  animateValue('patients-acquired-counter', 0, dashboardStats.value.patientsAcquired, 1500);
  animateValue('cpa-counter', 0, dashboardStats.value.cpa, 1000, '$');
  animateValue('revenue-generated-counter', 0, dashboardStats.value.revenueGenerated, 2000, '$');
  animateValue('roi-counter', 0, dashboardStats.value.roi, 1500, '', '%');

  // Animate earnings and sales from second template
  animateValue('earnings-counter', 0, dashboardStats.value.allEarnings, 2000, '$');
  animateValue('sales-percent', 0, dashboardStats.value.salesPerDay, 1000, '', '%');
  animateValue('revenue-today-counter', 0, dashboardStats.value.revenueToday, 1500, '$');


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
  labels: campaignPerformanceData.value.map((item: CampaignPerformanceItem) => item.date),
  datasets: [
    {
      label: 'Impressions',
      backgroundColor: '#3b82f6',
      data: campaignPerformanceData.value.map((item: CampaignPerformanceItem) => item.impressions),
    },
    {
      label: 'Clicks',
      backgroundColor: '#66BB6A',
      data: campaignPerformanceData.value.map((item: CampaignPerformanceItem) => item.clicks),
    },
  ],
}));

const trafficPieChartData = computed(() => ({
  labels: trafficSourceData.value.map((item: TrafficSourceItem) => item.utm_source),
  datasets: [
    {
      backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#EF5350', '#9C27B0', '#FFC107'],
      data: trafficSourceData.value.map((item: TrafficSourceItem) => item.lead_count),
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

const getConversionFunnelPercentage = (step: keyof ConversionFunnelData) => {
  const total = Object.values(conversionFunnelData.value).reduce((sum: number, value: number) => sum + value, 0);
  return total > 0 ? (conversionFunnelData.value[step] / total) * 100 : 0;
};

// Data fetching functions
const fetchDashboardData = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.dashboardData'));
    dashboardStats.value = { ...dashboardStats.value, ...response.data }; // Merge fetched data
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

const fetchCampaignPerformance = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.campaignPerformance'));
    campaignPerformanceData.value = response.data.data; // Assuming paginated data
  } catch (error) {
    console.error('Error fetching campaign performance:', error);
  }
};

const fetchTrafficSourceDistribution = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.trafficSourceDistribution'));
    trafficSourceData.value = response.data;
  } catch (error) {
    console.error('Error fetching traffic source distribution:', error);
  }
};

const fetchConversionFunnel = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.conversionFunnel'));
    conversionFunnelData.value = response.data;
  } catch (error) {
    console.error('Error fetching conversion funnel:', error);
  }
};

watch(currentTab, (newTab) => {
  if (newTab === 'Overview' || newTab === 'Analytics') { // Fetch for both tabs if needed
    fetchDashboardData();
    fetchCampaignPerformance();
    fetchTrafficSourceDistribution();
    fetchConversionFunnel();
  }
}, { immediate: true }); // Fetch data immediately on component mount
</script>

<template>
  <AppLayout title="Marketing Analytics Dashboard">
    <Head title="Marketing Analytics Dashboard" />

    <div class="flex-1 space-y-4 p-4 md:p-6 pt-6">
      <DashboardHeader title="Marketing Analytics" :show-date-range-picker="true" />
      <DashboardTabs @tab-change="handleTabChange" />

      <div v-if="currentTab === 'Overview' || currentTab === 'Analytics'">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.2s">
            <h2 class="text-lg font-medium mb-1">Total Leads</h2>
            <p class="text-3xl font-bold mb-4" id="total-leads-counter">0</p>
            <StatCard
              title=""
              :value="dashboardStats.totalLeads.toLocaleString()"
              change=""
              :icon="Users"
              color="bg-transparent"
              text-color="text-white"
              icon-color="text-indigo-200"
              class="!p-0 !shadow-none"
            />
          </div>

          <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.3s">
            <h2 class="text-lg font-medium mb-1">Converted Leads</h2>
            <p class="text-3xl font-bold mb-4" id="converted-leads-counter">0</p>
            <StatCard
              title=""
              :value="dashboardStats.convertedLeads.toLocaleString()"
              change=""
              :icon="Activity"
              color="bg-transparent"
              text-color="text-white"
              icon-color="text-green-200"
              class="!p-0 !shadow-none"
            />
          </div>

          <div class="bg-gradient-to-r from-yellow-600 to-orange-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.4s">
            <h2 class="text-lg font-medium mb-1">Conversion Rate</h2>
            <p class="text-3xl font-bold mb-4" id="conversion-rate-counter">0%</p>
            <StatCard
              title=""
              :value="dashboardStats.conversionRate + '%'"
              change=""
              :icon="CreditCard"
              color="bg-transparent"
              text-color="text-white"
              icon-color="text-yellow-200"
              class="!p-0 !shadow-none"
            />
          </div>

          <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.5s">
            <h2 class="text-lg font-medium mb-1">Total Marketing Spend</h2>
            <p class="text-3xl font-bold mb-4" id="total-marketing-spend-counter">$0</p>
            <StatCard
              title=""
              :value="'$' + dashboardStats.totalMarketingSpend.toLocaleString()"
              change=""
              :icon="DollarSign"
              color="bg-transparent"
              text-color="text-white"
              icon-color="text-red-200"
              class="!p-0 !shadow-none"
            />
          </div>

          <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.6s">
            <h2 class="text-lg font-medium mb-1">Patients Acquired</h2>
            <p class="text-3xl font-bold mb-4" id="patients-acquired-counter">0</p>
          </div>

          <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.7s">
            <h2 class="text-lg font-medium mb-1">Cost Per Acquisition (CPA)</h2>
            <p class="text-3xl font-bold mb-4" id="cpa-counter">$0</p>
          </div>

          <div class="bg-gradient-to-r from-teal-600 to-green-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.8s">
            <h2 class="text-lg font-medium mb-1">Revenue Generated</h2>
            <p class="text-3xl font-bold mb-4" id="revenue-generated-counter">$0</p>
          </div>

          <div class="bg-gradient-to-r from-pink-600 to-red-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.9s">
            <h2 class="text-lg font-medium mb-1">Return on Investment (ROI)</h2>
            <p class="text-3xl font-bold mb-4" id="roi-counter">0%</p>
          </div>

          <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white shadow-lg transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.0s">
              <h2 class="text-lg font-medium mb-1">All Earnings</h2>
              <p class="text-3xl font-bold mb-4" id="earnings-counter">$0</p>
              <div class="flex justify-between items-center">
                  <span class="text-indigo-100">IDX changes on profit</span>
                  <span class="bg-white text-indigo-600 px-3 py-1 rounded-full text-sm font-medium transform transition duration-300 hover:scale-110">+3.2%</span>
              </div>
          </div>

          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.1s">
              <h3 class="text-lg font-medium text-gray-700 mb-4">Sales Per Day</h3>
              <div class="flex justify-between items-end">
                  <div>
                      <p class="text-2xl font-bold text-gray-800" id="sales-percent">0%</p>
                      <p class="text-green-500 text-sm font-medium">+1.2% from yesterday</p>
                  </div>
                  <div class="bg-green-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
                      <i class="fas fa-chart-line text-green-600 text-xl"></i>
                  </div>
              </div>
          </div>

          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.2s">
              <h3 class="text-lg font-medium text-gray-700 mb-4">Revenue Today</h3>
              <div class="flex justify-between items-end">
                  <div>
                      <p class="text-2xl font-bold text-gray-800" id="revenue-today-counter">$0</p>
                      <p class="text-gray-500 text-sm">321 Today Sales</p>
                  </div>
                  <div class="bg-blue-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
                      <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                  </div>
              </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.3s">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 1.4s">
              <h3 class="text-lg font-medium text-gray-700 mb-4">Social Media Performance</h3>
              <div class="space-y-4">
                  <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                      <div class="flex items-center space-x-3">
                          <div class="bg-red-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                              <i class="fab fa-youtube text-red-600"></i>
                          </div>
                          <span class="font-medium">YouTube</span>
                      </div>
                      <div class="text-right">
                          <p class="text-green-500 font-medium">+1/8 REVX</p>
                          <p class="text-xs text-gray-500">Views: 12.4K</p>
                      </div>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                      <div class="flex items-center space-x-3">
                          <div class="bg-blue-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                              <i class="fab fa-facebook-f text-blue-600"></i>
                          </div>
                          <span class="font-medium">Facebook</span>
                      </div>
                      <div class="text-right">
                          <p class="text-green-500 font-medium">+4/5 26.9K</p>
                          <p class="text-xs text-gray-500">Engagement: 8.2%</p>
                      </div>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-sky-50 rounded-lg transform transition duration-300 hover:scale-[1.01] hover:shadow">
                      <div class="flex items-center space-x-3">
                          <div class="bg-sky-100 p-2 rounded-lg transform transition duration-300 hover:rotate-12">
                              <i class="fab fa-twitter text-sky-500"></i>
                          </div>
                          <span class="font-medium">Twitter</span>
                      </div>
                      <div class="text-right">
                          <p class="text-red-500 font-medium">-6/10 6.9K</p>
                          <p class="text-xs text-gray-500">Impressions: 24.1K</p>
                      </div>
                  </div>
              </div>
          </div>

          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.5s">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Traffic Sources</h3>
            <div class="h-[200px] flex items-center justify-center mb-4">
              <Pie :data="trafficPieChartData" :options="chartOptions" />
            </div>
            <div class="space-y-3 mt-4">
              <div v-for="(data, index) in trafficSourceData" :key="index" class="flex items-center justify-between">
                <span class="w-24 font-medium">{{ data.utm_source }}</span>
                <div class="flex-1 mx-4">
                  <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div
                      class="bg-indigo-600 h-2.5 rounded-full progress-bar"
                      :style="{ width: (data.lead_count / trafficSourceData.reduce((sum, item) => sum + item.lead_count, 0) * 100) + '%' }"
                      :data-width="(data.lead_count / trafficSourceData.reduce((sum, item) => sum + item.lead_count, 0) * 100) + '%'">
                    </div>
                  </div>
                </div>
                <span class="w-10 text-right font-medium" :id="`traffic-source-percent-${index}`">{{ (data.lead_count / trafficSourceData.reduce((sum, item) => sum + item.lead_count, 0) * 100).toFixed(1) }}%</span>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.6s">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Conversion Funnel</h3>
            <div class="space-y-4">
              <div v-for="(value, key) in conversionFunnelData" :key="key" class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
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