<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Edit2, Trash2 } from 'lucide-vue-next';
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
const perPage = ref(props.filters.per_page || 5);
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

watch([search, perPage], debounce(() => {
  filters.value.search = search.value;
  router.get(route('admin.inventory-transactions.index'), filters.value, { preserveState: true, replace: true });
}, 300));

watch(filters, () => {
  router.get(route('admin.inventory-transactions.index'), { ...filters.value, per_page: perPage.value }, { preserveState: true, replace: true });
}, { deep: true });

import { useExport } from '@/Composables/useExport';

const { exportData, printCurrentView, printAllRecords } = useExport({ routeName: 'admin.inventory-transactions', filters: props.filters });

function destroyTx(id: number) {
  if (confirm('Delete this transaction?')) {
    router.delete(route('admin.inventory-transactions.destroy', id));
  }
}

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
        <div class="flex items-center gap-2">
          <button @click="() => exportData('csv')" class="btn btn-primary">
            <Download class="h-4 w-4" /> Export CSV
          </button>
          <button @click="printCurrentView" class="btn btn-dark">
            <Printer class="h-4 w-4" /> Print Current
          </button>
          <button @click="printAllRecords" class="btn btn-dark">
            <Printer class="h-4 w-4" /> Print All
          </button>
        </div>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Search and Filter Section -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
          <div class="relative w-full md:w-1/3">
            <input type="text" v-model="search" placeholder="Search transactions..." class="w-full input input-bordered pr-9" />
          </div>
          <div>
            <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
            <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
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
                <td class="p-2">{{ transaction.performed_by ? (transaction.performed_by.first_name + ' ' + transaction.performed_by.last_name) : '-' }}</td>
                <td class="p-2">{{ new Date(transaction.created_at).toLocaleDateString() }}</td>
                <td class="p-2 text-right">
                  <div class="inline-flex items-center justify-end space-x-1">
                    <Link :href="route('admin.inventory-transactions.show', transaction.id)" class="btn btn-icon" title="View"><Eye class="w-4 h-4" /></Link>
                    <Link :href="route('admin.inventory-transactions.edit', transaction.id)" class="btn btn-icon" title="Edit"><Edit2 class="w-4 h-4" /></Link>
                    <button @click="() => destroyTx(transaction.id)" class="btn btn-icon text-red-600" title="Delete"><Trash2 class="w-4 h-4" /></button>
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