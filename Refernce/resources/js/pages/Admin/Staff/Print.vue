<template>
  <div class="min-h-screen bg-white">
    <!-- Print Header -->
    <div class="print-header mb-8">
      <div class="text-center border-b-2 border-gray-300 pb-4">
        <h1 class="text-3xl font-bold text-gray-900">Staff Directory</h1>
        <p class="text-lg text-gray-600 mt-2">Complete Staff Listing</p>
        <p class="text-sm text-gray-500 mt-1">Generated on {{ printDate }}</p>
      </div>
    </div>

    <!-- Filters Applied -->
    <div v-if="hasFilters" class="mb-6 p-4 bg-gray-50 rounded-lg">
      <h3 class="text-lg font-medium text-gray-900 mb-2">Applied Filters:</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div v-if="filters.search">
          <span class="font-medium">Search:</span> {{ filters.search }}
        </div>
        <div v-if="filters.department">
          <span class="font-medium">Department:</span> {{ filters.department }}
        </div>
        <div v-if="filters.status">
          <span class="font-medium">Status:</span> {{ filters.status }}
        </div>
        <div v-if="filters.employment_type">
          <span class="font-medium">Employment Type:</span> {{ filters.employment_type }}
        </div>
      </div>
    </div>

    <!-- Statistics Summary -->
    <div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="text-center p-4 border border-gray-200 rounded">
        <div class="text-2xl font-bold text-gray-900">{{ statistics.total_staff }}</div>
        <div class="text-sm text-gray-600">Total Staff</div>
      </div>
      <div class="text-center p-4 border border-gray-200 rounded">
        <div class="text-2xl font-bold text-green-600">{{ statistics.active_staff }}</div>
        <div class="text-sm text-gray-600">Active Staff</div>
      </div>
      <div class="text-center p-4 border border-gray-200 rounded">
        <div class="text-2xl font-bold text-blue-600">{{ Object.keys(statistics.by_department || {}).length }}</div>
        <div class="text-sm text-gray-600">Departments</div>
      </div>
      <div class="text-center p-4 border border-gray-200 rounded">
        <div class="text-2xl font-bold text-yellow-600">{{ statistics.recent_hires }}</div>
        <div class="text-sm text-gray-600">Recent Hires</div>
      </div>
    </div>

    <!-- Staff List -->
    <div class="space-y-6">
      <!-- Department Groups -->
      <div v-for="(departmentStaff, department) in groupedStaff" :key="department" class="print-section">
        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-300 pb-2">
          {{ department }} Department ({{ departmentStaff.length }})
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="member in departmentStaff"
            :key="member.id"
            class="border border-gray-200 rounded-lg p-4 print-card"
          >
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <div
                  v-if="!member.profile_photo_url"
                  class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center"
                >
                  <span class="text-sm font-medium text-gray-700">
                    {{ member.first_name.charAt(0) }}{{ member.last_name.charAt(0) }}
                  </span>
                </div>
                <img
                  v-else
                  :src="member.profile_photo_url"
                  :alt="member.full_name"
                  class="h-12 w-12 rounded-full object-cover"
                />
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-medium text-gray-900 truncate">
                    {{ member.full_name }}
                  </h3>
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      getStatusColor(member.status)
                    ]"
                  >
                    {{ member.status_display }}
                  </span>
                </div>
                <p class="text-sm text-gray-600">{{ member.position }}</p>
                <p class="text-sm text-gray-500">{{ member.employee_id }}</p>
                
                <div class="mt-2 space-y-1">
                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ member.email }}
                  </div>
                  <div v-if="member.phone_number" class="flex items-center text-sm text-gray-600">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ member.phone_number }}
                  </div>
                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-1 12a2 2 0 002 2h6a2 2 0 002-2L16 7"/>
                    </svg>
                    {{ member.employment_type_display }}
                  </div>
                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-1 12a2 2 0 002 2h6a2 2 0 002-2L16 7"/>
                    </svg>
                    Hired: {{ formatDate(member.hire_date) }}
                  </div>
                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Service: {{ member.years_of_service }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Department Summary -->
    <div class="mt-8 print-section">
      <h2 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-300 pb-2">
        Department Summary
      </h2>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div
          v-for="(count, department) in statistics.by_department"
          :key="department"
          class="text-center p-3 border border-gray-200 rounded"
        >
          <div class="text-lg font-bold text-gray-900">{{ count }}</div>
          <div class="text-sm text-gray-600">{{ department }}</div>
        </div>
      </div>
    </div>

    <!-- Employment Type Summary -->
    <div class="mt-6 print-section">
      <h2 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-300 pb-2">
        Employment Type Summary
      </h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div
          v-for="(count, type) in statistics.by_employment_type"
          :key="type"
          class="text-center p-3 border border-gray-200 rounded"
        >
          <div class="text-lg font-bold text-gray-900">{{ count }}</div>
          <div class="text-sm text-gray-600">{{ formatEmploymentType(type) }}</div>
        </div>
      </div>
    </div>

    <!-- Print Footer -->
    <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-500">
      <p>This report was generated on {{ printDate }}</p>
      <p>Total {{ staff.length }} staff members listed</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  staff: Array,
  statistics: Object,
  filters: Object,
  printDate: String,
})

// Computed
const hasFilters = computed(() => {
  return Object.values(props.filters).some(value => value && value !== '')
})

const groupedStaff = computed(() => {
  const groups = {}
  props.staff.forEach(member => {
    const department = member.department || 'Unassigned'
    if (!groups[department]) {
      groups[department] = []
    }
    groups[department].push(member)
  })
  
  // Sort departments alphabetically and staff within each department
  const sortedGroups = {}
  Object.keys(groups).sort().forEach(department => {
    sortedGroups[department] = groups[department].sort((a, b) => 
      a.full_name.localeCompare(b.full_name)
    )
  })
  
  return sortedGroups
})

// Methods
const getStatusColor = (status) => {
  const colors = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800',
    terminated: 'bg-red-100 text-red-800',
    'on-leave': 'bg-yellow-100 text-yellow-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatEmploymentType = (type) => {
  const types = {
    'full-time': 'Full Time',
    'part-time': 'Part Time',
    'contract': 'Contract',
    'intern': 'Intern',
  }
  return types[type] || type
}

// Auto-print when component mounts
import { onMounted } from 'vue'

onMounted(() => {
  // Small delay to ensure content is rendered
  setTimeout(() => {
    window.print()
  }, 500)
})
</script>

<style>
@media print {
  body {
    font-size: 12px;
  }
  
  .print-section {
    page-break-inside: avoid;
  }
  
  .print-card {
    page-break-inside: avoid;
    margin-bottom: 0.5rem;
  }
  
  .print-header {
    page-break-after: avoid;
  }
  
  /* Ensure colors print correctly */
  .bg-green-100 { background-color: #dcfce7 !important; }
  .bg-red-100 { background-color: #fee2e2 !important; }
  .bg-yellow-100 { background-color: #fef3c7 !important; }
  .bg-gray-100 { background-color: #f3f4f6 !important; }
  
  .text-green-800 { color: #166534 !important; }
  .text-red-800 { color: #991b1b !important; }
  .text-yellow-800 { color: #92400e !important; }
  .text-gray-800 { color: #1f2937 !important; }
}

@page {
  margin: 1in;
  size: letter;
}
</style>
