<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Plus, Edit, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItemType } from '@/types';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps<{
  services: any;
  filters: any;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Services', href: route('admin.services.index') },
];

const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('admin.services.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const destroy = (id: number, name: string) => {
  if (confirm(`Are you sure you want to delete the service "${name}"?`)) {
    router.delete(route('admin.services.destroy', id));
  }
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
        <Link :href="route('admin.services.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
          <Plus class="h-4 w-4" /> Add New Service
        </Link>
      </div>

      <!-- Search Filter -->
       <div class="relative w-full md:w-1/3">
          <input type="text" v-model="search" placeholder="Search services..." class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100" />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
            <tr>
              <th class="px-6 py-3">Service Name</th>
              <th class="px-6 py-3">Price</th>
              <th class="px-6 py-3">Status</th>
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
                <div class="inline-flex items-center space-x-2">
                  <Link :href="route('admin.services.edit', service.id)" class="p-2 rounded-full hover:bg-blue-100"><Edit class="w-4 h-4 text-blue-600" /></Link>
                  <button @click="destroy(service.id, service.name)" class="p-2 rounded-full hover:bg-red-100"><Trash2 class="w-4 h-4 text-red-600" /></button>
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
