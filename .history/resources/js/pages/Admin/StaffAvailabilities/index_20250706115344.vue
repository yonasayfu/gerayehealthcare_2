<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Trash2, ArrowUpDown, Filter } from 'lucide-vue-next'
import debounce from 'lodash/debounce'

const props = defineProps<{
  availabilities: any;
  filters: any;
  staffList: Array<{ id: number; first_name: string; last_name: string }>;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Staff Availabilities', href: '/dashboard/staff-availabilities' },
]

// Reactive state for filters
const filters = ref({
  staff_id: props.filters.staff_id || '',
  status: props.filters.status || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  sort: props.filters.sort || 'start_time',
  direction: props.filters.direction || 'desc',
  per_page: props.filters.per_page || 15,
})

// Watch for changes and trigger API request
watch(filters, debounce(() => {
  router.get('/dashboard/staff-availabilities', { ...filters.value }, {
    preserveState: true,
    replace: true,
  })
}, 300), { deep: true })

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this availability slot?')) {
    router.delete(route('admin.staff-availabilities.destroy', id), {
        preserveScroll: true,
    })
  }
}

function toggleSort(field: string) {
  if (filters.value.sort === field) {
    filters.value.direction = filters.value.direction === 'asc' ? 'desc' : 'asc'
  } else {
    filters.value.sort = field
    filters.value.direction = 'asc'
  }
}

const getStaffFullName = (staff) => {
    if (!staff) return 'N/A';
    return `${staff.first_name || ''} ${staff.last_name || ''}`.trim();
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
}
</script>

<template>
  <Head title="Staff Availabilities" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff Availability Records</h1>
        <p class="text-sm text-muted-foreground">Review and manage all staff availability slots.</p>
      </div>

      <!-- Filters Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <Filter class="h-5 w-5 text-muted-foreground" />
            <h3 class="text-lg font-semibold">Filters</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filter by Staff -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Staff Member</label>
                <select v-model="filters.staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800">
                    <option value="">All Staff</option>
                    <option v-for="staff in staffList" :key="staff.id" :value="staff.id">
                        {{ getStaffFullName(staff) }}
                    </option>
                </select>
            </div>
            <!-- Filter by Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select v-model="filters.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800">
                    <option value="">All Statuses</option>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            <!-- Filter by Start Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                <input type="date" v-model="filters.start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800">
            </div>
            <!-- Filter by End Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                <input type="date" v-model="filters.end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800">
            </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('staff_id')">
                Staff Member <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('start_time')">
                Start Time <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('end_time')">
                End Time <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="availability in availabilities.data" :key="availability.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ getStaffFullName(availability.staff) }}</td>
              <td class="px-6 py-4">
                 <span :class="[
                  'px-2 py-1 rounded-full text-xs font-semibold',
                  availability.status === 'Available' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                ]">{{ availability.status }}</span>
              </td>
              <td class="px-6 py-4">{{ formatDate(availability.start_time) }}</td>
              <td class="px-6 py-4">{{ formatDate(availability.end_time) }}</td>
              <td class="px-6 py-4 text-right">
                <button @click="destroy(availability.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete Slot">
                    <Trash2 class="w-4 h-4" />
                </button>
              </td>
            </tr>
            <tr v-if="availabilities.data.length === 0">
              <td colspan="5" class="text-center px-6 py-4 text-gray-400">No availability records found for the selected filters.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Links -->
      <div class="flex justify-end" v-if="availabilities.links.length > 1">
        <div class="flex items-center space-x-1">
          <Link
            v-for="(link, i) in availabilities.links"
            :key="i"
            :href="link.url || ''"
            v-html="link.label"
            :class="[
              'px-3 py-1 rounded-md text-sm',
              link.active ? 'bg-primary-600 text-white' : 'hover:bg-gray-200 dark:hover:bg-gray-700',
              !link.url && 'cursor-not-allowed text-gray-400'
            ]"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
