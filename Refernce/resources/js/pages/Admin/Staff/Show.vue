<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <nav class="flex" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-4">
                <li>
                  <Link :href="route('admin.staff.index')" class="text-gray-400 hover:text-gray-500">
                    <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L9 5.414V17a1 1 0 102 0V5.414l5.293 5.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    <span class="sr-only">Home</span>
                  </Link>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <Link :href="route('admin.staff.index')" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                      Staff
                    </Link>
                  </div>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">{{ staff.full_name }}</span>
                  </div>
                </li>
              </ol>
            </nav>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ staff.full_name }}</h1>
            <p class="mt-1 text-sm text-gray-600">
              {{ staff.position }} • {{ staff.department }}
            </p>
          </div>
          <div class="flex space-x-3">
            <button
              @click="printProfile"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              Print Profile
            </button>
            <Link
              :href="route('admin.staff.edit', staff.id)"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Edit Profile
            </Link>
            <Link
              :href="route('admin.staff.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Back to Staff
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Profile Summary -->
        <div class="lg:col-span-1">
          <!-- Profile Card -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
              <div class="flex flex-col items-center">
                <div class="flex-shrink-0">
                  <img
                    v-if="staff.profile_photo_url"
                    :src="staff.profile_photo_url"
                    :alt="staff.full_name"
                    class="h-32 w-32 rounded-full object-cover"
                  />
                  <div
                    v-else
                    class="h-32 w-32 rounded-full bg-gray-300 flex items-center justify-center"
                  >
                    <span class="text-2xl font-medium text-gray-700">
                      {{ staff.first_name.charAt(0) }}{{ staff.last_name.charAt(0) }}
                    </span>
                  </div>
                </div>
                <div class="mt-4 text-center">
                  <h3 class="text-lg font-medium text-gray-900">{{ staff.full_name }}</h3>
                  <p class="text-sm text-gray-500">{{ staff.employee_id }}</p>
                  <div class="mt-2">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        getStatusColor(staff.status)
                      ]"
                    >
                      {{ staff.status_display }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
            </div>
            <div class="px-6 py-6 space-y-4">
              <div class="flex justify-between">
                <span class="text-sm text-gray-500">Years of Service</span>
                <span class="text-sm font-medium text-gray-900">{{ staff.years_of_service }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-500">Employment Type</span>
                <span class="text-sm font-medium text-gray-900">{{ staff.employment_type_display }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-500">Hire Date</span>
                <span class="text-sm font-medium text-gray-900">{{ formatDate(staff.hire_date) }}</span>
              </div>
              <div v-if="staff.salary" class="flex justify-between">
                <span class="text-sm text-gray-500">Salary</span>
                <span class="text-sm font-medium text-gray-900">{{ staff.formatted_salary }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Detailed Information -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Contact Information -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
            </div>
            <div class="px-6 py-6">
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <a :href="`mailto:${staff.email}`" class="text-indigo-600 hover:text-indigo-500">
                      {{ staff.email }}
                    </a>
                  </dd>
                </div>
                <div v-if="staff.phone_number">
                  <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <a :href="`tel:${staff.phone_number}`" class="text-indigo-600 hover:text-indigo-500">
                      {{ staff.phone_number }}
                    </a>
                  </dd>
                </div>
                <div v-if="staff.address" class="md:col-span-2">
                  <dt class="text-sm font-medium text-gray-500">Address</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ staff.address }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Employment Details -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Employment Details</h3>
            </div>
            <div class="px-6 py-6">
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Position</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ staff.position }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Department</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ staff.department }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Employment Type</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ staff.employment_type_display }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Status</dt>
                  <dd class="mt-1">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        getStatusColor(staff.status)
                      ]"
                    >
                      {{ staff.status_display }}
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Hire Date</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(staff.hire_date) }}</dd>
                </div>
                <div v-if="staff.salary">
                  <dt class="text-sm font-medium text-gray-500">Salary</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ staff.formatted_salary }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Emergency Contact -->
          <div v-if="staff.emergency_contact_name || staff.emergency_contact_phone" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Emergency Contact</h3>
            </div>
            <div class="px-6 py-6">
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div v-if="staff.emergency_contact_name">
                  <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ staff.emergency_contact_name }}</dd>
                </div>
                <div v-if="staff.emergency_contact_phone">
                  <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <a :href="`tel:${staff.emergency_contact_phone}`" class="text-indigo-600 hover:text-indigo-500">
                      {{ staff.emergency_contact_phone }}
                    </a>
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- User Account -->
          <div v-if="staff.user" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">User Account</h3>
            </div>
            <div class="px-6 py-6">
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                  <dd class="mt-1">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        staff.user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ staff.user.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ staff.user.last_login_at ? formatDateTime(staff.user.last_login_at) : 'Never' }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Account Created</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(staff.user.created_at) }}</dd>
                </div>
                <div v-if="staff.user.roles && staff.user.roles.length">
                  <dt class="text-sm font-medium text-gray-500">Roles</dt>
                  <dd class="mt-1">
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="role in staff.user.roles"
                        :key="role.id"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"
                      >
                        {{ role.display_name }}
                      </span>
                    </div>
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Additional Notes -->
          <div v-if="staff.notes" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Additional Notes</h3>
            </div>
            <div class="px-6 py-6">
              <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ staff.notes }}</p>
            </div>
          </div>

          <!-- Record Information -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Record Information</h3>
            </div>
            <div class="px-6 py-6">
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Created</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(staff.created_at) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(staff.updated_at) }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Props
const props = defineProps({
  staff: Object,
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
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (datetime) => {
  if (!datetime) return ''
  return new Date(datetime).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const printProfile = () => {
  window.print()
}
</script>

<style>
@media print {
  .no-print {
    display: none !important;
  }
  
  .print-break {
    page-break-before: always;
  }
}
</style>
