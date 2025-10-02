<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Plus, Edit3, Trash2, Eye, ArrowUpDown } from 'lucide-vue-next';
import type { BreadcrumbItemType } from '@/types';
import { ref, watch } from 'vue';
import { confirmDialog } from '@/lib/confirm'
import { useTableFilters } from '@/composables/useTableFilters'

const props = defineProps<{
  services: {
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
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    sort_by?: string;
    sort_order?: string;
  };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Services', href: route('admin.services.index') },
];

const { search, perPage, sort, direction, toggleSort } = useTableFilters({
  routeName: 'admin.services.index',
  initial: {
    search: props.filters?.search,
    per_page: props.filters?.per_page ?? props.services?.meta?.per_page ?? 5,
    sort: props.filters?.sort,
    direction: props.filters?.direction ?? 'asc',
  }
})

// Status filter (All/Active/Inactive)
const statusFilter = ref(props.filters?.status || 'All')

watch([statusFilter, search, perPage, sort, direction], () => {
  router.get(route('admin.services.index'), {
    search: search.value,
    per_page: perPage.value,
    sort: sort.value,
    direction: direction.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true })
})

const destroy = async (id: number, name: string) => {
  const ok = await confirmDialog({
    title: 'Delete Service',
    message: `Are you sure you want to delete the service "${name}"?`,
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.services.destroy', id));
};

const formatCurrency = (value: number | string) => {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
  <Head title="Manage Services" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold">Services Price List</h1>
          <p class="text-sm text-muted-foreground">Manage the billable services your organization offers.</p>
        </div>
        <Link :href="route('admin.services.create')" class="btn-glass inline-flex items-center gap-2">
          <Plus class="h-4 w-4" />
          <span>Add New Service</span>
        </Link>
      </div>

      <!-- Filters -->
       <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="relative w-full md:w-1/3">
          <input type="text" v-model="search" placeholder="Search services..." class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100" />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
        </div>
        <div class="flex items-center gap-3">
          <div>
            <label for="statusFilter" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Status:</label>
            <select id="statusFilter" v-model="statusFilter" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
              <option>All</option>
              <option>Active</option>
              <option>Inactive</option>
            </select>
          </div>
          <div>
    <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
    <select id="perPage" v-model="perPage" class="rounded-md border-cyan-600 bg-cyan-600 text-white sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-700 dark:border-gray-700">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
    </select>
</div>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('name')">
                Service Name <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('price')">
                Price <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('is_active')">
                Status <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in services.data" :key="service.id" class="border-b dark:border-gray-700">
              <td class="px-6 py-4 font-medium">{{ service.name }}</td>
              <td class="px-6 py-4">{{ formatCurrency(service.price) }}</td>
              <td class="px-6 py-4">
                <span :class="['px-2 py-1 text-xs font-semibold rounded-full', service.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                  {{ service.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.services.show', service.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.services.edit', service.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(service.id, service.name)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-red-600 hover:bg-red-100 dark:hover:bg-red-900"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
             <tr v-if="services.data.length === 0">
              <td colspan="4" class="text-center px-6 py-4 text-gray-400">No services found.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <Pagination v-if="services.data.length > 0" :links="services.links" class="mt-6 flex justify-center" />
    </div>
  </AppLayout>
</template>
