<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm'
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

const destroy = async (id: number) => {
  const ok = await confirmDialog({
    title: 'Delete Inventory Transaction',
    message: 'Are you sure you want to delete this transaction?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.inventory-transactions.destroy', id), {
    preserveScroll: true,
  });
};

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
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Transactions</h1>
          <p class="text-sm text-muted-foreground">View all movements and changes of inventory items.</p>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('admin.invoices.create')" class="btn-glass">
              <span>Create Transaction</span>
            </Link>
          <button @click="exportData('csv')" class="btn-glass btn-glass-sm" title="Export CSV" aria-label="Export CSV">
            <Download class="h-4 w-4" />
            <span class="ml-2">CSV</span>
          </button>
          <button @click="printCurrentView" class="btn-glass btn-glass-sm" title="Print current page" aria-label="Print current page">
            <Printer class="h-4 w-4" />
            <span class="ml-2">Print Current</span>
          </button>
        </div>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <!-- Search and Filter Section -->
              <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <!-- keep original input size & rounded-lg but wrap with a subtle liquid-glass outer effect -->
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search inventory transactions..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>
          <div>
            <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
            <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
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
              <tr v-if="inventoryTransactions?.data?.length > 0" v-for="(transaction, i) in inventoryTransactions.data" :key="transaction.id" class="border-t">
                <td class="p-2">{{ (inventoryTransactions?.from ?? 1) + i }}</td>
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
                    <Link :href="route('admin.inventory-transactions.edit', transaction.id)" class="btn-icon text-blue-600" title="Edit"><Edit3 class="w-4 h-4" /></Link>
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
        <div class="mt-4">
          <div class="flex justify-center">
            <Pagination :links="inventoryTransactions.links" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page { size: A4; margin: 12mm; }
  /* Hide AppLayout chrome while printing */
  .app-sidebar, .app-sidebar-header, header[role="banner"], nav[role="navigation"] { display: none !important; }
  /* Ensure print-only blocks are visible */
  .hidden.print\:block { display: block !important; }
}
</style>
]]>
