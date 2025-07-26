<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Edit3, Trash2 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Maintenance Records', href: route('admin.inventory-maintenance-records.index') },
];

const props = defineProps({
  maintenanceRecords: Object, // Paginated list of maintenance records
  filters: { // Add filters prop with a default empty object
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const filters = ref({
  search: props.filters.search || '',
  sort_by: props.filters.sort_by || 'scheduled_date',
  sort_direction: props.filters.sort_direction || 'desc',
});

const toggleSort = (column: string) => {
  if (filters.value.sort_by === column) {
    filters.value.sort_direction = filters.value.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.value.sort_by = column;
    filters.value.sort_direction = 'asc';
  }
};

watch(search, debounce(() => {
  filters.value.search = search.value;
  router.get(route('admin.inventory-maintenance-records.index'), filters.value, { preserveState: true, replace: true });
}, 300));

watch(filters, () => {
  router.get(route('admin.inventory-maintenance-records.index'), filters.value, { preserveState: true, replace: true });
}, { deep: true });

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
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Maintenance Records</h1>
          <p class="text-sm text-muted-foreground">Track and manage maintenance activities for inventory items.</p>
        </div>
        <Link :href="route('admin.inventory-maintenance-records.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          Add New Record
        </Link>
        <a :href="route('admin.inventory-maintenance-records.export')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          <Download class="h-4 w-4" /> Export
        </a>
        <a :href="route('admin.inventory-maintenance-records.generatePdf')" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          <Download class="h-4 w-4" /> PDF
        </a>
        <button class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          <Printer class="h-4 w-4" /> Print All
        </button>
        <button class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          <Printer class="h-4 w-4" /> Print Current
        </button>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Search and Filter Section -->
        <div class="mb-4">
          <input type="text" v-model="search" placeholder="Search records..." class="w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <!-- Maintenance Records Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2 cursor-pointer" @click="toggleSort('item_id')">Item <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('scheduled_date')">Scheduled Date <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('actual_date')">Actual Date <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('performed_by')">Performed By <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('status')">Status <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="record in maintenanceRecords.data" :key="record.id" class="border-t">
                <td class="p-2">{{ record.item.name }}</td>
                <td class="p-2">{{ record.scheduled_date }}</td>
                <td class="p-2">{{ record.actual_date }}</td>
                <td class="p-2">{{ record.performed_by }}</td>
                <td class="p-2">{{ record.status }}</td>
                <td class="p-2 text-right">
                  <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.inventory-maintenance-records.show', record.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600" title="View Details"><Eye class="w-4 h-4" /></Link>
                    <Link :href="route('admin.inventory-maintenance-records.edit', record.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit"><Edit3 class="w-4 h-4" /></Link>
                    <button @click="destroy(record.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900 text-red-600" title="Delete"><Trash2 class="w-4 h-4" /></button>
                  </div>
                </td>
              </tr>
              <tr v-if="maintenanceRecords.data.length === 0">
                <td colspan="6" class="text-center p-4 text-muted-foreground">No maintenance records found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
          <Pagination :links="maintenanceRecords.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>