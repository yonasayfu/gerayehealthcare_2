<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Transactions', href: route('admin.inventory-transactions.index') },
];

const props = defineProps({
  inventoryTransactions: Object, // Paginated list of inventory transactions
  filters: { // Add filters prop with a default empty object
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const filters = ref({
  search: props.filters.search || '',
  sort_by: props.filters.sort_by || 'created_at',
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
  router.get(route('admin.inventory-transactions.index'), filters.value, { preserveState: true, replace: true });
}, 300));

watch(filters, () => {
  router.get(route('admin.inventory-transactions.index'), filters.value, { preserveState: true, replace: true });
}, { deep: true });

const generatePdf = () => {
  const url = route('admin.inventory-transactions.generatePdf', { ...filters.value });
  window.open(url, '_blank');
};

</script>

<template>
  <Head title="Inventory Transactions" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory ransactions</h1>
          <p class="text-sm text-muted-foreground">View all movements and changes of inventory items.</p>
        </div>
        <div class="flex items-center gap-2">
          <!-- Transactions are typically created via other actions, not directly here -->
          <a :href="route('admin.inventory-transactions.export')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            <Download class="h-4 w-4" /> Export
          </a>
          <button @click="generatePdf" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> PDF
          </button>
        </div>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Search and Filter Section -->
        <div class="mb-4">
          <input type="text" v-model="search" placeholder="Search transactions..." class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
        </div>

        <!-- Inventory Maintenance Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2 cursor-pointer" @click="toggleSort('item_id')">Item <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('transaction_type')">Type <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('quantity')">Quantity <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('from_location')">From <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('to_location')">To <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('performed_by_id')">Performed By <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('created_at')">Date <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 text-right">Actions</th>
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
                <td class="p-2 text-right">
                  <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.inventory-transactions.show', transaction.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600" title="View Details"><Eye class="w-4 h-4" /></Link>
                  </div>
                </td>
              </tr>
              <tr v-if="inventoryTransactions.data.length === 0">
                <td colspan="7" class="text-center p-4 text-muted-foreground">No inventory transactions found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
          <Pagination :links="inventoryTransactions.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>