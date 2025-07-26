<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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

import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Edit3, Trash2 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';

const search = ref(props.filters.search || '');
const filters = ref({
  search: props.filters.search || '',
  sort_by: props.filters.sort_by || 'name',
  sort_direction: props.filters.sort_direction || 'asc',
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
  router.get(route('admin.inventory-items.index'), filters.value, { preserveState: true, replace: true });
}, 300));

watch(filters, () => {
  router.get(route('admin.inventory-items.index'), filters.value, { preserveState: true, replace: true });
}, { deep: true });

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