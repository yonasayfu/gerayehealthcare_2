<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Requests', href: route('admin.inventory-requests.index') },
];

const props = defineProps({
  inventoryRequests: Object, // Paginated list of inventory requests
});

const search = ref('');
const filters = ref({});

// Implement search and filter logic here later

</script>

<template>
  <Head title="Inventory Requests" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Requests</h1>
          <p class="text-sm text-muted-foreground">Manage all requests for inventory items.</p>
        </div>
        <Link :href="route('admin.inventory-requests.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          Create New Request
        </Link>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Search and Filter Section -->
        <div class="mb-4">
          <input type="text" v-model="search" placeholder="Search requests..." class="w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <!-- Inventory Requests Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2">Requester</th>
                <th class="p-2">Item</th>
                <th class="p-2">Quantity</th>
                <th class="p-2">Status</th>
                <th class="p-2">Priority</th>
                <th class="p-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="request in inventoryRequests.data" :key="request.id" class="border-t">
                <td class="p-2">{{ request.requester.first_name }} {{ request.requester.last_name }}</td>
                <td class="p-2">{{ request.item.name }}</td>
                <td class="p-2">{{ request.quantity_requested }}</td>
                <td class="p-2">{{ request.status }}</td>
                <td class="p-2">{{ request.priority }}</td>
                <td class="p-2 text-right">
                  <Link :href="route('admin.inventory-requests.edit', request.id)" class="text-blue-600 hover:underline">Edit</Link>
                </td>
              </tr>
              <tr v-if="inventoryRequests.data.length === 0">
                <td colspan="6" class="text-center p-4 text-muted-foreground">No inventory requests found.</td>
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
