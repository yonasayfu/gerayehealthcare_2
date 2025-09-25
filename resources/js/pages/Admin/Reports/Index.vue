<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import type { BreadcrumbItemType } from '@/types'

type ReportDef = {
  key: string
  title: string
  description: string
  export_route: string
  filters: string[]
}

const props = defineProps<{ reports: ReportDef[]; defaults: { granularity: string } }>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Reports', href: route('admin.reports.index') },
]

const dateFrom = ref<string>('')
const dateTo = ref<string>('')
const granularity = ref<string>(props.defaults?.granularity ?? 'quarter')
const platform = ref<string>('')
const serviceCategory = ref<string>('')
const isEventService = ref<string>('')

const baseFilters = computed(() => ({
  date_from: dateFrom.value || undefined,
  date_to: dateTo.value || undefined,
  granularity: granularity.value || undefined,
}))

function buildQueryFor(report: ReportDef) {
  const q: Record<string, any> = { ...baseFilters.value }
  if (report.key === 'marketing-roi') {
    if (platform.value) q.platform = platform.value
  }
  if (report.key === 'service-volume') {
    if (serviceCategory.value) q.service_category = serviceCategory.value
    if (isEventService.value !== '') q.is_event_service = isEventService.value === '1'
  }
  return q
}

function exportReport(report: ReportDef, type: 'csv' | 'pdf') {
  const q = buildQueryFor(report)
  q.type = type
  // @ts-ignore - Ziggy route names are available at runtime
  const url = route(report.export_route, q)
  window.open(url, '_blank')
}
</script>

<template>
  <Head title="Reports" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content p-4 flex flex-col gap-3">
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Reports</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">Generate CSV/PDF with unified filters</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
              <label class="text-xs">Date From</label>
              <input v-model="dateFrom" type="date" class="mt-1 w-full form-input" />
            </div>
            <div>
              <label class="text-xs">Date To</label>
              <input v-model="dateTo" type="date" class="mt-1 w-full form-input" />
            </div>
            <div>
              <label class="text-xs">Granularity</label>
              <select v-model="granularity" class="mt-1 w-full form-input">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
                <option value="quarter">Quarter</option>
                <option value="year">Year</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label class="text-xs">Platform (Marketing ROI)</label>
              <input v-model="platform" type="text" placeholder="e.g. Facebook" class="mt-1 w-full form-input" />
            </div>
            <div>
              <label class="text-xs">Service Category (Service Volume)</label>
              <input v-model="serviceCategory" type="text" placeholder="e.g. Nursing" class="mt-1 w-full form-input" />
            </div>
            <div>
              <label class="text-xs">Is Event Service (Service Volume)</label>
              <select v-model="isEventService" class="mt-1 w-full form-input">
                <option value="">Any</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="r in props.reports" :key="r.key" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-4 flex flex-col justify-between">
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ r.title }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ r.description }}</p>
          </div>
          <div class="mt-4 flex gap-2">
            <button class="btn-glass btn-glass-sm" @click="exportReport(r, 'csv')">Export CSV</button>
            <button class="btn-glass btn-glass-sm" @click="exportReport(r, 'pdf')">Export PDF</button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.form-input {
  border-radius: 0.375rem;
  background-color: white;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  color: #111827;
  border: 1px solid #d1d5db;
}

.dark .form-input {
  background-color: #1f2937;
  color: white;
  border-color: #374151;
}
</style>

