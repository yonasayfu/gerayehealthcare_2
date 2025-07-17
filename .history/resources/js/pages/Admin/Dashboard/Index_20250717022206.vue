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
              <button type="button" role="combobox" aria-controls="radix-«r3l»" aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed" data-slot="select-trigger" data-size="default" class="border-input data-[placeholder]:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 dark:hover:bg-input/50 flex w-fit items-center justify-between gap-2 rounded-md border bg-transparent px-3 py-2 text-sm whitespace-nowrap shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 data-[size=default]:h-9 data-[size=sm]:h-8 *:data-[slot=select-value]:line-clamp-1 *:data-[slot=select-value]:flex *:data-[slot=select-value]:items-center *:data-[slot=select-value]:gap-2 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"><span data-slot="select-value" style="pointer-events: none;">2025</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down size-4 opacity-50" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button>
            </div>
          </div>
          <div data-slot="card-content" class="px-6">
            <div data-slot="chart" data-chart="chart-«r3m»" class="[&_.recharts-cartesian-axis-tick_text]:fill-muted-foreground [&_.recharts-cartesian-grid_line[stroke='#ccc']]:stroke-border/50 [&_.recharts-curve.recharts-tooltip-cursor]:stroke-border [&_.recharts-polar-grid_[stroke='#ccc']]:stroke-border [&_.recharts-radial-bar-background-sector]:fill-muted [&_.recharts-rectangle.recharts-tooltip-cursor]:fill-muted [&_.recharts-reference-line_[stroke='#ccc']]:stroke-border flex aspect-video justify-center text-xs [&_.recharts-dot[stroke='#fff']]:stroke-transparent [&_.recharts-layer]:outline-hidden [&_.recharts-sector]:outline-hidden [&_.recharts-sector[stroke='#fff']]:stroke-transparent [&_.recharts-surface]:outline-hidden w-full lg:h-[400px]">
              <style>
                [data-chart=chart-«r3m»] {
                  --color-famale: var(--chart-1);
                  --color-male: var(--chart-2);
                  --color-child: var(--chart-2);
                }
                .dark [data-chart=chart-«r3m»] {
                  --color-famale: var(--chart-1);
                  --color-male: var(--chart-2);
                  --color-child: var(--chart-2);
                }
              </style>
              <div class="recharts-responsive-container" style="width: 100%; height: 100%; min-width: 0px;">
                <div class="recharts-wrapper" style="position: relative; cursor: default; width: 100%; height: 100%; max-height: 400px; max-width: 819px;">
                  <svg tabindex="0" role="application" class="recharts-surface" width="819" height="400" viewBox="0 0 819 400" style="width: 100%; height: 100%;">
                    <title></title>
                    <desc></desc>
                    <defs><clipPath id="recharts241-clip"><rect x="66" y="0" height="370" width="747"></rect></clipPath></defs>
                    <g class="recharts-cartesian-grid"><g class="recharts-cartesian-grid-horizontal"><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="370" x2="813" y2="370"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="277.5" x2="813" y2="277.5"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="185" x2="813" y2="185"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="92.5" x2="813" y2="92.5"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="0" x2="813" y2="0"></line></g></g>
                    <g class="recharts-layer recharts-cartesian-axis recharts-xAxis xAxis"><g class="recharts-cartesian-axis-ticks"><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="66" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="66" dy="0.71em">Jan</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="215.4" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="215.4" dy="0.71em">Feb</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="364.8" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="364.8" dy="0.71em">Mar</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="514.2" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="514.2" dy="0.71em">Apr</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="663.6" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="663.6" dy="0.71em">May</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="808.76953125" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="808.76953125" dy="0.71em">Jun</tspan></text></g></g></g>
                    <g class="recharts-layer recharts-cartesian-axis recharts-yAxis yAxis"><g class="recharts-cartesian-axis-ticks"><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="370" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">0</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="277.5" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">80</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="185" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">160</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="92.5" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">240</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="9" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">320</tspan></text></g></g></g>
                    <g class="recharts-layer recharts-line"><path stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="none" class="recharts-curve recharts-line-curve" d="M66,154.937C115.8,90.446,165.6,25.954,215.4,17.344C265.2,8.734,315,56.005,364.8,95.969C414.6,135.932,464.4,168.588,514.2,169.969C564,171.35,613.8,141.457,663.6,128.344C713.4,115.23,763.2,118.896,813,122.563"></path><g class="recharts-layer"></g><g class="recharts-layer recharts-line-dots"><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="66" cy="154.93749999999997" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="215.4" cy="17.34375" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="364.8" cy="95.96875000000001" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="514.2" cy="169.96875" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="663.6" cy="128.34375000000003" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="813" cy="122.56250000000001" class="recharts-dot recharts-line-dot"></circle></g></g>
                    <g class="recharts-layer recharts-line"><path stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="none" class="recharts-curve recharts-line-curve" d="M66,208.125C115.8,146.809,165.6,85.492,215.4,104.063C265.2,122.633,315,221.089,364.8,231.25C414.6,241.411,464.4,163.277,514.2,150.313C564,137.348,613.8,189.555,663.6,219.688C713.4,249.82,763.2,257.879,813,265.938"></path><g class="recharts-layer"></g><g class="recharts-layer recharts-line-dots"><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="66" cy="208.125" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="215.4" cy="104.0625" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="364.8" cy="231.25" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="514.2" cy="150.3125" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="663.6" cy="219.6875" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="813" cy="265.9375" class="recharts-dot recharts-line-dot"></circle></g></g>
