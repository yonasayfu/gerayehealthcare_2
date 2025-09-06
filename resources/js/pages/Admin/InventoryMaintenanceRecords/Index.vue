<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue'; // Import 'computed'
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Edit3, Trash2, Search, FileText } from 'lucide-vue-next'; // Import Search, FileText
import Pagination from '@/components/Pagination.vue';
import { format } from 'date-fns'; // Import format
import type { InventoryMaintenanceRecordPagination } from '@/types'; // Import InventoryMaintenanceRecordPagination type
import { confirmDialog } from '@/lib/confirm'

interface InventoryMaintenanceRecordFilters {
  search?: string;
  sort?: string;
  direction?: 'asc' | 'desc';
  per_page?: number;
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Maintenance Records', href: route('admin.inventory-maintenance-records.index') },
];

const props = defineProps<{
  maintenanceRecords: InventoryMaintenanceRecordPagination;
  filters: {
    type: object,
    default: () => ({
      search: '',
      sort: '',
      direction: 'asc',
      per_page: 5,
    }),
  },
}>();

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || 'asc');
const perPage = ref(props.filters.per_page || 5);

// Create a computed property for the formatted date string
const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p'); // Use the imported format function here
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

  router.get(route('admin.inventory-maintenance-records.index'), params, {
    preserveState: true,
    replace: true,
  });
}, 500));

const toggleSort = (field: string) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};


import { useExport } from '@/composables/useExport';

const { exportData, printCurrentView, printAllRecords } = useExport({ routeName: 'admin.inventory-maintenance-records', filters: props.filters });

const destroy = async (id: number) => {
  const ok = await confirmDialog({
    title: 'Delete Maintenance Record',
    message: 'Are you sure you want to delete this record?',
    confirmText: 'Delete',
    variant: 'danger',
  })
  if (!ok) return
  router.delete(route('admin.inventory-maintenance-records.destroy', id), {
    preserveScroll: true,
  });
};

</script>

<template>
  <Head title="Inventory Maintenance Records" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Inventory Maintenance Records</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage inventory maintenance records</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.inventory-maintenance-records.create')" class="btn-glass">
              <span>Add Inventory Maintenance Record</span>
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

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
                <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Inventory Maintenance Records List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('item_id')">
                Item <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('scheduled_date')">
                Scheduled Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('actual_date')">
                Actual Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('performed_by_staff_id')">
                Performed By <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in maintenanceRecords.data" :key="record.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ record.item.name ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.scheduled_date ?? '-' }}</td>
              <td class="px-6 py-4">{{ record.actual_date ? format(new Date(record.actual_date), 'PPP') : '-' }}</td>
              <td class="px-6 py-4">{{ record.performed_by_staff ? `${record.performed_by_staff.first_name} ${record.performed_by_staff.last_name}` : '-' }}</td>
              <td class="px-6 py-4">{{ record.status ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.inventory-maintenance-records.show', record.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.inventory-maintenance-records.edit', record.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(record.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="maintenanceRecords.data.length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No maintenance records found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- THE FIX IS HERE: Replaced the manual links with the reusable Pagination component -->
      <Pagination v-if="maintenanceRecords.data.length > 0" :links="maintenanceRecords.links" class="mt-6 flex justify-center print:hidden" />
      <p v-if="maintenanceRecords.total" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300 print:hidden">
        Showing {{ maintenanceRecords.from || 0 }}–{{ maintenanceRecords.to || 0 }} of {{ maintenanceRecords.total }}
      </p>

      <!-- spacer to prevent content under footer when printing -->
      <div class="hidden print:block h-24"></div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> 
      </div>

    </div>
  </AppLayout>
</template>
