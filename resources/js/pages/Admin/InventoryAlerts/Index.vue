<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Alerts', href: route('admin.inventory-alerts.index') },
];

const props = defineProps({
  inventoryAlerts: Object, // Paginated list of inventory alerts
});

const search = ref('');
const filters = ref({});

// Implement search and filter logic here later

</script>

<template>
  <Head title="Inventory Alerts" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Alerts</h1>
          <p class="text-sm text-muted-foreground">Manage all alerts related to inventory items.</p>
        </div>
        <Link :href="route('admin.inventory-alerts.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          Create New Alert
        </Link>
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
                  <Link :href="route('admin.inventory-alerts.edit', alert.id)" class="text-blue-600 hover:underline">Edit</Link>
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
          <!-- Pagination links will go here -->
        </div>
      </div>
    </div>
  </AppLayout>
</template>
