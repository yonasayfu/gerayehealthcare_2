<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import AdminStatCard from '@/components/AdminStatCard.vue';
import RecentSales from '@/components/RecentSales.vue';
import { DollarSign, Users, CreditCard, Calendar } from 'lucide-vue-next';
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
    required: true,
  },
  recentVisits: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <Head title="Admin Dashboard" />

  <AppLayout>
    <div class="flex-1 space-y-4 p-8 pt-6">
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <AdminStatCard
          title="Total Appointments"
          :value="stats.total_appointments"
          change="+20.1% from last month"
          :icon="Calendar"
          color="indigo"
        />
        <AdminStatCard
          title="New Patients"
          :value="stats.new_patients"
          change="+180.1% from last month"
          :icon="Users"
          color="green"
        />
        <AdminStatCard
          title="Operations"
          :value="stats.operations"
          change="-19% from last month"
          :icon="CreditCard"
          color="purple"
        />
        <AdminStatCard
          title="Total Revenue"
          :value="`$${stats.total_revenue}`"
          change="+20.1% from last month"
          :icon="DollarSign"
          color="orange"
        />
      </div>
      <div class="gap-4 space-y-4 md:grid-cols-2 lg:grid lg:grid-cols-7 lg:space-y-0">
        <div data-slot="card" class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 col-span-4">
          <div data-slot="card-header" class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-[data-slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
            <div data-slot="card-title" class="leading-none font-semibold">Patient Visits by Gender</div>
            <div data-slot="card-action" class="col-start-2 row-span-2 row-start-1 self-start justify-self-end">
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
