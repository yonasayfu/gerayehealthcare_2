<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import AdminStatCard from '@/components/AdminStatCard.vue';
import RecentSales from '@/components/RecentSales.vue';
import { DollarSign, Users, CreditCard, Activity, Calendar } from 'lucide-vue-next';
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const chartData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [
    {
      label: 'Total Revenue',
      backgroundColor: '#3b82f6',
      data: [4000, 3000, 2000, 2780, 1890, 2390, 3490, 2000, 2780, 1890, 2390, 3490],
    },
  ],
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}

const props = defineProps({
  stats: {
    type: Object,
    required: false,
  },
});
</script>

<template>
  <Head title="Admin Dashboard" />

  <AppLayout>
    <div class="flex-1 space-y-4 p-8 pt-6">
      <DashboardHeader />
      <DashboardTabs />
      <div class="grid md:grid-cols-2 lg:grid-cols-4">
        <AdminStatCard
          title="Total Appointments"
          value="2350"
          change="+20.1% from last month"
          :icon="Calendar"
          color="indigo"
        />
        <AdminStatCard
          title="New Patients"
          value="145"
          change="+180.1% from last month"
          :icon="Users"
          color="green"
        />
        <AdminStatCard
          title="Operations"
          value="89"
          change="-19% from last month"
          :icon="CreditCard"
          color="purple"
        />
        <AdminStatCard
          title="Total Revenue"
          value="$9583"
          change="+20.1% from last month"
          :icon="DollarSign"
          color="orange"
        />
      </div>
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
        <div class="col-span-4">
          <h3 class="text-lg font-medium">Overview</h3>
          <div class="h-[350px] mt-4">
            <Bar :data="chartData" :options="chartOptions" />
          </div>
        </div>
        <div class="col-span-3">
          <h3 class="text-lg font-medium">Recent Sales</h3>
          <p class="text-sm text-muted-foreground">You made 265 sales this month.</p>
          <RecentSales />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
