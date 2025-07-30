<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import { ref, onMounted, computed } from 'vue'; // Import ref, onMounted, and computed

import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
// Removed RecentSales as it's no longer directly used in the template
import { Bar, Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

const currentTab = ref('Overview'); // State for active tab

function handleTabChange(tab: string) {
  currentTab.value = tab;
}

const barChartData = computed(() => ({
  labels: campaignPerformanceData.value.map(item => item.date),
  datasets: [
    {
      label: 'Impressions',
      backgroundColor: '#3b82f6',
      data: campaignPerformanceData.value.map(item => item.impressions),
    },
    {
      label: 'Clicks',
      backgroundColor: '#66BB6A',
      data: campaignPerformanceData.value.map(item => item.clicks),
    },
  ],
}));

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

const pieChartData = computed(() => ({
  labels: trafficSourceData.value.map(item => item.utm_source),
  datasets: [
    {
      backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#EF5350', '#9C27B0', '#FFC107'],
      data: trafficSourceData.value.map(item => item.lead_count),
    },
  ],
}));

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

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

const campaignPerformanceData = ref([]);
const trafficSourceData = ref([]);
const conversionFunnelData = ref({});

const fetchDashboardData = async () => {
  try {
    const response = await axios.get(route('api.admin.marketing-analytics.dashboardData'));
    dashboardStats.value = response.data;
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
  if (newTab === 'Analytics') {
    fetchDashboardData();
    fetchCampaignPerformance();
    fetchTrafficSourceDistribution();
    fetchConversionFunnel();
  }
}, { immediate: true }); // Fetch data immediately if the initial tab is 'Analytics'
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <div class="flex-1 space-y-4 p-8 pt-6">
      <DashboardTabs @tab-change="handleTabChange" />

      <div v-if="currentTab === 'Overview'">
        <!-- Row 1: Stat Cards -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
          <StatCard
            title="Total Leads"
            :value="dashboardStats.totalLeads.toLocaleString()"
            change=""
            :icon="Users"
            color="bg-blue-100"
          />
          <StatCard
            title="Converted Leads"
            :value="dashboardStats.convertedLeads.toLocaleString()"
            change=""
            :icon="Activity"
            color="bg-green-100"
          />
          <StatCard
            title="Conversion Rate"
            :value="dashboardStats.conversionRate + '%'"
            change=""
            :icon="CreditCard"
            color="bg-yellow-100"
          />
          <StatCard
            title="Total Marketing Spend"
            :value="dashboardStats.totalMarketingSpend.toLocaleString()"
            change=""
            :icon="DollarSign"
            color="bg-red-100"
          />
          <StatCard
            title="Patients Acquired"
            :value="dashboardStats.patientsAcquired.toLocaleString()"
            change=""
            :icon="Users"
            color="bg-purple-100"
          />
          <StatCard
            title="Cost Per Acquisition (CPA)"
            :value="dashboardStats.cpa.toLocaleString()"
            change=""
            :icon="DollarSign"
            color="bg-orange-100"
          />
          <StatCard
            title="Revenue Generated"
            :value="dashboardStats.revenueGenerated.toLocaleString()"
            change=""
            :icon="CreditCard"
            color="bg-teal-100"
          />
          <StatCard
            title="Return on Investment (ROI)"
            :value="dashboardStats.roi + '%'"
            change=""
            :icon="Activity"
            color="bg-pink-100"
          />
        </div>

        <!-- Row 2: Bar Chart, Pie Chart, and Table -->
        <div class="grid gap-4 grid-cols-1 lg:grid-cols-3 mt-4">
          <div class="col-span-full lg:col-span-1">
            <h3 class="text-lg font-medium">Monthly Patient Registrations Overview</h3>
            <div class="h-[350px] mt-4">
              <Bar :data="barChartData" :options="barChartOptions" />
            </div>
          </div>
          <div class="col-span-full lg:col-span-1">
            <h3 class="text-lg font-medium">Patient Demographics by Department</h3>
            <div class="h-[350px] mt-4 flex items-center justify-center">
              <Pie :data="pieChartData" :options="pieChartOptions" />
            </div>
          </div>
          <div class="col-span-full lg:col-span-1">
            <h3 class="text-lg font-medium">Recent Appointments</h3>
            <div class="mt-4 overflow-x-auto">
              <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Patient</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Date</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Time</th>
                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(appointment, index) in recentAppointments" :key="index" :class="{'bg-gray-50 dark:bg-gray-700': index % 2 === 0}">
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">{{ appointment.patient }}</td>
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">{{ appointment.date }}</td>
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">{{ appointment.time }}</td>
                    <td class="py-2 px-4 text-sm text-gray-800 dark:text-gray-200">{{ appointment.status }}</td>
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
        />
      </div>

      <div v-else-if="currentTab === 'Reports'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">Operational Reports</h3>
        <p class="text-muted-foreground mb-4">Generate and view detailed reports for various operational aspects.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <h4 class="text-lg font-medium mb-2">Monthly Revenue Summary (July 2025)</h4>
            <ul class="list-disc list-inside space-y-1 text-gray-800 dark:text-gray-200">
              <li>Total Revenue: $150,000</li>
              <li>Insurance Claims Processed: $80,000</li>
              <li>Outstanding Invoices: $20,000</li>
            </ul>
          </div>
          <div>
            <h4 class="text-lg font-medium mb-2">Staff Performance (Q2 2025)</h4>
            <ul class="list-disc list-inside space-y-1 text-gray-800 dark:text-gray-200">
              <li>Top Performer (Visits): Nurse A (120 visits)</li>
              <li>Highest Patient Satisfaction: Dr. B (4.9/5)</li>
              <li>Leave Requests Approved: 15</li>
            </ul>
          </div>
        </div>
      </div>

      <div v-else-if="currentTab === 'Notifications'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">System Notifications</h3>
        <p class="text-muted-foreground mb-4">Important alerts and updates from the Home-to-Home Care Platform.</p>
        
        <ul class="mt-4 space-y-3">
          <li class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm flex items-center space-x-3">
            <Bell class="h-5 w-5 text-blue-500" />
            <div>
              <p class="font-medium">New Patient Registered: <span class="text-blue-600 dark:text-blue-400">Sarah Connor</span></p>
              <p class="text-xs text-muted-foreground">Assigned to Dr. Smith. (5 minutes ago)</p>
            </div>
          </li>
          <li class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm flex items-center space-x-3">
            <Activity class="h-5 w-5 text-green-500" />
            <div>
              <p class="font-medium">Visit Completed: <span class="text-green-600 dark:text-green-400">Patient ID #P1023</span></p>
              <p class="text-xs text-muted-foreground">Notes and vitals uploaded. (1 hour ago)</p>
            </div>
          </li>
          <li class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm flex items-center space-x-3">
            <CreditCard class="h-5 w-5 text-yellow-500" />
            <div>
              <p class="font-medium">Pending Invoice: <span class="text-yellow-600 dark:text-yellow-400">Invoice #INV2025-001</span></p>
              <p class="text-xs text-muted-foreground">Due in 3 days. (Yesterday)</p>
            </div>
          </li>
          <li class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm flex items-center space-x-3">
            <Users class="h-5 w-5 text-purple-500" />
            <div>
              <p class="font-medium">Staff Availability Update: <span class="text-purple-600 dark:text-purple-400">Nurse Johnson</span></p>
              <p class="text-xs text-muted-foreground">New shifts added for next week. (2 days ago)</p>
            </div>
          </li>
        </ul>
      </div>
      </div>
    
  </AppLayout>
</template>
