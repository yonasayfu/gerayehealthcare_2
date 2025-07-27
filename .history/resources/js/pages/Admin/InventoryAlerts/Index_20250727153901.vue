<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Alerts', href: route('admin.inventory-alerts.index') },
];

const props = defineProps({
  inventoryAlerts: Object, // Paginated list of inventory alerts
});

const search = ref('');
const filters = ref({});
const alertCount = ref(0);

onMounted(async () => {
  try {
    const response = await axios.get(route('admin.inventory-alerts.count'));
    alertCount.value = response.data.count;
  } catch (error) {
    console.error('Error fetching alert count:', error);
  }
});

// Implement search and filter logic here later

</script>

<template>
  <Head title="Inventory Alerts" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">
            Inventory Alerts
            <span v-if="alertCount > 0" class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ alertCount }}</span>
          </h1>
          <p class="text-sm text-muted-foreground">Manage all alerts related to inventory items.</p>
        </div>
        <Link :href="route('admin.inventory-alerts.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          Create New Alert
        </Link>
        <a :href="route('admin.inventory-alerts.export')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          <Download class="h-4 w-4" /> Export
        </a>
        <a :href="route('admin.inventory-alerts.generatePdf')" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
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
          <input type="text" v-model="search" placeholder="Search alerts..." class="w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <!-- Inventory Alerts Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2">Item</th>
                <th class="p-2">Alert Type</th>
                <th class="p-2">Message</th>
                <th class="p-2">Status</th>
                <th class="p-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="alert in inventoryAlerts.data" :key="alert.id" class="border-t">
                <td class="p-2">{{ alert.item.name }}</td>
                <td class="p-2">{{ alert.alert_type }}</td>
                <td class="p-2">{{ alert.message }}</td>
                <td class="p-2">{{ alert.is_active ? 'Active' : 'Inactive' }}</td>
                <td class="p-2 text-right">
                  <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.inventory-alerts.show', alert.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600" title="View Details"><Eye class="w-4 h-4" /></Link>
                    <Link :href="route('admin.inventory-alerts.edit', alert.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit"><Edit3 class="w-4 h-4" /></Link>
                    <button @click="destroy(alert.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900 text-red-600" title="Delete"><Trash2 class="w-4 h-4" /></button>
                  </div>
                </td>
              </tr>
              <tr v-if="inventoryAlerts.data.length === 0">
                <td colspan="5" class="text-center p-4 text-muted-foreground">No inventory alerts found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
          <Pagination :links="inventoryAlerts.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
