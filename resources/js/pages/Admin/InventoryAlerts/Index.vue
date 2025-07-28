<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import Pagination from '@/components/Pagination.vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Alerts', href: route('admin.inventory-alerts.index') },
];

const props = defineProps<{
  inventoryAlerts: {
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
  filters: {
    type: Object,
    default: () => ({
      search: '',
      per_page: 10,
    }),
  },
}>();

const search = ref(props.filters.search || '');
const perPage = ref(props.inventoryAlerts?.meta?.per_page || 10);

watch([search, perPage], debounce(() => {
  router.get(route('admin.inventory-alerts.index'), {
    search: search.value,
    per_page: perPage.value,
  }, { preserveState: true, replace: true })
}, 300))

</script>
<template>
  <Head title="Inventory Alerts" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Alerts</h1>
          <p class="text-sm text-muted-foreground">Items that are low in stock and need attention.</p>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <div class="relative w-full md:w-1/3">
              <input type="text" v-model="search" placeholder="Search by item name..." class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100" />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
          </div>
          <div>
              <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
              <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
          </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">Item Name</th>
              <th class="px-6 py-3">Quantity on Hand</th>
              <th class="px-6 py-3">Reorder Level</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="alert in inventoryAlerts.data" :key="alert.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ alert.item.name }}</td>
              <td class="px-6 py-4">{{ alert.item.quantity_on_hand }}</td>
              <td class="px-6 py-4">{{ alert.item.reorder_level }}</td>
            </tr>
            <tr v-if="inventoryAlerts.data.length === 0">
              <td colspan="3" class="text-center px-6 py-4 text-gray-400">No inventory alerts.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-6">
        <Pagination v-if="inventoryAlerts.data.length > 0" :links="inventoryAlerts.links" />
      </div>
      
    </div>
  </AppLayout>
</template>
