<script setup lang="ts">
import { computed, toRefs } from 'vue'

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
  const d = conversionFunnelData.value || {}
  // Preferred order; include only if present
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

  // Compute step conversion rate vs previous stage when numeric
  return stages.map((s, idx) => {
    const prev = idx > 0 ? stages[idx - 1] : undefined
    const rate = prev && prev.value > 0 && !s.isMoney
      ? (s.value / prev.value) * 100
      : null
    return { ...s, rate }
  })
})
</script>

<template>
  <div class="space-y-6">
    <!-- KPI cards -->
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
      <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
        <div class="text-sm text-gray-500 dark:text-gray-300">Total Leads</div>
        <div v-if="!$props.loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ dashboardStats.totalLeads?.toLocaleString?.() ?? '-' }}</div>
        <div v-else class="h-7 mt-1 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
      </div>
      <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
        <div class="text-sm text-gray-500 dark:text-gray-300">Converted Leads</div>
        <div v-if="!$props.loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ dashboardStats.convertedLeads?.toLocaleString?.() ?? '-' }}</div>
        <div v-else class="h-7 mt-1 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
      </div>
      <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
        <div class="text-sm text-gray-500 dark:text-gray-300">Conversion Rate</div>
        <div v-if="!$props.loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ fmtPct(dashboardStats.conversionRate) }}</div>
        <div v-else class="h-7 mt-1 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
      </div>
      <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
        <div class="text-sm text-gray-500 dark:text-gray-300">ROI</div>
        <div v-if="!$props.loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ fmtPct(dashboardStats.roi) }}</div>
        <div v-else class="h-7 mt-1 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
      </div>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <div v-for="i in 6" :key="i" class="h-20 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
        </div>
      </template>
      <template v-else>
        <div v-if="funnelStages.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <div v-for="s in funnelStages" :key="s.key" class="p-3 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ s.label }}</div>
            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ s.isMoney ? money(s.value) : s.value.toLocaleString() }}</div>
            <div v-if="s.rate !== null" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Step conv: {{ (s.rate as number).toFixed(1) }}%</div>
          </div>
        </div>
        <div v-else class="text-sm text-gray-500">No conversion funnel data</div>
      </template>
    </div>
  </div>
</template>
