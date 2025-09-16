<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { confirmDialog } from '@/lib/confirm'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Plus, Paperclip, MapPin, Search } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { useTableFilters } from '@/composables/useTableFilters'
import { useExport } from '@/composables/useExport'

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

// Centralized filters with URL sync
const { search, perPage, toggleSort } = useTableFilters({
  routeName: 'admin.visit-services.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort,
    direction: props.filters?.direction,
    per_page: props.filters?.per_page ?? props.visitServices?.per_page ?? 5,
  },
})

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


async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Visit',
    message: 'Are you sure you want to cancel this visit?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.visit-services.destroy', id))
}

const { exportData, printCurrentView, printAllRecords } = useExport({ routeName: 'admin.visit-services', filters: props.filters || {} })

// printCurrentView and printAllRecords provided by useExport

function onToggleSort(field: string) { toggleSort(field) }

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
            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Visit Services</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage visit services</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.visit-services.create')" class="btn-glass">
              <span>Add Visit Service</span>
            </Link>
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
            <button @click="printAllRecords()" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print All</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
                  <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search visit services..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>
          <div>
              <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
              <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
          </div>
      </div>

      <!-- Data Table -->
      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Assigned Staff</th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('scheduled_at')">Scheduled At <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('status')">Status <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
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
                  <Link
                    :href="route('admin.visit-services.show', visit.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.visit-services.edit', visit.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(visit.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
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
      <div class="mt-6 space-y-2 print:hidden">
        <div class="flex justify-center">
          <Pagination v-if="visitServices.data.length > 0" :links="visitServices.links" />
        </div>
        <p v-if="visitServices.total" class="text-center text-sm text-gray-600 dark:text-gray-300">
          Showing {{ visitServices.from || 0 }}–{{ visitServices.to || 0 }} of {{ visitServices.total }}
        </p>
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
  @page { size: A4 landscape; margin: 0.5cm; }
  .app-sidebar-header, .app-sidebar { display: none !important; }
  body > header, body > nav, [role="banner"], [role="navigation"] { display: none !important; }
  html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
  table { border-collapse: collapse; width: 100%; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }
}
</style>
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
