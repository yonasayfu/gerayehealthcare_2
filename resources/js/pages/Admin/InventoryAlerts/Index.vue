<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import Pagination from '@/components/Pagination.vue';
import { Printer, Edit3, ClipboardList, Eye } from 'lucide-vue-next';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Alerts', href: route('admin.inventory-alerts.index') },
];

const props = defineProps<{
  inventoryAlerts: {
    data: Array<any>;
    links: Array<any>;
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters?: {
    search?: string;
    per_page?: number;
    active_only?: boolean | number | string;
    sort?: string;
    direction?: string;
  };
}>();

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.per_page || 5);
const activeOnly = ref(
  typeof props.filters?.active_only === 'undefined'
    ? true
    : props.filters?.active_only === true || props.filters?.active_only === 1 || props.filters?.active_only === '1'
);

// Sorting state
const sort = ref<string | undefined>(props.filters?.sort || undefined);
const direction = ref<'asc' | 'desc'>(
  (props.filters?.direction === 'asc' || props.filters?.direction === 'desc') ? (props.filters?.direction as 'asc' | 'desc') : 'asc'
);

// Keep local controls in sync when navigating via pagination links (props update)
watch(() => props.filters, (f) => {
  if (!f) return;
  search.value = f.search ?? '';
  perPage.value = (f.per_page as any) ?? 5;
  activeOnly.value = f.active_only === true || f.active_only === 1 || f.active_only === '1';
  sort.value = (f.sort as any) ?? undefined;
  direction.value = (f.direction === 'asc' || f.direction === 'desc') ? (f.direction as any) : direction.value;
}, { deep: true });

watch([search, perPage, activeOnly, sort, direction], debounce(() => {
  router.get(route('admin.inventory-alerts.index'), {
    search: search.value,
    per_page: perPage.value,
    active_only: activeOnly.value ? 1 : 0,
    sort: sort.value,
    direction: direction.value,
  }, { preserveState: true, replace: true })
}, 300))

// Build reactive filters object so prints use the latest UI state
const currentFilters = computed(() => ({
  search: search.value,
  per_page: perPage.value,
  active_only: activeOnly.value ? 1 : 0,
  sort: sort.value,
  direction: direction.value,
}));

// Server-side print handlers (PDF, landscape)
const onPrintAll = () => {
  const url = route('admin.inventory-alerts.printAll', { preview: true, ...(currentFilters.value as any) });
  window.open(url, '_blank');
};
const onPrintCurrent = () => {
  const url = route('admin.inventory-alerts.printCurrent', { preview: true, ...(currentFilters.value as any) });
  window.open(url, '_blank');
};

</script>
<template>
  <Head title="Inventory Alerts" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">
            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Inventory Alerts</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage inventory alerts</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.inventory-alerts.create')" class="btn-glass">
              <span>Add Inventory Alert</span>
            </Link>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
          <div class="relative w-full md:w-1/3">
              <input type="text" v-model="search" placeholder="Search by item name..." class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100" />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
          </div>
          <div class="flex items-center gap-4">
              <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" v-model="activeOnly" class="mr-2" />
                Show only active
              </label>
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

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">
                <button
                  class="inline-flex items-center gap-1 hover:underline"
                  @click="
                    if (sort === 'is_active') {
                      direction = (direction === 'asc' ? 'desc' : 'asc') as any;
                    } else {
                      sort = 'is_active';
                      direction = 'desc';
                    }
                  "
                >
                  Status
                  <svg v-if="sort === 'is_active'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                    <path v-if="direction === 'asc'" fill-rule="evenodd" d="M3.293 12.95a1 1 0 011.414 0L10 18.243l5.293-5.293a1 1 0 111.414 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414z" clip-rule="evenodd" />
                    <path v-else fill-rule="evenodd" d="M16.707 7.05a1 1 0 01-1.414 0L10 1.757 4.707 7.05A1 1 0 013.293 5.636l6-6a1 1 0 011.414 0l6 6a1 1 0 010 1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </th>
              <th class="px-6 py-3">Item Name</th>
              <th class="px-6 py-3">Qty on Hand</th>
              <th class="px-6 py-3">Reorder Level</th>
              <th class="px-6 py-3">Alert Type</th>
              <th class="px-6 py-3">Triggered</th>
              <th class="px-6 py-3">Message</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="alert in inventoryAlerts.data"
              :key="alert.id"
              class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800"
              :class="{ 'bg-red-50 dark:bg-red-900/10': alert.is_active }"
            >
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                  :class="alert.is_active ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200'"
                >
                  {{ alert.is_active ? 'Active' : 'Resolved' }}
                </span>
              </td>
              <td class="px-6 py-4">{{ alert.item?.name ?? '-' }}</td>
              <td class="px-6 py-4">{{ alert.item?.quantity_on_hand ?? '-' }}</td>
              <td class="px-6 py-4">{{ alert.item?.reorder_level ?? '-' }}</td>
              <td class="px-6 py-4">{{ alert.alert_type || '-' }}</td>
              <td class="px-6 py-4">{{ alert.triggered_at ? new Date(alert.triggered_at).toLocaleString() : '-' }}</td>
              <td class="px-6 py-4 truncate max-w-[280px]" :title="alert.message">{{ alert.message || '-' }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-1">
                  <Link
                    :href="route('admin.inventory-alerts.show', { inventory_alert: alert.id, return_to: route('admin.inventory-alerts.index', currentFilters.value as any) })"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.inventory-alerts.edit', alert.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600"
                    title="Edit Alert"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.task-delegations.create', {
                      title: `Restock ${alert.item?.name ?? 'Item'} - ${alert.alert_type ?? 'Alert'}`,
                      notes: alert.message ?? '',
                      return_to: route('admin.inventory-alerts.index', currentFilters.value as any)
                    })"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-cyan-50 dark:hover:bg-cyan-900/20 text-cyan-600"
                    title="Delegate Task"
                  >
                    <ClipboardList class="w-4 h-4" />
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-if="inventoryAlerts.data.length === 0">
              <td colspan="3" class="text-center px-6 py-4 text-gray-400">No inventory alerts.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6 print:hidden">
        <Pagination v-if="inventoryAlerts.data.length > 0" :links="inventoryAlerts.links" />
        <p v-if="inventoryAlerts?.meta?.total" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
          Showing {{ inventoryAlerts.meta.from || 0 }}–{{ inventoryAlerts.meta.to || 0 }} of {{ inventoryAlerts.meta.total }}
        </p>
      </div>
      
    </div>
  </AppLayout>
</template>