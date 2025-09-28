<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import KpiCard from '@/components/KpiCard.vue'
import { type BreadcrumbItemType as BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Reports', href: '/dashboard/reports/revenue-ar' },
  { title: 'Revenue & AR', href: '#' },
]

const page = usePage()
const initial = computed(() => (page.props as any).filters || {})

const preset = ref<string>(initial.value.preset || 'this_quarter')
const granularity = ref<string>(initial.value.granularity || 'quarter')
const date_from = ref<string>('')
const date_to = ref<string>('')

const loading = ref(false)
const rows = ref<any[]>([])

// Aggregated KPIs
const totalBilled = computed(() => rows.value.reduce((s, r) => s + (Number(r.total_billed) || 0), 0))
const totalReceived = computed(() => rows.value.reduce((s, r) => s + (Number(r.total_received) || 0), 0))
const arOutstanding = computed(() => rows.value.reduce((s, r) => s + (Number(r.ar_outstanding) || 0), 0))
const collectionRate = computed(() => totalBilled.value > 0 ? (totalReceived.value / totalBilled.value) * 100 : 0)

function buildQuery() {
  const params = new URLSearchParams()
  if (date_from.value) params.set('date_from', date_from.value)
  if (date_to.value) params.set('date_to', date_to.value)
  if (granularity.value) params.set('granularity', granularity.value)
  return params
}

async function fetchData() {
  loading.value = true
  try {
    const qs = buildQuery().toString()
    const res = await fetch(`/dashboard/reports/revenue-ar/data${qs ? `?${qs}` : ''}`)
    const json = await res.json()
    rows.value = json.data || []
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  preset.value = 'this_quarter'
  granularity.value = 'quarter'
  date_from.value = ''
  date_to.value = ''
  fetchData()
}

function exportCsv() {
  const qs = buildQuery()
  qs.set('type', 'csv')
  window.open(`/dashboard/reports/revenue-ar/export?${qs.toString()}`, '_blank')
}

function exportPdf() {
  const qs = buildQuery()
  qs.set('type', 'pdf')
  window.open(`/dashboard/reports/revenue-ar/export?${qs.toString()}`, '_blank')
}

onMounted(fetchData)
</script>

<template>
  <Head title="Revenue & AR Report" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4 md:p-6">
      <div>
        <h1 class="text-xl font-semibold">Revenue & Accounts Receivable</h1>
        <p class="text-sm text-muted-foreground">Quarterly / Half-Year / Yearly revenue, AR aging, collections.</p>
      </div>

      <!-- Filters -->
      <div class="rounded-xl border border-border bg-white p-4 dark:border-sidebar-border dark:bg-background">
        <div class="grid gap-3 md:grid-cols-5">
          <div>
            <label class="block text-sm font-medium mb-1">Preset</label>
            <select v-model="preset" class="w-full rounded-md border p-2 text-sm">
              <option value="this_quarter">This Quarter</option>
              <option value="last_quarter">Last Quarter</option>
              <option value="ytd">Year to Date (YTD)</option>
              <option value="last_year">Last Year</option>
              <option value="custom">Custom</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Granularity</label>
            <select v-model="granularity" class="w-full rounded-md border p-2 text-sm">
              <option value="quarter">Quarter</option>
              <option value="half">Half-Year</option>
              <option value="year">Year</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">From</label>
            <input v-model="date_from" type="date" class="w-full rounded-md border p-2 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">To</label>
            <input v-model="date_to" type="date" class="w-full rounded-md border p-2 text-sm" />
          </div>
          <div class="flex items-end gap-2">
            <button @click="fetchData" class="rounded-md bg-primary px-3 py-2 text-white text-sm">Apply</button>
            <button @click="resetFilters" class="rounded-md border px-3 py-2 text-sm">Reset</button>
          </div>
        </div>
      </div>

      <!-- KPI Cards with Loading Skeletons -->
      <div class="grid gap-4 md:grid-cols-3">
        <div v-if="loading" class="grid gap-4 md:grid-cols-3 md:col-span-3">
          <div class="rounded-xl border bg-white p-4 dark:border-sidebar-border dark:bg-background">
            <div class="h-4 w-32 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-7 w-24 mt-2 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
          </div>
          <div class="rounded-xl border bg-white p-4 dark:border-sidebar-border dark:bg-background">
            <div class="h-4 w-28 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-7 w-20 mt-2 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
          </div>
          <div class="rounded-xl border bg-white p-4 dark:border-sidebar-border dark:bg-background">
            <div class="h-4 w-40 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-7 w-28 mt-2 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
          </div>
        </div>
        <template v-else>
          <KpiCard title="Total Revenue" :value="totalBilled.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})" color="blue" />
          <KpiCard title="AR Balance" :value="arOutstanding.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})" color="orange" />
          <KpiCard title="Collections Rate" :value="collectionRate.toFixed(2) + '%'" color="green" />
        </template>
      </div>

      <!-- Chart/Table with Skeletons and Empty State -->
      <div class="rounded-xl border bg-white p-4 dark:border-sidebar-border dark:bg-background">
        <template v-if="loading">
          <div class="space-y-3">
            <div class="h-5 w-24 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
            <div class="h-40 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
          </div>
        </template>
        <template v-else>
          <div v-if="rows.length === 0" class="text-sm text-muted-foreground">No data available for the selected filters.</div>
          <div v-else class="text-sm text-muted-foreground">
            <div class="mb-2">Rows: {{ rows.length }}</div>
            <pre class="text-xs whitespace-pre-wrap max-h-56 overflow-auto">{{ rows.slice(0, 5) }}</pre>
          </div>
        </template>
      </div>

      <div class="rounded-xl border bg-white p-4 dark:border-sidebar-border dark:bg-background">
        <div class="mb-3 flex items-center gap-2">
          <button @click="exportCsv" class="rounded-md border px-3 py-2 text-sm">Export CSV</button>
          <button @click="exportPdf" class="rounded-md border px-3 py-2 text-sm">Print / PDF</button>
        </div>
        <div class="text-sm text-muted-foreground">AR Aging Table Placeholder</div>
      </div>
    </div>
  </AppLayout>
</template>
