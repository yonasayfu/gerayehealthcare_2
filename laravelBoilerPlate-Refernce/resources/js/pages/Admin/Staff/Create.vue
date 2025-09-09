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
                    <span class="ml-4 text-sm font-medium text-gray-500">Create</span>
                  </div>
                </li>
              </ol>
            </nav>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Add New Staff Member</h1>
            <p class="mt-1 text-sm text-gray-600">
              Create a new staff member profile with all necessary information
            </p>
          </div>
          <div class="flex space-x-3">
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

    <!-- Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="submit" class="space-y-8">
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
            <p class="mt-1 text-sm text-gray-600">Personal details and contact information</p>
          </div>
          <div class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Employee ID -->
              <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700">
                  Employee ID <span class="text-red-500">*</span>
                </label>
                <input
                  id="employee_id"
                  v-model="form.employee_id"
                  type="text"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.employee_id ? 'border-red-300' : ''
                  ]"
                  placeholder="Auto-generated if empty"
                />
                <p v-if="errors.employee_id" class="mt-1 text-sm text-red-600">{{ errors.employee_id }}</p>
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                  Email Address <span class="text-red-500">*</span>
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.email ? 'border-red-300' : ''
                  ]"
                  placeholder="john.doe@company.com"
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- First Name -->
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">
                  First Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="first_name"
                  v-model="form.first_name"
                  type="text"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.first_name ? 'border-red-300' : ''
                  ]"
                  placeholder="John"
                />
                <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}</p>
              </div>

              <!-- Last Name -->
              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">
                  Last Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="last_name"
                  v-model="form.last_name"
                  type="text"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.last_name ? 'border-red-300' : ''
                  ]"
                  placeholder="Doe"
                />
                <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Phone Number -->
              <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">
                  Phone Number
                </label>
                <input
                  id="phone_number"
                  v-model="form.phone_number"
                  type="tel"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.phone_number ? 'border-red-300' : ''
                  ]"
                  placeholder="+1-555-123-4567"
                />
                <p v-if="errors.phone_number" class="mt-1 text-sm text-red-600">{{ errors.phone_number }}</p>
              </div>

              <!-- Hire Date -->
              <div>
                <label for="hire_date" class="block text-sm font-medium text-gray-700">
                  Hire Date <span class="text-red-500">*</span>
                </label>
                <input
                  id="hire_date"
                  v-model="form.hire_date"
                  type="date"
                  :max="today"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.hire_date ? 'border-red-300' : ''
                  ]"
                />
                <p v-if="errors.hire_date" class="mt-1 text-sm text-red-600">{{ errors.hire_date }}</p>
              </div>
            </div>

            <!-- Address -->
            <div>
              <label for="address" class="block text-sm font-medium text-gray-700">
                Address
              </label>
              <textarea
                id="address"
                v-model="form.address"
                rows="3"
                :class="[
                  'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                  errors.address ? 'border-red-300' : ''
                ]"
                placeholder="123 Main Street, City, State, ZIP"
              ></textarea>
              <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
            </div>
          </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Employment Information</h3>
            <p class="mt-1 text-sm text-gray-600">Job position and employment details</p>
          </div>
          <div class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Position -->
              <div>
                <label for="position" class="block text-sm font-medium text-gray-700">
                  Position <span class="text-red-500">*</span>
                </label>
                <input
                  id="position"
                  v-model="form.position"
                  type="text"
                  list="positions-list"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.position ? 'border-red-300' : ''
                  ]"
                  placeholder="Software Engineer"
                />
                <datalist id="positions-list">
                  <option v-for="position in positions" :key="position" :value="position" />
                </datalist>
                <p v-if="errors.position" class="mt-1 text-sm text-red-600">{{ errors.position }}</p>
              </div>

              <!-- Department -->
              <div>
                <label for="department" class="block text-sm font-medium text-gray-700">
                  Department <span class="text-red-500">*</span>
                </label>
                <input
                  id="department"
                  v-model="form.department"
                  type="text"
                  list="departments-list"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.department ? 'border-red-300' : ''
                  ]"
                  placeholder="Engineering"
                />
                <datalist id="departments-list">
                  <option v-for="dept in departments" :key="dept" :value="dept" />
                </datalist>
                <p v-if="errors.department" class="mt-1 text-sm text-red-600">{{ errors.department }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Employment Type -->
              <div>
                <label for="employment_type" class="block text-sm font-medium text-gray-700">
                  Employment Type <span class="text-red-500">*</span>
                </label>
                <select
                  id="employment_type"
                  v-model="form.employment_type"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.employment_type ? 'border-red-300' : ''
                  ]"
                >
                  <option value="">Select Type</option>
                  <option v-for="(label, value) in employmentTypes" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
                <p v-if="errors.employment_type" class="mt-1 text-sm text-red-600">{{ errors.employment_type }}</p>
              </div>

              <!-- Status -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700">
                  Status <span class="text-red-500">*</span>
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.status ? 'border-red-300' : ''
                  ]"
                >
                  <option value="">Select Status</option>
                  <option v-for="(label, value) in statusTypes" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
                <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
              </div>

              <!-- Salary -->
              <div>
                <label for="salary" class="block text-sm font-medium text-gray-700">
                  Salary
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                  </div>
                  <input
                    id="salary"
                    v-model="form.salary"
                    type="number"
                    step="0.01"
                    min="0"
                    :class="[
                      'block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                      errors.salary ? 'border-red-300' : ''
                    ]"
                    placeholder="50000.00"
                  />
                </div>
                <p v-if="errors.salary" class="mt-1 text-sm text-red-600">{{ errors.salary }}</p>
              </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
              <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="is_active" class="ml-2 block text-sm text-gray-900">
                Active Employee
              </label>
            </div>
          </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Emergency Contact</h3>
            <p class="mt-1 text-sm text-gray-600">Emergency contact information</p>
          </div>
          <div class="px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Emergency Contact Name -->
              <div>
                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">
                  Contact Name
                </label>
                <input
                  id="emergency_contact_name"
                  v-model="form.emergency_contact_name"
                  type="text"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.emergency_contact_name ? 'border-red-300' : ''
                  ]"
                  placeholder="Jane Doe"
                />
                <p v-if="errors.emergency_contact_name" class="mt-1 text-sm text-red-600">{{ errors.emergency_contact_name }}</p>
              </div>

              <!-- Emergency Contact Phone -->
              <div>
                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">
                  Contact Phone
                </label>
                <input
                  id="emergency_contact_phone"
                  v-model="form.emergency_contact_phone"
                  type="tel"
                  :class="[
                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    errors.emergency_contact_phone ? 'border-red-300' : ''
                  ]"
                  placeholder="+1-555-987-6543"
                />
                <p v-if="errors.emergency_contact_phone" class="mt-1 text-sm text-red-600">{{ errors.emergency_contact_phone }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Additional Notes</h3>
            <p class="mt-1 text-sm text-gray-600">Any additional information or notes</p>
          </div>
          <div class="px-6 py-6">
            <div>
              <label for="notes" class="block text-sm font-medium text-gray-700">
                Notes
              </label>
              <textarea
                id="notes"
                v-model="form.notes"
                rows="4"
                :class="[
                  'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                  errors.notes ? 'border-red-300' : ''
                ]"
                placeholder="Any additional notes about the staff member..."
              ></textarea>
              <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</p>
              <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
          <Link
            :href="route('admin.staff.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="processing"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ processing ? 'Creating...' : 'Create Staff Member' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

// Props
const props = defineProps({
  departments: Array,
  positions: Array,
  employmentTypes: Object,
  statusTypes: Object,
  errors: Object,
})

// Form data
const form = useForm({
  employee_id: '',
  first_name: '',
  last_name: '',
  email: '',
  phone_number: '',
  position: '',
  department: '',
  hire_date: '',
  salary: '',
  address: '',
  emergency_contact_name: '',
  emergency_contact_phone: '',
  employment_type: 'full-time',
  status: 'active',
  is_active: true,
  notes: '',
})

// Computed
const today = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const processing = computed(() => form.processing)
const errors = computed(() => form.errors)

// Methods
const submit = () => {
  form.post(route('admin.staff.store'), {
    onSuccess: () => {
      // Success handled by redirect
    },
    onError: (errors) => {
      console.log('Validation errors:', errors)
    }
  })
}
</script>
