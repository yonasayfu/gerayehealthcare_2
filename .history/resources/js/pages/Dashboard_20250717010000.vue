<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DashboardHeader from '@/components/DashboardHeader.vue';
import DashboardTabs from '@/components/DashboardTabs.vue';
import StatCard from '@/components/StatCard.vue';
import RecentSales from '@/components/RecentSales.vue';
import { DollarSign, Users, CreditCard, Activity } from 'lucide-vue-next';
import ChartBarLabelCustom from '@/components/ChartBarLabelCustom.vue'

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
      <DashboardTabs />
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <StatCard
          title="Total Revenue"
          :value="`$${stats?.totalRevenue || '0.00'}`"
          change="+20.1% from last month"
          :icon="DollarSign"
        />
        <StatCard
          title="Subscriptions"
          :value="`+${stats?.subscriptions || '0'}`"
          change="+180.1% from last month"
          :icon="Users"
        />
        <StatCard
          title="Sales"
          :value="`+${stats?.sales || '0'}`"
          change="+19% from last month"
          :icon="CreditCard"
        />
        <StatCard
          title="Active Now"
          :value="`+${stats?.activeNow || '0'}`"
          change="+201 since last hour"
          :icon="Activity"
        />
      </div>
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
        <div class="col-span-4">
          <ChartBarLabelCustom />
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
