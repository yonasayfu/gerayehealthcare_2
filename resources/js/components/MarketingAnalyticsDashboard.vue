<script setup lang="ts">
import { computed, toRefs } from 'vue'
import StatCard from '@/components/StatCard.vue'
import Tooltip from '@/components/Tooltip.vue'
import { Users, Activity, CreditCard, Percent, DollarSign } from 'lucide-vue-next'

interface DashboardStats {
  totalLeads: number
  convertedLeads: number
  conversionRate: number
  totalMarketingSpend: number
  patientsAcquired: number
  cpa: number
  revenueGenerated: number
  roi: number
}

const props = defineProps<{
  dashboardStats: DashboardStats
  campaignPerformanceData: Array<any>
  trafficSourceData: Array<any>
  conversionFunnelData: Record<string, any>
  loading?: boolean
}>()

const { dashboardStats, campaignPerformanceData, trafficSourceData, conversionFunnelData } = toRefs(props)

const fmtPct = (v: number | null | undefined) =>
  `${(v ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%`

const topTrafficSources = computed(() => (trafficSourceData.value || []).slice(0, 5))

// Currency formatter (align with app global if present)
const appCurrency = (window as any)?.appCurrency || 'USD'
const money = (v: number) => new Intl.NumberFormat(undefined, { style: 'currency', currency: appCurrency, minimumFractionDigits: 2 }).format(Number(v || 0))

// Build a friendly conversion funnel from whatever keys exist
const funnelStages = computed(() => {
  const d: any = conversionFunnelData.value || {}
  // Detect schema: status-based vs metric-based
  if (d.New !== undefined || d.Contacted !== undefined || d.Qualified !== undefined || d.Converted !== undefined) {
    const order = [
      { key: 'New', label: 'New' },
      { key: 'Contacted', label: 'Contacted' },
      { key: 'Qualified', label: 'Qualified' },
      { key: 'Converted', label: 'Converted' },
    ]
    const stages = order.map(s => ({ ...s, value: Number(d[s.key] || 0) }))
    return stages.map((s, idx) => {
      const prev = idx > 0 ? stages[idx - 1] : undefined
      const rate = prev && prev.value > 0 ? (s.value / prev.value) * 100 : null
      return { ...s, rate }
    })
  }

  // Metric-based fallback
  const order = [
    { key: 'impressions', label: 'Impressions' },
    { key: 'clicks', label: 'Clicks' },
    { key: 'leads', label: 'Leads' },
    { key: 'appointments', label: 'Appointments' },
    { key: 'patients', label: 'Patients' },
    { key: 'revenue', label: 'Revenue', isMoney: true },
  ]
  const stages = order
    .filter(s => d[s.key] !== undefined && d[s.key] !== null)
    .map(s => ({ ...s, value: Number(d[s.key]) }))
  return stages.map((s, idx) => {
    const prev = idx > 0 ? stages[idx - 1] : undefined
    const rate = prev && prev.value > 0 && !s.isMoney ? (s.value / prev.value) * 100 : null
    return { ...s, rate }
  })
})
</script>

<template>
  <div class="space-y-6">
    <!-- KPI cards (StatCard style) -->
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
      <Tooltip text="Total marketing leads in the selected range">
        <StatCard
          title="Total Leads"
          :value="!$props.loading ? (dashboardStats.totalLeads ?? 0).toLocaleString() : '—'"
          change=""
          :icon="Users"
          color="bg-blue-100"
        />
      </Tooltip>
      <Tooltip text="Leads converted to patients (linked)">
        <StatCard
          title="Converted Leads"
          :value="!$props.loading ? (dashboardStats.convertedLeads ?? 0).toLocaleString() : '—'"
          change=""
          :icon="Activity"
          color="bg-green-100"
        />
      </Tooltip>
      <Tooltip text="Lead-to-patient conversion rate">
        <StatCard
          title="Conversion Rate"
          :value="!$props.loading ? fmtPct(dashboardStats.conversionRate) : '—'"
          change=""
          :icon="Percent"
          color="bg-yellow-100"
        />
      </Tooltip>
      <Tooltip text="Return on Investment for the range">
        <StatCard
          title="ROI"
          :value="!$props.loading ? fmtPct(dashboardStats.roi) : '—'"
          change=""
          :icon="CreditCard"
          color="bg-teal-100"
        />
      </Tooltip>
      <Tooltip text="Total marketing spend in the selected range">
        <StatCard
          title="Total Spend"
          :value="!$props.loading ? money(dashboardStats.totalMarketingSpend ?? 0) : '—'"
          change=""
          :icon="CreditCard"
          color="bg-orange-100"
        />
      </Tooltip>
      <Tooltip text="Revenue attributed to marketing in the range">
        <StatCard
          title="Revenue Generated"
          :value="!$props.loading ? money(dashboardStats.revenueGenerated ?? 0) : '—'"
          change=""
          :icon="DollarSign"
          color="bg-indigo-100"
        />
      </Tooltip>
    </div>

    <!-- Campaign Performance -->
    <div class="grid gap-4 grid-cols-1 lg:grid-cols-2">
      <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Campaign Performance</h4>
        <template v-if="$props.loading">
          <div class="space-y-2">
            <div v-for="i in 5" :key="i" class="h-4 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
          </div>
        </template>
        <ul v-else class="text-sm text-gray-700 dark:text-gray-200 list-disc list-inside space-y-1">
          <li v-for="(row, idx) in (campaignPerformanceData || []).slice(0, 6)" :key="idx">
            {{ row.date ?? '—' }}: {{ row.impressions ?? 0 }} impressions, {{ row.clicks ?? 0 }} clicks
          </li>
          <li v-if="!campaignPerformanceData || campaignPerformanceData.length === 0" class="text-gray-500">No campaign performance data</li>
        </ul>
      </div>

      <!-- Traffic Sources -->
      <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Top Traffic Sources</h4>
        <template v-if="$props.loading">
          <div class="space-y-2">
            <div v-for="i in 5" :key="i" class="h-4 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
          </div>
        </template>
        <ul v-else class="text-sm text-gray-700 dark:text-gray-200 list-disc list-inside space-y-1">
          <li v-for="(row, idx) in topTrafficSources" :key="idx">
            {{ row.utm_source ?? '—' }}: {{ row.lead_count ?? 0 }} leads
          </li>
          <li v-if="!topTrafficSources || topTrafficSources.length === 0" class="text-gray-500">No traffic source data</li>
        </ul>
      </div>
    </div>

    <!-- Conversion Funnel -->
    <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
      <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Conversion Funnel</h4>
      <template v-if="$props.loading">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
          <div v-for="i in 4" :key="i" class="h-20 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
        </div>
      </template>
      <template v-else>
        <div v-if="funnelStages.length" class="grid grid-cols-2 lg:grid-cols-4 gap-3">
          <Tooltip v-for="s in funnelStages" :key="s.key" :text="s.rate !== null ? 'Step conversion from previous: ' + (s.rate as number).toFixed(1) + '%' : 'Stage value'">
            <div class="p-3 rounded bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
              <div class="text-sm text-gray-600 dark:text-gray-300">{{ s.label }}</div>
              <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ s.isMoney ? money(s.value) : s.value.toLocaleString() }}</div>
              <div v-if="s.rate !== null" class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ (s.rate as number).toFixed(1) }}% from prev</div>
            </div>
          </Tooltip>
        </div>
        <div v-else class="text-sm text-gray-500">No conversion funnel data</div>
      </template>
    </div>
  </div>
</template>
