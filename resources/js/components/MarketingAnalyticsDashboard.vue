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

    <!-- Conversion Funnel Raw -->
    <div class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow">
      <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Conversion Funnel (raw)</h4>
      <template v-if="$props.loading">
        <div class="h-24 rounded animate-pulse bg-gray-200 dark:bg-gray-700" />
      </template>
      <template v-else>
        <pre v-if="conversionFunnelData && Object.keys(conversionFunnelData).length" class="text-xs overflow-auto bg-gray-50 dark:bg-gray-900 p-3 rounded text-gray-800 dark:text-gray-200">{{ JSON.stringify(conversionFunnelData, null, 2) }}</pre>
        <div v-else class="text-sm text-gray-500">No conversion funnel data</div>
      </template>
    </div>
  </div>
</template>
