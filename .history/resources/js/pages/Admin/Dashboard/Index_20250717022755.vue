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
              <button type="button" role="combobox" aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed" data-slot="select-trigger" data-size="default" class="border-input data-[placeholder]:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 dark:hover:bg-input/50 flex w-fit items-center justify-between gap-2 rounded-md border bg-transparent px-3 py-2 text-sm whitespace-nowrap shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 data-[size=default]:h-9 data-[size=sm]:h-8 *:data-[slot=select-value]:line-clamp-1 *:data-[slot=select-value]:flex *:data-[slot=select-value]:items-center *:data-[slot=select-value]:gap-2 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"><span data-slot="select-value" style="pointer-events: none;">2025</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down size-4 opacity-50" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button>
            </div>
          </div>
          <div data-slot="card-content" class="px-6">
            <div data-slot="chart" data-chart="chart-m" class="[&_.recharts-cartesian-axis-tick_text]:fill-muted-foreground [&_.recharts-cartesian-grid_line[stroke='#ccc']]:stroke-border/50 [&_.recharts-curve.recharts-tooltip-cursor]:stroke-border [&_.recharts-polar-grid_[stroke='#ccc']]:stroke-border [&_.recharts-radial-bar-background-sector]:fill-muted [&_.recharts-rectangle.recharts-tooltip-cursor]:fill-muted [&_.recharts-reference-line_[stroke='#ccc']]:stroke-border flex aspect-video justify-center text-xs [&_.recharts-dot[stroke='#fff']]:stroke-transparent [&_.recharts-layer]:outline-hidden [&_.recharts-sector]:outline-hidden [&_.recharts-sector[stroke='#fff']]:stroke-transparent [&_.recharts-surface]:outline-hidden w-full lg:h-[400px]">
              <div class="recharts-responsive-container" style="width: 100%; height: 100%; min-width: 0px;">
                <div class="recharts-wrapper" style="position: relative; cursor: default; width: 100%; height: 100%; max-height: 400px; max-width: 819px;">
                  <svg tabindex="0" role="application" class="recharts-surface" width="819" height="400" viewBox="0 0 819 400" style="width: 100%; height: 100%;">
                    <title></title>
                    <desc></desc>
                    <defs><clipPath id="recharts241-clip"><rect x="66" y="0" height="370" width="747"></rect></clipPath></defs>
                    <g class="recharts-cartesian-grid"><g class="recharts-cartesian-grid-horizontal"><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="370" x2="813" y2="370"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="277.5" x2="813" y2="277.5"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="185" x2="813" y2="185"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="92.5" x2="813" y2="92.5"></line><line stroke="#ccc" fill="none" x="66" y="0" width="747" height="370" x1="66" y1="0" x2="813" y2="0"></line></g></g>
                    <g class="recharts-layer recharts-cartesian-axis recharts-xAxis xAxis"><g class="recharts-cartesian-axis-ticks"><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="66" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="66" dy="0.71em">Jan</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="215.4" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="215.4" dy="0.71em">Feb</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="364.8" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="364.8" dy="0.71em">Mar</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="514.2" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="514.2" dy="0.71em">Apr</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="663.6" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="663.6" dy="0.71em">May</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="bottom" width="747" height="30" stroke="none" x="808.76953125" y="384" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="middle" fill="#666"><tspan x="808.76953125" dy="0.71em">Jun</tspan></text></g></g></g>
                    <g class="recharts-layer recharts-cartesian-axis recharts-yAxis yAxis"><g class="recharts-cartesian-axis-ticks"><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="370" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">0</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="277.5" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">80</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="185" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">160</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="92.5" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">240</tspan></text></g><g class="recharts-layer recharts-cartesian-axis-tick"><text orientation="left" width="60" height="370" stroke="none" x="25" y="9" class="recharts-text recharts-cartesian-axis-tick-value" text-anchor="end" fill="#666"><tspan x="25" dy="0.355em">320</tspan></text></g></g></g>
                    <g class="recharts-layer recharts-line"><path stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="none" class="recharts-curve recharts-line-curve" d="M66,154.937C115.8,90.446,165.6,25.954,215.4,17.344C265.2,8.734,315,56.005,364.8,95.969C414.6,135.932,464.4,168.588,514.2,169.969C564,171.35,613.8,141.457,663.6,128.344C713.4,115.23,763.2,118.896,813,122.563"></path><g class="recharts-layer"></g><g class="recharts-layer recharts-line-dots"><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="66" cy="154.93749999999997" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="215.4" cy="17.34375" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="364.8" cy="95.96875000000001" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="514.2" cy="169.96875" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="663.6" cy="128.34375000000003" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-famale)" stroke-width="2" width="747" height="370" fill="#fff" cx="813" cy="161.875" class="recharts-dot recharts-line-dot"></circle></g></g>
                    <g class="recharts-layer recharts-line"><path stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="none" class="recharts-curve recharts-line-curve" d="M66,208.125C115.8,146.809,165.6,85.492,215.4,104.063C265.2,122.633,315,221.089,364.8,231.25C414.6,241.411,464.4,163.277,514.2,150.313C564,137.348,613.8,189.555,663.6,219.688C713.4,249.82,763.2,257.879,813,265.938"></path><g class="recharts-layer"></g><g class="recharts-layer recharts-line-dots"><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="66" cy="208.125" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="215.4" cy="104.0625" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="364.8" cy="231.25" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="514.2" cy="150.3125" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="663.6" cy="219.6875" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-male)" stroke-width="2" width="747" height="370" fill="#fff" cx="813" cy="265.9375" class="recharts-dot recharts-line-dot"></circle></g></g>
                    <g class="recharts-layer recharts-line"><path stroke="var(--color-child)" stroke-width="2" width="747" height="370" fill="none" class="recharts-curve recharts-line-curve" d="M66,196.563C115.8,185.918,165.6,175.274,215.4,166.5C265.2,157.726,315,150.821,364.8,150.313C414.6,149.804,464.4,155.69,514.2,173.438C564,191.185,613.8,220.794,663.6,220.844C713.4,220.894,763.2,191.384,813,161.875"></path><g class="recharts-layer"></g><g class="recharts-layer recharts-line-dots"><circle r="3" stroke="var(--color-child)" stroke-width="2" width="747" height="370" fill="#fff" cx="66" cy="196.5625" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-child)" stroke-width="2" width="747" height="370" fill="#fff" cx="215.4" cy="166.49999999999997" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-child)" stroke-width="2" width="747" height="370" fill="#fff" cx="364.8" cy="150.3125" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-child)" stroke-width="2" width="747" height="370" fill="#fff" cx="514.2" cy="173.4375" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-child)" stroke-width="2" width="747" height="370" fill="#fff" cx="663.6" cy="220.84375000000003" class="recharts-dot recharts-line-dot"></circle><circle r="3" stroke="var(--color-child)" stroke-width="2" width="747" height="370" fill="#fff" cx="813" cy="161.875" class="recharts-dot recharts-line-dot"></circle></g></g></svg><div tabindex="-1" class="recharts-tooltip-wrapper recharts-tooltip-wrapper-right recharts-tooltip-wrapper-bottom" style="visibility: hidden; pointer-events: none; position: absolute; top: 0px; left: 0px; transform: translate(76px, 210px);"></div></div></div></div>
          </div>
        </div>
        <div data-slot="card" class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 col-span-3">
          <div data-slot="card-header" class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-[data-slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
            <div data-slot="card-title" class="leading-none font-semibold">Patients by Department</div>
          </div>
          <div data-slot="card-content" class="px-6">
            <div data-slot="chart" data-chart="chart-n" class="[&_.recharts-cartesian-axis-tick_text]:fill-muted-foreground [&_.recharts-cartesian-grid_line[stroke='#ccc']]:stroke-border/50 [&_.recharts-curve.recharts-tooltip-cursor]:stroke-border [&_.recharts-polar-grid_[stroke='#ccc']]:stroke-border [&_.recharts-radial-bar-background-sector]:fill-muted [&_.recharts-rectangle.recharts-tooltip-cursor]:fill-muted [&_.recharts-reference-line_[stroke='#ccc']]:stroke-border flex justify-center text-xs [&_.recharts-dot[stroke='#fff']]:stroke-transparent [&_.recharts-layer]:outline-hidden [&_.recharts-sector]:outline-hidden [&_.recharts-sector[stroke='#fff']]:stroke-transparent [&_.recharts-surface]:outline-hidden [&_.recharts-pie-label-text]:fill-foreground mx-auto aspect-square max-h-[400px] pb-0">
              <div class="recharts-responsive-container" style="width: 100%; height: 100%; min-width: 0px;">
                <div class="recharts-wrapper" style="position: relative; cursor: default; width: 100%; height: 100%; max-height: 400px; max-width: 400px;">
                  <svg cx="50%" cy="50%" class="recharts-surface" width="400" height="400" viewBox="0 0 400 400" style="width: 100%; height: 100%;">
                    <title></title>
                    <desc></desc>
                    <defs><clipPath id="recharts248-clip"><rect x="5" y="5" height="338" width="390"></rect></clipPath></defs>
                    <g class="recharts-layer recharts-pie" tabindex="0"><g class="recharts-layer"><g class="recharts-layer recharts-pie-sector" tabindex="-1"><path cx="200" cy="174" name="cardiology" stroke="#fff" fill="var(--color-cardiology)" tabindex="-1" class="recharts-sector" d="M 335.2,174
    A 135.2,135.2,0,
    0,0,
    135.35779015405487,55.25479080723808
  L 200,174 Z" role="img"></path></g><g class="recharts-layer recharts-pie-sector" tabindex="-1"><path cx="200" cy="174" name="neurology" stroke="#fff" fill="var(--color-neurology)" tabindex="-1" class="recharts-sector" d="M 135.35779015405487,55.25479080723808
    A 135.2,135.2,0,
    0,0,
    77.25900374546875,230.68939793687304
  L 200,174 Z" role="img"></path></g><g class="recharts-layer recharts-pie-sector" tabindex="-1"><path cx="200" cy="174" name="oncology" stroke="#fff" fill="var(--color-oncology)" tabindex="-1" class="recharts-sector" d="M 77.25900374546875,230.68939793687304
    A 135.2,135.2,0,
    0,0,
    235.9331556890521,304.3374402166405
  L 200,174 Z" role="img"></path></g><g class="recharts-layer recharts-pie-sector" tabindex="-1"><path cx="200" cy="174" name="pediatrics" stroke="#fff" fill="var(--color-pediatrics)" tabindex="-1" class="recharts-sector" d="M 235.9331556890521,304.3374402166405
    A 135.2,135.2,0,
    0,0,
    335.2,174.00000000000003
  L 200,174 Z" role="img"></path></g></g><g class="recharts-layer recharts-pie-labels"><g class="recharts-layer"><path cx="200" cy="174" stroke="var(--color-cardiology)" fill="none" name="cardiology" class="recharts-curve recharts-pie-label-line" d="M269.063,57.77L279.279,40.577"></path><text cx="200" cy="174" stroke="none" name="cardiology" alignment-baseline="middle" x="279.279491968082" y="40.576605674706286" class="recharts-text recharts-pie-label-text" text-anchor="start" fill="var(--color-cardiology)"><tspan x="279.279491968082" dy="0em">275</tspan></text></g><g class="recharts-layer"><path cx="200" cy="174" stroke="var(--color-neurology)" fill="none" name="neurology" class="recharts-curve recharts-pie-label-line" d="M71.655,131.496L52.669,125.208"></path><text cx="200" cy="174" stroke="none" name="neurology" alignment-baseline="middle" x="52.669040724639615" y="125.20831588270295" class="recharts-text recharts-pie-label-text" text-anchor="end" fill="var(--color-neurology)"><tspan x="52.669040724639615" dy="0em">200</tspan></text></g><g class="recharts-layer"><path cx="200" cy="174" stroke="var(--color-oncology)" fill="none" name="oncology" class="recharts-curve recharts-pie-label-line" d="M143.08,296.634L134.66,314.775"></path><text cx="200" cy="174" stroke="none" name="oncology" alignment-baseline="middle" x="134.65965549157815" y="314.7752797174303" class="recharts-text recharts-pie-label-text" text-anchor="end" fill="var(--color-oncology)"><tspan x="134.65965549157815" dy="0em">187</tspan></text></g><g class="recharts-layer"><path cx="200" cy="174" stroke="var(--color-pediatrics)" fill="none" name="pediatrics" class="recharts-curve recharts-pie-label-line" d="M307.557,255.917L323.468,268.035"></path><text cx="200" cy="174" stroke="none" name="pediatrics" alignment-baseline="middle" x="323.46829550177654" y="268.03520620430413" class="recharts-text recharts-pie-label-text" text-anchor="start" fill="var(--color-pediatrics)"><tspan x="323.46829550177654" dy="0em">173</tspan></text></g></g></g></svg><div class="recharts-legend-wrapper" style="position: absolute; width: 390px; height: auto; left: 5px; bottom: 5px;"><div class="flex items-center justify-center pt-3 -translate-y-2 flex-wrap gap-2 *:basis-1/4 *:justify-center"><div class="[&>svg]:text-muted-foreground flex items-center gap-1.5 [&>svg]:h-3 [&>svg]:w-3"><div class="h-2 w-2 shrink-0 rounded-[2px]" style="background-color: var(--color-cardiology);"></div>Cardiology</div><div class="[&>svg]:text-muted-foreground flex items-center gap-1.5 [&>svg]:h-3 [&>svg]:w-3"><div class="h-2 w-2 shrink-0 rounded-[2px]" style="background-color: var(--color-neurology);"></div>Neurology</div><div class="[&>svg]:text-muted-foreground flex items-center gap-1.5 [&>svg]:h-3 [&>svg]:w-3"><div class="h-2 w-2 shrink-0 rounded-[2px]" style="background-color: var(--color-oncology);"></div>Oncology</div><div class="[&>svg]:text-muted-foreground flex items-center gap-1.5 [&>svg]:h-3 [&>svg]:w-3"><div class="h-2 w-2 shrink-0 rounded-[2px]" style="background-color: var(--color-pediatrics);"></div>Pediatrics</div></div></div><div tabindex="-1" class="recharts-tooltip-wrapper" style="visibility: hidden; pointer-events: none; position: absolute; top: 0px; left: 0px;"></div></div></div></div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
[data-chart=chart-m] {
  --color-famale: var(--chart-1);
  --color-male: var(--chart-2);
  --color-child: var(--chart-2);
}
.dark [data-chart=chart-m] {
  --color-famale: var(--chart-1);
  --color-male: var(--chart-2);
  --color-child: var(--chart-2);
}
[data-chart=chart-n] {
  --color-cardiology: var(--chart-1);
  --color-neurology: var(--chart-2);
  --color-oncology: var(--chart-3);
  --color-pediatrics: var(--chart-4);
}
.dark [data-chart=chart-n] {
  --color-cardiology: var(--chart-1);
  --color-neurology: var(--chart-2);
  --color-oncology: var(--chart-3);
  --color-pediatrics: var(--chart-4);
}
</style>
