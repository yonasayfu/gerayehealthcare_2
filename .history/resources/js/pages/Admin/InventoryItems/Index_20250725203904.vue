<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import Pagination from '@/components/Pagination.vue';
import debounce from 'lodash/debounce';
import Pagination from '@/components/Pagination.vue';
import { format } from 'date-fns'; // Import format

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Items', href: route('admin.inventory-items.index') },
];

const props = defineProps({
  inventoryItems: Object, // Paginated list of inventory items
  filters: { // Add filters prop with a default empty object
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort_by || ''); // Changed to sortField for consistency
const sortDirection = ref(props.filters.sort_direction || 'asc'); // Changed to sortDirection
const perPage = ref(props.filters.per_page || 10); // Added perPage

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

  router.get(route('admin.inventory-items.index'), params, {
    preserveState: true,
    replace: true,
  });
}, 500));

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this inventory item?')) {
    router.delete(route('admin.inventory-items.destroy', id));
  }
}

function exportData(type: 'csv' | 'pdf') {
  window.open(route('admin.inventory-items.export', { type }), '_blank');
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

const printAllInventoryItems = () => {
    // This will call your InventoryItemController@generatePdf method
    window.open(route('admin.inventory-items.generatePdf'), '_blank');
};

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
}

</script>

<template>
  <Head title="Inventory Items" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Items</h1>
          <p class="text-sm text-muted-foreground">Manage all medical equipment and supplies.</p>
        </div>
        <Link :href="route('admin.inventory-items.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          Add New Item
        </Link>
        <a :href="route('admin.inventory-items.export')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          <Download class="h-4 w-4" /> Export
        </a>
        <a :href="route('admin.inventory-items.generatePdf')" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
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
          <input type="text" v-model="search" placeholder="Search items..." class="w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <!-- Inventory Items Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2 cursor-pointer" @click="toggleSort('name')">Name <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('item_category')">Category <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('item_type')">Type <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('serial_number')">Serial Number <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('status')">Status <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in inventoryItems.data" :key="item.id" class="border-t">
                <td class="p-2">{{ item?.name }}</td>
                <td class="p-2">{{ item?.item_category }}</td>
                <td class="p-2">{{ item?.item_type }}</td>
                <td class="p-2">{{ item?.serial_number }}</td>
                <td class="p-2">{{ item?.status }}</td>
                <td class="p-2 text-right">
                  <Link v-if="item?.id" :href="route('admin.inventory-items.edit', item.id)" class="text-blue-600 hover:underline">Edit</Link>
                </td>
              </tr>
              <tr v-if="inventoryItems.data.length === 0">
                <td colspan="6" class="text-center p-4 text-muted-foreground">No inventory items found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
          <Pagination :links="inventoryItems.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
