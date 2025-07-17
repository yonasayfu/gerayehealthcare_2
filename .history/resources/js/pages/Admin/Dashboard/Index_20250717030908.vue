<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import { ref } from 'vue'; // Import ref
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
// Removed RecentSales as it's no longer directly used in the template
import { Bar, Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

const currentTab = ref('Overview'); // State for active tab

function handleTabChange(tab: string) {
  currentTab.value = tab;
}

const barChartData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [
    {
      label: 'Monthly Patient Registrations',
      backgroundColor: '#3b82f6',
      data: [400, 300, 500, 450, 600, 550, 700, 650, 800, 750, 900, 850],
    },
  ],
}

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}

const pieChartData = {
  labels: ['Cardiology', 'Pediatrics', 'Orthopedics', 'Dermatology'],
  datasets: [
    {
      backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726', '#EF5350'],
      data: [300, 50, 100, 75],
    },
  ],
}

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}

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
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <div class="flex-1 space-y-4 p-8 pt-6">
      <DashboardHeader />
      <DashboardTabs @tab-change="handleTabChange" />

      <div v-if="currentTab === 'Overview'">
        <!-- Row 1: Stat Cards -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
          <StatCard
            title="Total Patients"
            value="1,234"
            change="+15% from last month"
            :icon="Users"
            color="bg-blue-100"
          />
          <StatCard
            title="Active Staff"
            value="250"
            change="+5% from last month"
            :icon="Activity"
            color="bg-green-100"
          />
          <StatCard
            title="Completed Visits"
            value="876"
            change="+10% from last month"
            :icon="CreditCard"
            color="bg-yellow-100"
          />
          <StatCard
            title="Pending Tasks"
            value="42"
            change="-5% from last week"
            :icon="DollarSign"
            color="bg-red-100"
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

      <div v-else-if="currentTab === 'Analytics'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">Service Analytics</h3>
        <p class="text-muted-foreground mb-4">Key performance indicators for services and marketing campaigns.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <h4 class="text-lg font-medium mb-2">Service Volume by Type</h4>
            <ul class="list-disc list-inside space-y-1 text-gray-800 dark:text-gray-200">
              <li>Home Visits: 750 (60%)</li>
              <li>Teleconsultations: 300 (25%)</li>
              <li>Clinic Appointments: 150 (15%)</li>
            </ul>
          </div>
          <div>
            <h4 class="text-lg font-medium mb-2">Marketing Campaign Performance (Last Month)</h4>
            <ul class="list-disc list-inside space-y-1 text-gray-800 dark:text-gray-200">
              <li>TikTok Ads: +200 new patients (ROI: 150%)</li>
              <li>Facebook Ads: +100 new patients (ROI: 120%)</li>
              <li>Referral Program: +50 new patients</li>
            </ul>
          </div>
        </div>
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
