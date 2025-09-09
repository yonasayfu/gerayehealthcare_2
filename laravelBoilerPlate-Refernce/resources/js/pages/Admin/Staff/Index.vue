<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Staff Management</h1>
            <p class="mt-1 text-sm text-gray-600">
              Manage your organization's staff members
            </p>
          </div>
          <div class="flex space-x-3">
            <button
              @click="exportData"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Export CSV
            </button>
            <button
              @click="printData"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Print
            </button>
            <Link
              :href="route('admin.staff.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Add Staff
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Staff</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ statistics.total_staff }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Active Staff</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ statistics.active_staff }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Departments</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ Object.keys(statistics.by_department || {}).length }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Recent Hires</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ statistics.recent_hires }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Filters</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
              <input
                v-model="searchForm.search"
                type="text"
                placeholder="Search staff..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @input="debouncedSearch"
              />
            </div>

            <!-- Department Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
              <select
                v-model="searchForm.department"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="applyFilters"
              >
                <option value="">All Departments</option>
                <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
              </select>
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="searchForm.status"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="applyFilters"
              >
                <option value="">All Status</option>
                <option v-for="(label, value) in statusTypes" :key="value" :value="value">{{ label }}</option>
              </select>
            </div>

            <!-- Employment Type Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
              <select
                v-model="searchForm.employment_type"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="applyFilters"
              >
                <option value="">All Types</option>
                <option v-for="(label, value) in employmentTypes" :key="value" :value="value">{{ label }}</option>
              </select>
            </div>

            <!-- Per Page -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
              <select
                v-model="searchForm.per_page"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="applyFilters"
              >
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
          </div>

          <div class="mt-4 flex justify-between">
            <button
              @click="clearFilters"
              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Staff Table -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Staff Members</h3>
          <div class="text-sm text-gray-500">
            Showing {{ staff.from || 0 }} to {{ staff.to || 0 }} of {{ staff.total || 0 }} results
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  v-for="column in columns"
                  :key="column.key"
                  @click="column.sortable ? sort(column.key) : null"
                  :class="[
                    'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                    column.sortable ? 'cursor-pointer hover:bg-gray-100' : ''
                  ]"
                >
                  <div class="flex items-center space-x-1">
                    <span>{{ column.label }}</span>
                    <svg
                      v-if="column.sortable && searchForm.sort_by === column.key"
                      :class="[
                        'h-4 w-4',
                        searchForm.sort_order === 'asc' ? 'transform rotate-180' : ''
                      ]"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="member in staff.data" :key="member.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img
                        v-if="member.profile_photo_url"
                        :src="member.profile_photo_url"
                        :alt="member.full_name"
                        class="h-10 w-10 rounded-full object-cover"
                      />
                      <div
                        v-else
                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center"
                      >
                        <span class="text-sm font-medium text-gray-700">
                          {{ member.first_name.charAt(0) }}{{ member.last_name.charAt(0) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ member.full_name }}</div>
                      <div class="text-sm text-gray-500">{{ member.employee_id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ member.email }}</div>
                  <div class="text-sm text-gray-500">{{ member.phone_number }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ member.position }}</div>
                  <div class="text-sm text-gray-500">{{ member.department }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      getStatusColor(member.status)
                    ]"
                  >
                    {{ member.status_display }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ member.employment_type_display }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(member.hire_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ member.years_of_service }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="route('admin.staff.show', member.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      View
                    </Link>
                    <Link
                      :href="route('admin.staff.edit', member.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Edit
                    </Link>
                    <button
                      @click="confirmDelete(member)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="staff.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="staff.prev_page_url"
                :href="staff.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link
                v-if="staff.next_page_url"
                :href="staff.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing <span class="font-medium">{{ staff.from }}</span> to <span class="font-medium">{{ staff.to }}</span> of <span class="font-medium">{{ staff.total }}</span> results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <Link
                    v-if="staff.prev_page_url"
                    :href="staff.prev_page_url"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </Link>
                  
                  <template v-for="page in getPageNumbers()" :key="page">
                    <Link
                      v-if="page !== '...'"
                      :href="getPageUrl(page)"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                        page === staff.current_page
                          ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                      ]"
                    >
                      {{ page }}
                    </Link>
                    <span
                      v-else
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                    >
                      ...
                    </span>
                  </template>

                  <Link
                    v-if="staff.next_page_url"
                    :href="staff.next_page_url"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  >
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </Link>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Staff Member</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete <strong>{{ staffToDelete?.full_name }}</strong>? This action cannot be undone.
            </p>
          </div>
          <div class="items-center px-4 py-3">
            <button
              @click="deleteStaff"
              class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
            >
              Delete
            </button>
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { debounce } from 'lodash'

// Props
const props = defineProps({
  staff: Object,
  statistics: Object,
  departments: Array,
  positions: Array,
  filters: Object,
  employmentTypes: Object,
  statusTypes: Object,
})

// Reactive data
const searchForm = reactive({
  search: props.filters.search || '',
  department: props.filters.department || '',
  status: props.filters.status || '',
  employment_type: props.filters.employment_type || '',
  sort_by: props.filters.sort_by || 'first_name',
  sort_order: props.filters.sort_order || 'asc',
  per_page: props.filters.per_page || 15,
})

const showDeleteModal = ref(false)
const staffToDelete = ref(null)

// Table columns
const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Contact', sortable: true },
  { key: 'position', label: 'Position', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'employment_type', label: 'Type', sortable: true },
  { key: 'hire_date', label: 'Hire Date', sortable: true },
  { key: 'years_of_service', label: 'Service', sortable: false },
  { key: 'actions', label: 'Actions', sortable: false },
]

// Methods
const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('admin.staff.index'), searchForm, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  Object.keys(searchForm).forEach(key => {
    if (key === 'sort_by') {
      searchForm[key] = 'first_name'
    } else if (key === 'sort_order') {
      searchForm[key] = 'asc'
    } else if (key === 'per_page') {
      searchForm[key] = 15
    } else {
      searchForm[key] = ''
    }
  })
  applyFilters()
}

const sort = (column) => {
  if (searchForm.sort_by === column) {
    searchForm.sort_order = searchForm.sort_order === 'asc' ? 'desc' : 'asc'
  } else {
    searchForm.sort_by = column
    searchForm.sort_order = 'asc'
  }
  applyFilters()
}

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
  return new Date(date).toLocaleDateString()
}

const confirmDelete = (staff) => {
  staffToDelete.value = staff
  showDeleteModal.value = true
}

const deleteStaff = () => {
  if (staffToDelete.value) {
    router.delete(route('admin.staff.destroy', staffToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        staffToDelete.value = null
      }
    })
  }
}

const exportData = () => {
  const params = new URLSearchParams(searchForm).toString()
  window.location.href = route('admin.staff.export') + '?' + params
}

const printData = () => {
  const params = new URLSearchParams(searchForm).toString()
  window.open(route('admin.staff.print') + '?' + params, '_blank')
}

const getPageNumbers = () => {
  const current = props.staff.current_page
  const last = props.staff.last_page
  const delta = 2
  const range = []
  const rangeWithDots = []

  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i)
  }

  if (current - delta > 2) {
    rangeWithDots.push(1, '...')
  } else {
    rangeWithDots.push(1)
  }

  rangeWithDots.push(...range)

  if (current + delta < last - 1) {
    rangeWithDots.push('...', last)
  } else {
    rangeWithDots.push(last)
  }

  return rangeWithDots.filter((item, index, arr) => arr.indexOf(item) === index)
}

const getPageUrl = (page) => {
  const url = new URL(window.location.href)
  url.searchParams.set('page', page)
  return url.toString()
}
</script>
