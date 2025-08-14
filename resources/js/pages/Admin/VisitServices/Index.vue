<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Plus, Paperclip, MapPin } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

const props = defineProps<{
  visitServices: {
    data: Array<any>;
    links: Array<any>;
    current_page: number;
    per_page: number;
    from: number;
    to: number;
    total: number;
  };
  filters?: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Visit Services', href: route('admin.visit-services.index') },
]

// Fix: Use optional chaining and provide defaults
const search = ref(props.filters?.search || '')
const sortField = ref(props.filters?.sort || '')
const sortDirection = ref(props.filters?.direction || 'asc')
const perPage = ref(props.filters?.per_page || 5)

// Create a computed property for the formatted date string
const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

const currentDate = computed(() => {
  return format(new Date(), 'PPP');
});

// Add a computed property to calculate the index numbers
const currentIndex = computed(() => {
  return (props.visitServices.current_page - 1) * props.visitServices.per_page;
});

// Trigger search, sort, pagination
watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  // Only add sort parameter if sortField.value is not an empty string
  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.visit-services.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to cancel this visit?')) {
    router.delete(route('admin.visit-services.destroy', id))
  }
}

function printCurrentView() {
  // Trigger browser print of the current index view
  setTimeout(() => window.print(), 50);
}

const printAllVisitServices = () => {
  window.open(route('admin.visit-services.printAll', { preview: true }), '_blank');
};

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
      <!-- Print Header (Hidden by default, shown only when printing) -->
      <div class="hidden print:block text-center mb-8">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="mx-auto mb-4 max-w-24">
        <h1 class="text-2xl font-bold">Geraye Home Care Services</h1>
        <h2 class="text-lg mt-2">Visit Services List</h2>
        <p class="text-sm text-gray-600 mt-2">Generated on {{ formattedGeneratedDate }}</p>
        <hr class="my-4 border-gray-400">
      </div>
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Visit Services</h1>
          <p class="text-sm text-muted-foreground">Manage all scheduled patient visits.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.visit-services.create')" class="btn btn-primary">
            <Plus class="h-4 w-4" /> Schedule Visit
          </Link>
          <button @click="printAllVisitServices" class="btn btn-info">
            <Printer class="h-4 w-4" /> Print All
          </button>
          <button @click="printCurrentView" class="btn btn-dark">
            <Printer class="h-4 w-4" /> Print Current
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
          <div class="relative w-full md:w-1/3">
              <input type="text" v-model="search" placeholder="Search by Patient or Staff..." class="form-input w-full rounded-md border border-gray-300 pl-3 pr-10 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100" />
              <svg class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
          </div>
          <div>
              <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
              <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white"><option :value="5">5</option><option :value="10">10</option><option :value="25">25</option><option :value="50">50</option><option :value="100">100</option></select>
          </div>
      </div>

      <!-- Data Table -->
      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Assigned Staff</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('scheduled_at')">Scheduled At <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">Status <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
              <th class="px-6 py-3 print:hidden">Documents</th>
              <th class="px-6 py-3 print:hidden">Location</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(visit, index) in visitServices.data" :key="visit.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ currentIndex + index + 1 }}</td>
              <td class="px-6 py-4">{{ visit.patient?.full_name ?? 'N/A' }}</td>
              <td class="px-6 py-4">{{ visit.staff ? `${visit.staff.first_name} ${visit.staff.last_name}` : 'N/A' }}</td>
              <td class="px-6 py-4">{{ formatDate(visit.scheduled_at) }}</td>
              <td class="px-6 py-4">
                <span :class="{'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true, 'bg-yellow-100 text-yellow-800': visit.status === 'Pending', 'bg-green-100 text-green-800': visit.status === 'Completed', 'bg-red-100 text-red-800': visit.status === 'Cancelled', 'bg-blue-100 text-blue-800': visit.status === 'In Progress'}">{{ visit.status }}</span>
              </td>
              <td class="px-6 py-4 print:hidden">
                <div class="flex items-center space-x-2">
                  <a v-if="visit.prescription_file_url" :href="visit.prescription_file_url" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="View Prescription"><Paperclip class="w-4 h-4" /></a>
                  <a v-if="visit.vitals_file_url" :href="visit.vitals_file_url" target="_blank" class="text-teal-600 hover:text-teal-800" title="View Vitals"><Paperclip class="w-4 h-4" /></a>
                </div>
              </td>
              <td class="px-6 py-4 print:hidden">
                <a v-if="visit.check_in_latitude && visit.check_in_longitude" :href="`https://www.google.com/maps/search/?api=1&query=${visit.check_in_latitude},${visit.check_in_longitude}`" target="_blank" class="text-blue-600 hover:text-blue-800" title="View Check-in Location"><MapPin class="w-5 h-5" /></a>
              </td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.visit-services.show', visit.id)" class="btn-icon text-gray-500" title="View Details"><Eye class="w-4 h-4" /></Link>
                  <Link :href="route('admin.visit-services.edit', visit.id)" class="btn-icon text-blue-600" title="Edit"><Edit3 class="w-4 h-4" /></Link>
                  <button @click="destroy(visit.id)" class="btn-icon text-red-600" title="Cancel Visit"><Trash2 class="w-4 h-4" /></button>
                </div>
              </td>
            </tr>
            <tr v-if="visitServices.data.length === 0">
              <td colspan="8" class="text-center px-6 py-4 text-gray-400">No visits found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-end mt-6 print:hidden">
        <Pagination v-if="visitServices.data.length > 0" :links="visitServices.links" />
      </div>

      
      
      <!-- Print-only footer with generated date -->
      <div class="hidden print:block vs-print-footer">
        <div class="vs-print-footer-inner">
          <div>Generated on: {{ formattedGeneratedDate }}</div>
          <div class="vs-print-footer-note">Geraye Home Care Services - Confidential Document</div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  .vs-print-footer {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    z-index: 1000 !important;
  }
  .vs-print-footer-inner {
    text-align: center !important;
    font-size: 0.8rem !important;
    color: #444 !important;
    border-top: 1px solid #ccc !important;
    padding: 8px 0 !important;
    background: #fff !important;
  }
  .vs-print-footer-note {
    font-size: 0.7rem !important;
    color: #777 !important;
    margin-top: 2px !important;
  }
}
</style>
