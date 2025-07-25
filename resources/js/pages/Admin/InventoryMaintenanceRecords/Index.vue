<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Maintenance Records', href: route('admin.inventory-maintenance-records.index') },
];

const props = defineProps({
  maintenanceRecords: Object, // Paginated list of maintenance records
});

const search = ref('');
const filters = ref({});

// Implement search and filter logic here later

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
                <th class="p-2">Item</th>
                <th class="p-2">Scheduled Date</th>
                <th class="p-2">Actual Date</th>
                <th class="p-2">Performed By</th>
                <th class="p-2">Status</th>
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
                  <Link :href="route('admin.inventory-maintenance-records.edit', record.id)" class="text-blue-600 hover:underline">Edit</Link>
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
          <!-- Pagination links will go here -->
        </div>
      </div>
    </div>
  </AppLayout>
</template>
