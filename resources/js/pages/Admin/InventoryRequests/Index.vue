<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm'
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Eye, Edit3, Trash2, Search } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { format } from 'date-fns';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Requests', href: route('admin.inventory-requests.index') },
];

const props = defineProps({
  inventoryRequests: Object, // Paginated list of inventory requests
  filters: { // Add filters prop with a default empty object
    type: Object,
    default: () => ({}),
  },
});

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || 'created_at');
const sortDirection = ref(props.filters.direction || 'desc');
const perPage = ref(props.filters.per_page || 5);

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
    sort: sortField.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };
  router.get(route('admin.inventory-requests.index'), params, { preserveState: true, replace: true });
}, 300));

const destroy = async (id: number) => {
  const ok = await confirmDialog({
    title: 'Delete Inventory Request',
    message: 'Are you sure you want to delete this request?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.inventory-requests.destroy', id), {
    preserveScroll: true,
  });
};

import { useExport } from '@/composables/useExport';

const { printCurrentView, isProcessing } = useExport({ routeName: 'admin.inventory-requests', filters: props.filters });

</script>

<template>
  <Head title="Inventory Requests" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">
            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Inventory Requests</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage inventory requests</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.inventory-requests.create')" class="btn-glass">
              <span>Add Inventory Request</span>
            </Link>
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Print Header (visible only when printing) -->
      <div class="hidden print:block text-center mb-4">
        <div class="flex items-center justify-center gap-3">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Health Care" class="h-12 w-12 object-contain" />
          <div>
            <h1 class="text-xl font-semibold">Geraye Health Care</h1>
            <p class="text-sm text-gray-500">Inventory Requests — Current Page</p>
          </div>
        </div>
        <hr class="my-2 border-gray-300" />
      </div>

            <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <!-- keep original input size & rounded-lg but wrap with a subtle liquid-glass outer effect -->
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search inventory requests..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>
        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent" v-if="inventoryRequests && inventoryRequests.data">
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
                  <Link
                    :href="route('admin.inventory-requests.show', request.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.inventory-requests.edit', request.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(request.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="inventoryRequests.data.length === 0">
              <td colspan="6" class="text-center p-4 text-muted-foreground">No inventory requests found.</td>
            </tr>
          </tbody>
        </table>
        <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p>
        </div>
        </div>

        <!-- Pagination -->
        <Pagination v-if="inventoryRequests.data.length > 0" :links="inventoryRequests.links" class="mt-6 flex justify-center print:hidden" />
      </div>
      
  </AppLayout>
</template>