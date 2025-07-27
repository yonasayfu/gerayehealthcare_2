<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'; // Import the component
import { Edit3, Trash2, ArrowUpDown, Plus, Paperclip, MapPin, FileText } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import { format } from 'date-fns'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  visitServices: {
    data: Array<any>;
    links: Array<any>;
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters: any;
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Visit Services', href: route('admin.visit-services.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort_by || 'scheduled_at')
const sortDirection = ref(props.filters.sort_direction || 'desc')
const perPage = ref(props.visitServices.meta.per_page || 10)

watch([search, sortField, sortDirection, perPage], debounce(() => {
  router.get(route('admin.visit-services.index'), {
    search: search.value,
    sort_by: sortField.value,
    sort_direction: sortDirection.value,
    per_page: perPage.value,
  }, { preserveState: true, replace: true })
}, 300))

function destroy(id: number) {
  if (confirm('Are you sure you want to cancel this visit?')) {
    router.delete(route('admin.visit-services.destroy', id), { preserveScroll: true })
  }
}

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

const formatDate = (dateString: string | null) => {
    if (!dateString) return 'N/A';
    return format(new Date(dateString), 'MMM dd, yyyy, hh:mm a');
};
</script>

<template>
  <Head title="Visit Services" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Visit Services</h1>
          <p class="text-sm text-muted-foreground">Manage all scheduled patient visits.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.visit-services.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            <Plus class="h-4 w-4" /> Schedule Visit
          </Link>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <div class="relative w-full md:w-1/3">
              <input type="text" v-model="search" placeholder="Search by Patient or Staff..." class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100" />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
          </div>
          <div>
              <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
              <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
          </div>
      </div>

      <!-- Data Table -->
      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Assigned Staff</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('scheduled_at')">Scheduled At <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">Status <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
              <th class="px-6 py-3">Documents</th>
              <th class="px-6 py-3">Location</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="visit in visitServices.data" :key="visit.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ visit.patient?.full_name ?? 'N/A' }}</td>
              <td class="px-6 py-4">{{ visit.staff ? `${visit.staff.first_name} ${visit.staff.last_name}` : 'N/A' }}</td>
              <td class="px-6 py-4">{{ formatDate(visit.scheduled_at) }}</td>
              <td class="px-6 py-4">
                <span :class="{'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true, 'bg-yellow-100 text-yellow-800': visit.status === 'Pending', 'bg-green-100 text-green-800': visit.status === 'Completed', 'bg-red-100 text-red-800': visit.status === 'Cancelled', 'bg-blue-100 text-blue-800': visit.status === 'In Progress'}">{{ visit.status }}</span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center space-x-2">
                  <a v-if="visit.prescription_file_url" :href="visit.prescription_file_url" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="View Prescription"><Paperclip class="w-4 h-4" /></a>
                  <a v-if="visit.vitals_file_url" :href="visit.vitals_file_url" target="_blank" class="text-teal-600 hover:text-teal-800" title="View Vitals"><Paperclip class="w-4 h-4" /></a>
                </div>
              </td>
              <td class="px-6 py-4">
                <a v-if="visit.check_in_latitude && visit.check_in_longitude" :href="`https://www.google.com/maps/search/?api=1&query=${visit.check_in_latitude},${visit.check_in_longitude}`" target="_blank" class="text-blue-600 hover:text-blue-800" title="View Check-in Location"><MapPin class="w-5 h-5" /></a>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end space-x-2">
                  
                  <Link :href="route('admin.visit-services.show', visit.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Details"><Eye class="w-4 h-4" /></Link>
                  <Link :href="route('admin.visit-services.edit', visit.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit"><Edit3 class="w-4 h-4" /></Link>
                  <button @click="destroy(visit.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Cancel Visit"><Trash2 class="w-4 h-4" /></button>
                </div>
              </td>
            </tr>
            <tr v-if="visitServices.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400">No visits found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-6">
        <div class="flex items-center gap-2">
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        <Pagination v-if="visitServices.data.length > 0" :links="visitServices.links" />
      </div>
      
    </div>
  </AppLayout>
</template>
