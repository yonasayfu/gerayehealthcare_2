<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Edit3, Trash2, Printer, ArrowUpDown, Eye, Search, Download } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

// Define a type for MarketingCampaign (adjust based on your actual model structure)
interface MarketingCampaign {
  id: number;
  campaign_code: string;
  campaign_name: string;
  campaign_type: string;
  start_date: string;
  end_date: string;
  status: string;
  platform: { name: string }; // Assuming platform is eager loaded
  assigned_staff: { full_name: string }; // Assuming assignedStaff is eager loaded
  created_by_staff: { full_name: string }; // Assuming createdByStaff is eager loaded
  // Add other fields as needed
}

// Define a type for pagination data
interface MarketingCampaignPagination {
  data: MarketingCampaign[];
  links: any[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

const props = defineProps<{
  marketingCampaigns: MarketingCampaignPagination;
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    platform_id?: number;
    status?: string;
    campaign_type?: string;
    start_date?: string;
    end_date?: string;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Campaigns', href: route('admin.marketing-campaigns.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)
const platformId = ref(props.filters.platform_id || '')
const status = ref(props.filters.status || '')
const campaignType = ref(props.filters.campaign_type || '')
const startDate = ref(props.filters.start_date || '')
const endDate = ref(props.filters.end_date || '')

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage, platformId, status, campaignType, startDate, endDate], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
    platform_id: platformId.value,
    status: status.value,
    campaign_type: campaignType.value,
    start_date: startDate.value,
    end_date: endDate.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.marketing-campaigns.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

import { confirmDialog } from '@/lib/confirm'
async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Marketing Campaign',
    message: 'Are you sure you want to delete this marketing campaign?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.marketing-campaigns.destroy', id))
}

import { useExport } from '@/composables/useExport';

const { printCurrentView, isProcessing, exportData } = useExport({ routeName: 'admin.marketing-campaigns', filters: props.filters });

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
  <Head title="Marketing Campaigns" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Marketing Campaigns</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage marketing campaigns</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.marketing-campaigns.create')" class="btn-glass">
              <span>Add Marketing Campaign</span>
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

            <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <!-- keep original input size & rounded-lg but wrap with a subtle liquid-glass outer effect -->
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search marketing campaigns..."
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

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Marketing Campaigns List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('campaign_name')">
                Campaign Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('campaign_code')">
                Campaign Code <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('platform_id')">
                Platform <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('campaign_type')">
                Type <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Urgency</th>
              <th class="px-6 py-3">Responsible Staff</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('start_date')">
                Start Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('end_date')">
                End Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="campaign in marketingCampaigns.data" :key="campaign.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ campaign.campaign_name }}</td>
              <td class="px-6 py-4">{{ campaign.campaign_code ?? '-' }}</td>
              <td class="px-6 py-4">{{ campaign.platform?.name ?? '-' }}</td>
              <td class="px-6 py-4">{{ campaign.campaign_type ?? '-' }}</td>
              <td class="px-6 py-4">{{ campaign.status ?? '-' }}</td>
              <td class="px-6 py-4">{{ campaign.urgency ?? '-' }}</td>
              <td class="px-6 py-4">{{ campaign.responsible_staff?.full_name ?? '-' }}</td>
              <td class="px-6 py-4">{{ campaign.start_date ? format(new Date(campaign.start_date), 'PPP') : '-' }}</td>
              <td class="px-6 py-4">{{ campaign.end_date ? format(new Date(campaign.end_date), 'PPP') : '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.marketing-campaigns.show', campaign.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.marketing-campaigns.edit', campaign.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(campaign.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="marketingCampaigns.data.length === 0">
              <td colspan="8" class="text-center px-6 py-4 text-gray-400">No marketing campaigns found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="marketingCampaigns.data.length > 0" :links="marketingCampaigns.links" class="mt-6 flex justify-center print:hidden" />

    </div>
  </AppLayout>
</template>
