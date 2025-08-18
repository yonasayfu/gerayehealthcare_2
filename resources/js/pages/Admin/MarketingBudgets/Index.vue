<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

interface MarketingBudget {
  id: number;
  budget_name: string;
  allocated_amount: number;
  spent_amount: number;
  period_start: string;
  period_end: string;
  status: string;
  campaign: { campaign_name: string };
  platform: { name: string };
}

interface MarketingBudgetPagination {
  data: MarketingBudget[];
  links: any[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

const props = defineProps<{
  marketingBudgets: MarketingBudgetPagination;
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    campaign_id?: number;
    platform_id?: number;
    status?: string;
    period_start?: string;
    period_end?: string;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Budgets', href: route('admin.marketing-budgets.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)
const campaignId = ref(props.filters.campaign_id || '')
const platformId = ref(props.filters.platform_id || '')
const status = ref(props.filters.status || '')
const periodStart = ref(props.filters.period_start || '')
const periodEnd = ref(props.filters.period_end || '')

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage, campaignId, platformId, status, periodStart, periodEnd], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
    campaign_id: campaignId.value,
    platform_id: platformId.value,
    status: status.value,
    period_start: periodStart.value,
    period_end: periodEnd.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.marketing-budgets.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this marketing budget?')) {
    router.delete(route('admin.marketing-budgets.destroy', id))
  }
}

import { useExport } from '@/composables/useExport';

const { exportData } = useExport({ routeName: 'admin.marketing-budgets', filters: props.filters });

function printCurrentView() {
  // Mirror Patients module: print the current index view with print-only styles
  setTimeout(() => window.print(), 50);
}

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}
</script>

<template>
  <Head title="Marketing Budgets" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Marketing Budgets</h1>
          <p class="text-sm text-muted-foreground">Manage all marketing budgets here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.marketing-budgets.create')" class="btn btn-primary">
            + Add Budget
          </Link>
          <button @click="exportData('csv')" class="btn btn-success">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="printCurrentView" class="btn btn-dark">
            <Printer class="h-4 w-4" /> Print Current
          </button>
          
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search budgets..."
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Marketing Budgets List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('budget_name')">
                Budget Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('campaign_id')">
                Campaign <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('platform_id')">
                Platform <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('allocated_amount')">
                Allocated <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('spent_amount')">
                Spent <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('period_start')">
                Start Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('period_end')">
                End Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="budget in marketingBudgets.data" :key="budget.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ budget.budget_name }}</td>
              <td class="px-6 py-4">{{ budget.campaign?.campaign_name ?? '-' }}</td>
              <td class="px-6 py-4">{{ budget.platform?.name ?? '-' }}</td>
              <td class="px-6 py-4">{{ budget.allocated_amount }}</td>
              <td class="px-6 py-4">{{ budget.spent_amount }}</td>
              <td class="px-6 py-4">{{ budget.period_start ? format(new Date(budget.period_start), 'PPP') : '-' }}</td>
              <td class="px-6 py-4">{{ budget.period_end ? format(new Date(budget.period_end), 'PPP') : '-' }}</td>
              <td class="px-6 py-4">{{ budget.status }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.marketing-budgets.show', budget.id)"
                    class="btn-icon text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.marketing-budgets.edit', budget.id)"
                    class="btn-icon text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(budget.id)" class="btn-icon text-red-600" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="marketingBudgets.data.length === 0">
              <td colspan="9" class="text-center px-6 py-4 text-gray-400">No marketing budgets found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="marketingBudgets.data.length > 0" :links="marketingBudgets.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>