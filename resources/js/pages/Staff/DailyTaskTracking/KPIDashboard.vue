<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { 
  BarChart, 
  TrendingUp, 
  Clock, 
  CheckCircle, 
  Users, 
  Filter,
  Calendar,
  Building
} from 'lucide-vue-next'

interface StaffKPI {
  staff: {
    id: number
    name: string
    position: string
    department: string
  }
  totalTasks: number
  completedTasks: number
  inProgressTasks: number
  completionRate: number
  avgDurationMinutes: number
  onTimeRate: number
}

const props = defineProps<{
  kpiData: StaffKPI[]
  dateRange: string
  department: string | null
  departments: string[]
  startDate: string
  endDate: string
}>()

const dateRange = ref(props.dateRange)
const departmentFilter = ref(props.department || '')

// Format time in minutes to HH:MM
const formatTime = (minutes: number) => {
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`
}

// Calculate overall statistics
const overallStats = computed(() => {
  const totalTasks = props.kpiData.reduce((sum, kpi) => sum + kpi.totalTasks, 0)
  const completedTasks = props.kpiData.reduce((sum, kpi) => sum + kpi.completedTasks, 0)
  const avgCompletionRate = props.kpiData.length > 0 
    ? props.kpiData.reduce((sum, kpi) => sum + kpi.completionRate, 0) / props.kpiData.length 
    : 0
  const avgDuration = props.kpiData.length > 0 
    ? props.kpiData.reduce((sum, kpi) => sum + kpi.avgDurationMinutes, 0) / props.kpiData.length 
    : 0
  const avgOnTimeRate = props.kpiData.length > 0 
    ? props.kpiData.reduce((sum, kpi) => sum + kpi.onTimeRate, 0) / props.kpiData.length 
    : 0
    
  return {
    totalTasks,
    completedTasks,
    avgCompletionRate: Math.round(avgCompletionRate * 10) / 10,
    avgDuration: Math.round(avgDuration),
    avgOnTimeRate: Math.round(avgOnTimeRate * 10) / 10
  }
})

// Update filters
const updateFilters = () => {
  router.get(route('staff.kpi-dashboard'), {
    range: dateRange.value,
    department: departmentFilter.value || undefined
  })
}

// Reset filters
const resetFilters = () => {
  dateRange.value = 'week'
  departmentFilter.value = ''
  updateFilters()
}

// Sort KPI data by completion rate (descending)
const sortedKpiData = computed(() => {
  return [...props.kpiData].sort((a, b) => b.completionRate - a.completionRate)
})

// Get top performers
const topPerformers = computed(() => {
  return sortedKpiData.value.slice(0, 5)
})

// Get departments for filter
const departmentOptions = computed(() => {
  return props.departments.filter(dept => dept !== null)
})
</script>

<template>
  <Head title="KPI Dashboard" />

  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Performance KPI Dashboard</h1>
          <p class="text-gray-600">Track and analyze staff performance metrics</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-600">
          <Calendar class="w-4 h-4" />
          {{ startDate }} to {{ endDate }}
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
          <div class="flex items-center gap-2">
            <Filter class="w-5 h-5 text-gray-500" />
            <span class="text-sm font-medium text-gray-700">Filters:</span>
          </div>
          
          <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="flex-1">
              <label class="block text-xs text-gray-500 mb-1">Date Range</label>
              <select 
                v-model="dateRange"
                @change="updateFilters"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
              >
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="quarter">This Quarter</option>
              </select>
            </div>
            
            <div class="flex-1">
              <label class="block text-xs text-gray-500 mb-1">Department</label>
              <select 
                v-model="departmentFilter"
                @change="updateFilters"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
              >
                <option value="">All Departments</option>
                <option 
                  v-for="dept in departmentOptions" 
                  :key="dept" 
                  :value="dept"
                >
                  {{ dept }}
                </option>
              </select>
            </div>
          </div>
          
          <button 
            @click="resetFilters"
            class="px-4 py-2 text-sm text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Overall Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-5 shadow-sm border border-cyan-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-cyan-700">Total Tasks</p>
              <p class="text-2xl font-bold text-cyan-900">{{ overallStats.totalTasks }}</p>
            </div>
            <BarChart class="w-8 h-8 text-cyan-500" />
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 shadow-sm border border-green-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-green-700">Completed</p>
              <p class="text-2xl font-bold text-green-900">{{ overallStats.completedTasks }}</p>
            </div>
            <CheckCircle class="w-8 h-8 text-green-500" />
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 shadow-sm border border-blue-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-blue-700">Avg. Completion</p>
              <p class="text-2xl font-bold text-blue-900">{{ overallStats.avgCompletionRate }}%</p>
            </div>
            <TrendingUp class="w-8 h-8 text-blue-500" />
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 shadow-sm border border-purple-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-purple-700">Avg. Duration</p>
              <p class="text-2xl font-bold text-purple-900">{{ formatTime(overallStats.avgDuration) }}</p>
            </div>
            <Clock class="w-8 h-8 text-purple-500" />
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5 shadow-sm border border-orange-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-orange-700">On-Time Rate</p>
              <p class="text-2xl font-bold text-orange-900">{{ overallStats.avgOnTimeRate }}%</p>
            </div>
            <TrendingUp class="w-8 h-8 text-orange-500" />
          </div>
        </div>
      </div>

      <!-- Top Performers -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 p-5">
          <h2 class="text-lg font-semibold text-gray-900">Top Performers</h2>
        </div>
        <div class="divide-y divide-gray-100">
          <div 
            v-for="(kpi, index) in topPerformers" 
            :key="kpi.staff.id"
            class="p-5 hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-cyan-100 text-cyan-800 font-medium">
                  {{ index + 1 }}
                </div>
                <div>
                  <h3 class="font-medium text-gray-900">{{ kpi.staff.name }}</h3>
                  <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span>{{ kpi.staff.position }}</span>
                    <span>•</span>
                    <div class="flex items-center gap-1">
                      <Building class="w-4 h-4" />
                      {{ kpi.staff.department }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-6">
                <div class="text-center">
                  <p class="text-sm text-gray-500">Completion</p>
                  <p class="font-semibold text-gray-900">{{ kpi.completionRate }}%</p>
                </div>
                <div class="text-center">
                  <p class="text-sm text-gray-500">Tasks</p>
                  <p class="font-semibold text-gray-900">{{ kpi.completedTasks }}/{{ kpi.totalTasks }}</p>
                </div>
                <div class="text-center">
                  <p class="text-sm text-gray-500">On-Time</p>
                  <p class="font-semibold text-gray-900">{{ kpi.onTimeRate }}%</p>
                </div>
              </div>
            </div>
          </div>
          <div v-if="topPerformers.length === 0" class="p-8 text-center text-gray-500">
            No performance data available.
          </div>
        </div>
      </div>

      <!-- Full KPI Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 p-5 flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">Staff Performance Details</h2>
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <Users class="w-4 h-4" />
            {{ kpiData.length }} staff members
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500">
              <tr>
                <th class="px-6 py-3 font-medium">Staff Member</th>
                <th class="px-6 py-3 font-medium">Position</th>
                <th class="px-6 py-3 font-medium">Department</th>
                <th class="px-6 py-3 font-medium text-center">Tasks</th>
                <th class="px-6 py-3 font-medium text-center">Completed</th>
                <th class="px-6 py-3 font-medium text-center">In Progress</th>
                <th class="px-6 py-3 font-medium text-center">Completion Rate</th>
                <th class="px-6 py-3 font-medium text-center">Avg. Duration</th>
                <th class="px-6 py-3 font-medium text-center">On-Time Rate</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr 
                v-for="kpi in sortedKpiData" 
                :key="kpi.staff.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 font-medium text-gray-900">{{ kpi.staff.name }}</td>
                <td class="px-6 py-4 text-gray-700">{{ kpi.staff.position }}</td>
                <td class="px-6 py-4 text-gray-700">
                  <div class="flex items-center gap-1">
                    <Building class="w-4 h-4 text-gray-500" />
                    {{ kpi.staff.department }}
                  </div>
                </td>
                <td class="px-6 py-4 text-center text-gray-700">{{ kpi.totalTasks }}</td>
                <td class="px-6 py-4 text-center text-gray-700">{{ kpi.completedTasks }}</td>
                <td class="px-6 py-4 text-center text-gray-700">{{ kpi.inProgressTasks }}</td>
                <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <span class="font-medium">{{ kpi.completionRate }}%</span>
                    <div class="w-16 bg-gray-200 rounded-full h-2">
                      <div 
                        class="bg-cyan-600 h-2 rounded-full" 
                        :style="{ width: kpi.completionRate + '%' }"
                      ></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-center text-gray-700">{{ formatTime(kpi.avgDurationMinutes) }}</td>
                <td class="px-6 py-4 text-center">
                  <span 
                    class="px-2 py-1 text-xs rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': kpi.onTimeRate >= 90,
                      'bg-yellow-100 text-yellow-800': kpi.onTimeRate >= 75 && kpi.onTimeRate < 90,
                      'bg-red-100 text-red-800': kpi.onTimeRate < 75
                    }"
                  >
                    {{ kpi.onTimeRate }}%
                  </span>
                </td>
              </tr>
              <tr v-if="sortedKpiData.length === 0">
                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                  No performance data available for the selected filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>