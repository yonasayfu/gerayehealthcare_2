<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Edit3, Trash2, Printer, ArrowUpDown, Eye, Search, ToggleLeft, ToggleRight } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

interface LeadSource {
  id: number;
  name: string;
  category: string;
  description: string;
  is_active: boolean;
}

interface LeadSourcePagination {
  data: LeadSource[];
  links: any[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

const props = defineProps<{
  leadSources: LeadSourcePagination;
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    is_active?: boolean;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Lead Sources', href: route('admin.lead-sources.index') },
]

console.log('Lead Sources Props:', props.leadSources);

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)
const isActive = ref(props.filters.is_active || '')

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage, isActive], debounce(() => {
  const params: Record<string, string | number | boolean> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
    is_active: isActive.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.lead-sources.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this lead source?')) {
    router.delete(route('admin.lead-sources.destroy', id))
  }
}

function printCurrentView() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog. Please check your browser settings or try again.');
    }
  }, 100);
}

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

function toggleStatus(id: number) {
  router.put(route('admin.lead-sources.toggle-status', id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['leadSources'] });
    },
  });
}
</script>

<template>
  <Head title="Lead Sources" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Lead Sources</h1>
          <p class="text-sm text-muted-foreground">Manage all lead sources here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.lead-sources.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Source
          </Link>
          <button @click="printCurrentView" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print Current View
          </button>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search lead sources..."
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 pr-10"
          />
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

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Lead Sources List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('name')">
                Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('category')">
                Category <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('is_active')">
                Active <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="source in leadSources.data" :key="source.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ source.name }}</td>
              <td class="px-6 py-4">{{ source.category ?? '-' }}</td>
              <td class="px-6 py-4">
                <span :class="{
                  'bg-green-100 text-green-800': source.is_active,
                  'bg-red-100 text-red-800': !source.is_active,
                }" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ source.is_active ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <button @click="toggleStatus(source.id)" :title="source.is_active ? 'Deactivate' : 'Activate'"
                    :class="{
                      'text-green-600 hover:text-green-800 hover:bg-green-100 dark:hover:bg-green-900': source.is_active,
                      'text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900': !source.is_active,
                    }"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full"
                  >
                    <component :is="source.is_active ? ToggleRight : ToggleLeft" class="w-4 h-4" />
                  </button>
                  <Link
                    :href="route('admin.lead-sources.show', source.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.lead-sources.edit', source.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(source.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="leadSources.data.length === 0">
              <td colspan="4" class="text-center px-6 py-4 text-gray-400">No lead sources found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="leadSources.data.length > 0" :links="leadSources.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>

