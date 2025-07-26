<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue'; // Import 'computed'
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Edit3, Trash2, Search, FileText } from 'lucide-vue-next'; // Import Search, FileText
import Pagination from '@/components/Pagination.vue';
import { format } from 'date-fns'; // Import format
import type { InventoryMaintenanceRecordPagination } from '@/types'; // Import InventoryMaintenanceRecordPagination type

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
  filters: InventoryMaintenanceRecordFilters;
}>();

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || 'asc');
const perPage = ref(props.filters.per_page || 10);

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

const destroy = (id: number) => {
  if (confirm('Are you sure you want to delete this record?')) {
    router.delete(route('admin.inventory-maintenance-records.destroy', id), {
      preserveScroll: true,
    });
  }
};

function exportData(type: 'csv' | 'pdf') {
  window.open(route('admin.inventory-maintenance-records.export', { type }), '_blank');
}

function printCurrentView() {
  // Trigger print for the current view of the table
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog for current view. Please check your browser settings or try again.');
    }
  }, 100); // Small delay for reliability
}

const printAllMaintenanceRecords = () => {
    // This will call your InventoryMaintenanceRecordController@generatePdf method
    window.open(route('admin.inventory-maintenance-records.generatePdf'), '_blank');
};

const destroy = (id: number) => {
  if (confirm('Are you sure you want to delete this record?')) {
    router.delete(route('admin.inventory-maintenance-records.destroy', id), {
      preserveScroll: true,
    });
  }
};

</script>

<template>
  <Head title="Inventory Maintenance Records" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Maintenance Records</h1>
          <p class="text-sm text-muted-foreground">Track and manage maintenance activities for inventory items.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.inventory-maintenance-records.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add New Record
          </Link>
          <button @click="exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printAllMaintenanceRecords" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print All
          </button>
    </div>
  </AppLayout>
</template>