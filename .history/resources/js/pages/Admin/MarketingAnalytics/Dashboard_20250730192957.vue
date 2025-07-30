<script setup lang="ts">
import { defineProps, ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
import { Bar, Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';

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
  conversionFunnelData?: ConversionFunnelData; // Made optional to handle initial undefined
}>();

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
  animateValue('total-marketing-spend-counter', 0, parseFloat(props.dashboardStats.totalMarketingSpend), 2000, '$');
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
});

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

// Ensure conversionFunnelData is always an object, even if empty
const safeConversionFunnelData = computed(() => props.conversionFunnelData || { New: 0, Contacted: 0, Qualified: 0, Converted: 0 });

</script>

<template>
  <AppLayout title="Marketing Analytics Dashboard">
    <Head title="Marketing Analytics Dashboard" />

    <div class="container mx-auto p-4 md:p-6">
      <header class="flex justify-between items-center mb-8 animate-fadeIn" style="animation-delay: 0.1s">
        <h1 class="text-3xl font-bold text-gray-800 transform transition duration-500 hover:scale-105">Marketing Analytics</h1>
        <div class="flex items-center space-x-4">
          <button class="p-2 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors duration-300 relative">
            <i class="fas fa-bell"></i>
            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500 pulse"></span>
          </button>
          <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold hover:bg-indigo-600 transition-colors duration-300 cursor-pointer">
            MA
          </div>
        </div>
      </header>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.2s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Total Leads</h2>
              <p class="text-3xl font-bold text-gray-800" id="total-leads-counter">0</p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <Users class="text-indigo-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">Overall leads generated</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.3s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Converted Leads</h2>
              <p class="text-3xl font-bold text-gray-800" id="converted-leads-counter">0</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <Activity class="text-green-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">Leads turned into patients</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.4s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Conversion Rate</h2>
              <p class="text-3xl font-bold text-gray-800" id="conversion-rate-counter">0%</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <CreditCard class="text-yellow-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">Success rate of leads</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.5s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Total Marketing Spend</h2>
              <p class="text-3xl font-bold text-gray-800" id="total-marketing-spend-counter">$0</p>
            </div>
            <div class="bg-red-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <DollarSign class="text-red-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">Total cost of campaigns</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.6s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Patients Acquired</h2>
              <p class="text-3xl font-bold text-gray-800" id="patients-acquired-counter">0</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <Users class="text-blue-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">New patients from marketing</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.7s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Cost Per Acquisition (CPA)</h2>
              <p class="text-3xl font-bold text-gray-800" id="cpa-counter">$0</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <DollarSign class="text-purple-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">Cost to acquire one patient</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.8s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Revenue Generated</h2>
              <p class="text-3xl font-bold text-gray-800" id="revenue-generated-counter">$0</p>
            </div>
            <div class="bg-teal-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <CreditCard class="text-teal-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">Total revenue from campaigns</p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.01] card-hover animate-fadeIn" style="animation-delay: 0.9s">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h2 class="text-lg font-medium text-gray-700 mb-1">Return on Investment (ROI)</h2>
              <p class="text-3xl font-bold text-gray-800" id="roi-counter">0%</p>
            </div>
            <div class="bg-pink-100 p-3 rounded-lg transform transition duration-500 hover:rotate-12">
              <Activity class="text-pink-600 text-xl" />
            </div>
          </div>
          <p class="text-sm text-gray-500">Profitability of marketing efforts</p>
        </div>
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

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.1s">
          <h3 class="text-lg font-medium text-gray-700 mb-4">Traffic Sources</h3>
          <div class="h-[300px] flex items-center justify-center">
            <Pie :data="trafficPieChartData" :options="chartOptions" />
          </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 transform transition duration-500 hover:scale-[1.02] card-hover animate-fadeIn" style="animation-delay: 1.2s">
          <h3 class="text-lg font-medium text-gray-700 mb-4">Conversion Funnel</h3>
          <div x-data="{
              chartData: [{{ safeConversionFunnelData.New }}, {{ safeConversionFunnelData.Contacted }}, {{ safeConversionFunnelData.Qualified }}, {{ safeConversionFunnelData.Converted }}],
              labels: ['New', 'Contacted', 'Qualified', 'Converted'],
              tooltipContent: '',
              tooltipOpen: false,
              tooltipX: 0,
              tooltipY: 0,
              showTooltip(e) {
                  const target = e.target;
                  this.tooltipContent = target.textContent || '';
                  this.tooltipX = target.offsetLeft - target.clientWidth;
                  this.tooltipY = target.clientHeight + target.clientWidth;
                  this.tooltipOpen = true;
              },
              hideTooltip() {
                  this.tooltipContent = '';
                  this.tooltipOpen = false;
                  this.tooltipX = 0;
                  this.tooltipY = 0;
              },
              getChartWidthStyle() {
                  if (!this.chartData || this.chartData.length === 0) {
                      return `width: 0%;`; // Or a default small width
                  }
                  return `width: ${ 100 - (1 / this.chartData.length) * 100 + 3}%;`;
              }
          }" x-cloak class="px-4">
            <div class="max-w-lg mx-auto py-4">
              <div class="bg-white">
                <div class="md:flex md:justify-between md:items-center mb-4">
                  <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-600 mr-2 rounded-full"></div>
                    <div class="text-sm text-gray-700">Leads</div>
                  </div>
                </div>

                <div class="line my-4 relative">
                  <template x-if="tooltipOpen == true">
                    <div x-ref="tooltipContainer" class="p-0 m-0 z-10 shadow-lg rounded-lg absolute h-auto block" :style="`bottom: ${tooltipY}px; left: ${tooltipX}px`">
                      <div class="shadow-xs rounded-lg bg-white p-2">
                        <div class="flex items-center justify-between text-sm">
                          <div>Leads:</div>
                          <div class="font-bold ml-2">
                            <span x-html="tooltipContent"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>

                  <div class="flex -mx-2 items-end mb-2 h-[200px]"> <template x-for="(data, index) in chartData" :key="index">
                      <div class="px-2 w-1/4"> <div :style="`height: ${data > 0 && Math.max(...chartData) > 0 ? (data / Math.max(...chartData)) * 100 : 0}%`"
                             class="transition ease-in duration-200 bg-blue-600 hover:bg-blue-400 relative flex items-end justify-center pb-2"
                             @mouseenter="showTooltip($event)" @mouseleave="hideTooltip($event)">
                          <div x-text="data" class="text-center text-white text-sm font-semibold"></div>
                        </div>
                      </div>
                    </template>
                  </div>

                  <div class="border-t border-gray-400 mx-auto" :style="getChartWidthStyle()"></div>
                  <div class="flex -mx-2 items-end">
                    <template x-for="(label, index) in labels" :key="index">
                      <div class="px-2 w-1/4"> <div class="bg-white relative">
                          <div class="text-center absolute top-0 left-0 right-0 h-2 -mt-px bg-gray-400 mx-auto" style="width: 1px"></div>
                          <div x-text="label" class="text-center absolute top-0 left-0 right-0 mt-3 text-gray-700 text-sm"></div>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
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
/* Alpine.js chart specific styles */
.line {
    background: repeating-linear-gradient(
        to bottom,
        #eee,
        #eee 1px,
        #fff 1px,
        #fff 8%
    );
}
.tick {
    background: repeating-linear-gradient(
        to right,
        #eee,
        #eee 1px,
        #fff 1px,
        #fff 5%
    );
}
</style>