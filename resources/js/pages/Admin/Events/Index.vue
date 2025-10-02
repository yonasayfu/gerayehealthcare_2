<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { confirmDialog } from '@/lib/confirm'
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { useTableFilters } from '@/composables/useTableFilters'
import { useExport } from '@/composables/useExport'

const props = defineProps({
    events: Object,
    filters: Object,
});

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Events', href: route('admin.events.index') },
];

const { search, perPage, toggleSort } = useTableFilters({
  routeName: 'admin.events.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort,
    direction: props.filters?.direction,
    per_page: props.filters?.per_page ?? props.events?.per_page ?? 5,
  },
})

const formattedGeneratedDate = computed(() => {
    return format(new Date(), 'PPP p');
});

// URL param sync handled by composable

async function destroy(id) {
    const ok = await confirmDialog({
        title: 'Delete Event',
        message: 'Are you sure you want to delete this event?',
        confirmText: 'Delete',
        cancelText: 'Cancel',
    })
    if (!ok) return
    router.delete(route('admin.events.destroy', id))
}

const { exportData, printCurrentView } = useExport({ routeName: 'admin.events', filters: props.filters || {} })

// printCurrentView provided by useExport

function onToggleSort(field: string) { toggleSort(field) }

// Safely format dates to avoid render errors on invalid inputs
function safeFormat(dateStr: string | null | undefined, fmt = 'PPP') {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return isNaN(d.getTime()) ? '-' : format(d, fmt)
}
</script>

<template>
    <Head title="Events" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6 print:p-0 print:space-y-0">
                  <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Events</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage events</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.events.create')" class="btn-glass">
              <span>Add Event</span>
            </Link>
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

                  <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <!-- keep original input size & rounded-lg but wrap with a subtle liquid-glass outer effect -->
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search events..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

                <div>
                     <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
              <select id="perPage" v-model="perPage" class="rounded-md border-cyan-600 bg-cyan-600 text-white sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-700 dark:border-gray-700">

                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Event List (Current View)</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
                        <tr>
                            <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('title')">
                                Title <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3">
                                Description
                            </th>
                            <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('event_date')">
                                Event Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3">
                                Free Service
                            </th>
                            <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('broadcast_status')">
                                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3 text-right print:hidden">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(event, idx) in (events.data || [])" :key="(event && event.id) ? event.id : idx">
                        <tr v-if="event" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
                            <td class="px-6 py-4">{{ event.title }}</td>
                            <td class="px-6 py-4">{{ event.description }}</td>
                            <td class="px-6 py-4">{{ safeFormat(event.event_date, 'PPP') }}</td>
                            <td class="px-6 py-4">{{ event.is_free_service ? 'Yes' : 'No' }}</td>
                            <td class="px-6 py-4">{{ event.broadcast_status }}</td>
                            <td class="px-6 py-4 text-right print:hidden">
                                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="(event && event.id) ? route('admin.events.show', event.id) : '#'"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="(event && event.id) ? route('admin.events.edit', event.id) : '#'"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="(event && event.id) && destroy(event.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
                            </td>
                        </tr>
                        </template>
                        <tr v-if="events.data && events.data.length === 0">
                            <td colspan="6" class="text-center px-6 py-4 text-gray-400">No events found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="events.data && events.data.length > 0" :links="events.links" class="mt-6 flex justify-center print:hidden" />

            <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
                <hr class="my-2 border-gray-300">
                <p>Document Generated: {{ formattedGeneratedDate }}</p>
            </div>
        </div>
    </AppLayout>
</template>
