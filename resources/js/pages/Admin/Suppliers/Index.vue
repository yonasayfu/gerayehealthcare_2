<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
];

const props = defineProps({
  suppliers: Object, // Paginated list of suppliers
});

const search = ref('');
const filters = ref({});

// Implement search and filter logic here later

</script>

<template>
  <Head title="Suppliers" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Suppliers</h1>
          <p class="text-sm text-muted-foreground">Manage all inventory suppliers.</p>
        </div>
        <Link :href="route('admin.suppliers.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          Add New Supplier
        </Link>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Search and Filter Section -->
        <div class="mb-4">
          <input type="text" v-model="search" placeholder="Search suppliers..." class="w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <!-- Suppliers Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2">Name</th>
                <th class="p-2">Contact Person</th>
                <th class="p-2">Email</th>
                <th class="p-2">Phone</th>
                <th class="p-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="supplier in suppliers.data" :key="supplier.id" class="border-t">
                <td class="p-2">{{ supplier.name }}</td>
                <td class="p-2">{{ supplier.contact_person }}</td>
                <td class="p-2">{{ supplier.email }}</td>
                <td class="p-2">{{ supplier.phone }}</td>
                <td class="p-2 text-right">
                  <Link :href="route('admin.suppliers.edit', supplier.id)" class="text-blue-600 hover:underline">Edit</Link>
                </td>
              </tr>
              <tr v-if="suppliers.data.length === 0">
                <td colspan="5" class="text-center p-4 text-muted-foreground">No suppliers found.</td>
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
