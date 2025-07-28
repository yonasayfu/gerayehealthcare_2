<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Edit3, Trash2, Search } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { format } from 'date-fns';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Items', href: route('admin.inventory-requests.index') },
];

const props = defineProps({
  inventoryRequests: Object, // Paginated list of inventory requests
  filters: { // Add filters prop with a default empty object
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort_by || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');
const perPage = ref(props.filters.per_page || 10);

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

const toggleSort = (column: string) => {
  if (sortField.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = column;
    sortDirection.value = 'asc';
  }
};

watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    sort_by: sortField.value,
    sort_direction: sortDirection.value,
    per_page: perPage.value,
  };
  router.get(route('admin.inventory-requests.index'), params, { preserveState: true, replace: true });
}, 300));

const destroy = (id: number) => {
  if (confirm('Are you sure you want to delete this request?')) {
    router.delete(route('admin.inventory-requests.destroy', id), {
      preserveScroll: true,
    });
  }
};

const downloadCsv = () => {
  window.open(route('admin.inventory-requests.export'), '_blank');
};



const printAllRequests = () => {
  const url = route('admin.inventory-requests.generatePdf', { ...props.filters });
  window.open(url, '_blank');
};

</script>

<template>
  <Head title="inventory-requests" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search requests..."
            class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
          />
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Items</h1>
            <p class="text-sm text-muted-foreground">Manage all requests for inventory items.</p>
          </div>
          <div class="flex items-center gap-2">
            <Link :href="route('admin.inventory-requests.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
              + Create New Request
            </Link>
            <a :href="route('admin.inventory-requests.export')" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
              <Download class="h-4 w-4" /> Export
            </a>
            <a :href="route('admin.inventory-requests.generatePdf')" target="_blank" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
              <Download class="h-4 w-4" /> PDF
            </a>
            <button @click="printAllRequests" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
              <Printer class="h-4 w-4" /> Print All
            </button>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search requests..."
            class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
          />
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent" v-if="inventoryRequests && inventoryRequests.data">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Hospital</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Inventory Items List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        <table class="min-w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
            <thead>
              <tr class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
                <th class="p-2 cursor-pointer" @click="toggleSort('requester_id')">Requester <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('item_id')">Item <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('quantity_requested')">Quantity <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('status')">Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('priority')">Priority <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" /></th>
                <th class="p-2 text-right print:hidden">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="request in inventoryRequests.data" :key="request.id" class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="p-2">{{ request.requester.first_name }} {{ request.requester.last_name }}</td>
                <td class="p-2">{{ request.item.name }}</td>
                <td class="p-2">{{ request.quantity_requested }}</td>
                <td class="p-2">{{ request.status }}</td>
                <td class="p-2">{{ request.priority }}</td>
                <td class="p-2 text-right print:hidden">
                  <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.inventory-requests.show', request.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600" title="View Details"><Eye class="w-4 h-4" /></Link>
                    <Link :href="route('admin.inventory-requests.edit', request.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit"><Edit3 class="w-4 h-4" /></Link>
                    <button @click="destroy(request.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900 text-red-600" title="Delete"><Trash2 class="w-4 h-4" /></button>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="6" class="text-center p-4 text-muted-foreground">No inventory requests found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination v-if="inventoryRequests.data.length > 0" :links="inventoryRequests.links" class="mt-6 flex justify-center print:hidden" />
      
        <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p>
        </div>
      </div>
      
  </AppLayout>
</template>


