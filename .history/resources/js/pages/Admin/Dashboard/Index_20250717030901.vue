<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import { ref } from 'vue'; // Import ref
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
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
        <h3 class="text-xl font-semibold mb-2">Analytics Dashboard</h3>
        <p class="text-muted-foreground">Detailed analytics and insights will be displayed here.</p>
        <!-- Placeholder for Analytics content -->
        <div class="mt-4 h-64 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center text-gray-500 dark:text-gray-400">
          Analytics Content Placeholder
        </div>
      </div>

      <div v-else-if="currentTab === 'Reports'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">Reports Generation</h3>
        <p class="text-muted-foreground">Generate various reports related to patient data, staff performance, and visits.</p>
        <!-- Placeholder for Reports content -->
        <div class="mt-4 h-64 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center text-gray-500 dark:text-gray-400">
          Reports Content Placeholder
        </div>
      </div>

      <div v-else-if="currentTab === 'Notifications'" class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <h3 class="text-xl font-semibold mb-2">Recent Notifications</h3>
        <p class="text-muted-foreground">View your latest system notifications and alerts.</p>
        <!-- Placeholder for Notifications content -->
        <ul class="mt-4 space-y-2">
          <li class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm">New patient registered: Jane Doe</li>
          <li class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm">Appointment scheduled for John Smith on 2025-07-18</li>
          <li class="p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm">Staff availability updated for Dr. Emily White</li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>
