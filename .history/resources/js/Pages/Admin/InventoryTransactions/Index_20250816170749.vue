<![CDATA[<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Search, Trash2, Edit3 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';

// Define the type for inventory transaction data
interface InventoryTransaction {
  id: number;
  item: { name: string } | null;
  transaction_type: string;
  quantity: number;
  from_location: string | null;
  to_location: string | null;
  performed_by: { first_name: string; last_name: string } | null;
  created_at: string;
  updated_at: string | null;
  notes: string | null;
}

// Define the type for the paginated data
interface InventoryTransactionPagination {
  data: InventoryTransaction[];
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
  from: number | null;
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Transactions', href: route('admin.inventory-transactions.index') },
];

const props = defineProps<{
  inventoryTransactions: InventoryTransactionPagination; // Use the defined pagination type
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    page?: number; // Add page to filters for printCurrentView
  };
}>();

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || 5);
const filters = ref({
  search: props.filters.search || '',
  sort: props.filters.sort || 'created_at',
  direction: props.filters.direction || 'desc',
});

const toggleSort = (column: string) => {
  if (filters.value.sort === column) {
    filters.value.direction = filters.value.direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.value.sort = column;
    filters.value.direction = 'asc';
  }
};

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this inventory transaction?')) {
    router.delete(route('admin.inventory-transactions.destroy', id));
  }
}

watch([search, perPage], debounce(() => {
  filters.value.search = search.value;
  router.get(
    route('admin.inventory-transactions.index'),
    { ...filters.value, per_page: perPage.value },
    { preserveState: true, replace: true }
  );
}, 300));

watch(filters, () => {
  router.get(route('admin.inventory-transactions.index'), { ...filters.value, per_page: perPage.value }, { preserveState: true, replace: true });
}, { deep: true });

import { useExport } from '@/composables/useExport';

const { exportData, printCurrentView, printAllRecords } = useExport({ routeName: 'admin.inventory-transactions', filters: props.filters });

</script>

<template>
  <Head title="Inventory Transactions" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Transactions</h1>
          <p class="text-sm text-muted-foreground">View all movements and changes of inventory items.</p>
        </div>
        <div class="flex items-center gap-2">
          <button @click="exportData('csv')" class="btn btn-success" title="Export CSV" aria-label="Export CSV">
            <Download class="h-4 w-4" />
            <span class="ml-2">CSV</span>
          </button>
          <button @click="printCurrentView" class="btn btn-dark" title="Print current page" aria-label="Print current page">
            <Printer class="h-4 w-4" />
            <span class="ml-2">Print Current</span>
          </button>
        </div>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Print-only header -->
        <div class="hidden print:block print-container">
          <h1>Current Inventory Transactions List</h1>
          <table>
            <thead>
              <tr>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>From</th>
                <th>To</th>
                <th>Performed By</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in inventoryTransactions.data" :key="transaction.id">
                <td>{{ transaction.item ? transaction.item.name : '-' }}</td>
                <td>{{ transaction.transaction_type }}</td>
                <td>{{ transaction.quantity }}</td>
                <td>{{ transaction.from_location }}</td>
                <td>{{ transaction.to_location }}</td>
                <td>{{ transaction.performed_by ? (transaction.performed_by.first_name + ' ' + transaction.performed_by.last_name) : '-' }}</td>
                <td>{{ new Date(transaction.created_at).toLocaleDateString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Search and Filter Section -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4 print:hidden">
          <div class="relative w-full md:w-1/3">
            <input type="text" v-model="search" placeholder="Search transactions..." class="w-full input input-bordered pr-9" />
            <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
          </div>
          <div>
            <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
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
                <th class="p-2">#</th>
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
              <tr v-for="(transaction, i) in inventoryTransactions.data" :key="transaction.id" class="border-t">
                <td class="p-2">{{ (inventoryTransactions.from ?? 1) + i }}</td>
                <td class="p-2">{{ transaction.item ? transaction.item.name : '-' }}</td>
                <td class="p-2">{{ transaction.transaction_type }}</td>
                <td class="p-2">{{ transaction.quantity }}</td>
                <td class="p-2">{{ transaction.from_location }}</td>
                <td class="p-2">{{ transaction.to_location }}</td>
                <td class="p-2">{{ transaction.performed_by ? (transaction.performed_by.first_name + ' ' + transaction.performed_by.last_name) : '-' }}</td>
                <td class="p-2">{{ new Date(transaction.created_at).toLocaleDateString() }}</td>
                <td class="p-2 text-right">
                  <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.inventory-transactions.show', transaction.id)" class="btn-icon text-indigo-600" title="View"><Eye class="w-4 h-4" /></Link>
                  </div>
                </td>
              </tr>
              <tr v-if="inventoryTransactions.data.length === 0">
                <td colspan="9" class="text-center p-4 text-muted-foreground">No inventory transactions found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 print:hidden">
          <div class="flex justify-center">
            <Pagination :links="inventoryTransactions.links" />
          </div>
        </div>

        <!-- Print-only footer -->
        <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
          <hr class="my-2 border-gray-300" />
          <p>Document Generated: {{ new Date().toLocaleString() }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
.print-container {
  font-family: Arial, sans-serif;
  margin: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}
@media print {
  @page { size: A4; margin: 12mm; }
  .app-sidebar, .app-sidebar-header, header[role="banner"], nav[role="navigation"], .print\:hidden { display: none !important; }
  .hidden.print\:block { display: block !important; }
}
</style>
]]>
