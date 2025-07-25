<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Transactions', href: route('admin.inventory-transactions.index') },
];

const props = defineProps({
  inventoryTransactions: Object, // Paginated list of inventory transactions
});

const search = ref('');
const filters = ref({});

// Implement search and filter logic here later

</script>

<template>
  <Head title="Inventory Transactions" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Transactions</h1>
          <p class="text-sm text-muted-foreground">View all movements and changes of inventory items.</p>
        </div>
        <!-- Transactions are typically created via other actions, not directly here -->
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Search and Filter Section -->
        <div class="mb-4">
          <input type="text" v-model="search" placeholder="Search transactions..." class="w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <!-- Inventory Transactions Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2">Item</th>
                <th class="p-2">Type</th>
                <th class="p-2">Quantity</th>
                <th class="p-2">From</th>
                <th class="p-2">To</th>
                <th class="p-2">Performed By</th>
                <th class="p-2">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in inventoryTransactions.data" :key="transaction.id" class="border-t">
                <td class="p-2">{{ transaction.item.name }}</td>
                <td class="p-2">{{ transaction.transaction_type }}</td>
                <td class="p-2">{{ transaction.quantity }}</td>
                <td class="p-2">{{ transaction.from_location }}</td>
                <td class="p-2">{{ transaction.to_location }}</td>
                <td class="p-2">{{ transaction.performed_by.first_name }} {{ transaction.performed_by.last_name }}</td>
                <td class="p-2">{{ new Date(transaction.created_at).toLocaleDateString() }}</td>
              </tr>
              <tr v-if="inventoryTransactions.data.length === 0">
                <td colspan="7" class="text-center p-4 text-muted-foreground">No inventory transactions found.</td>
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
