<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { useExport } from '@/Composables/useExport';

interface CampaignContent {
  id: number;
  title: string;
  content_type: string;
  status: string;
  scheduled_post_date: string;
  actual_post_date: string;
  campaign: { campaign_name: string };
  platform: { name: string };
}

interface CampaignContentPagination {
  data: CampaignContent[];
  links: any[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

const props = defineProps<{
  campaignContents: CampaignContentPagination;
  campaigns: any[];
  platforms: any[];
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
    campaign_id?: number;
    platform_id?: number;
    content_type?: string;
    status?: string;
    scheduled_post_date_start?: string;
    scheduled_post_date_end?: string;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Campaign Contents', href: route('admin.campaign-contents.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)
const campaignId = ref(props.filters.campaign_id || '')
const platformId = ref(props.filters.platform_id || '')
const contentType = ref(props.filters.content_type || '')
const status = ref(props.filters.status || '')
const scheduledPostDateStart = ref(props.filters.scheduled_post_date_start || '')
const scheduledPostDateEnd = ref(props.filters.scheduled_post_date_end || '')

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

// Index offset like Patient module
const currentIndex = computed(() => {
  return (props.campaignContents.current_page - 1) * props.campaignContents.per_page;
});

watch([search, sortField, sortDirection, perPage, campaignId, platformId, contentType, status, scheduledPostDateStart, scheduledPostDateEnd], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
    campaign_id: campaignId.value,
    platform_id: platformId.value,
    content_type: contentType.value,
    status: status.value,
    scheduled_post_date_start: scheduledPostDateStart.value,
    scheduled_post_date_end: scheduledPostDateEnd.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.campaign-contents.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this campaign content?')) {
    router.delete(route('admin.campaign-contents.destroy', id))
  }
}

const { exportData } = useExport({ routeName: 'admin.campaign-contents', filters: props.filters });

function printCurrentView() {
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
  <Head title="Campaign Contents" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div class="flex-grow min-w-0">
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Campaign Contents Management</h1>
          <p class="text-sm text-muted-foreground">Manage all campaign contents here, including creation, editing, and deletion.</p>
        </div>
        <div class="flex-shrink-0 flex flex-wrap gap-2">
          <Link :href="route('admin.campaign-contents.create')" class="btn btn-primary">
            + Add Content
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
            placeholder="Search contents..."
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 pr-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>
        <div class="flex gap-4">
          <select v-model="campaignId" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="">All Campaigns</option>
            <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
          </select>
          <select v-model="platformId" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="">All Platforms</option>
            <option v-for="platform in platforms" :key="platform.id" :value="platform.id">{{ platform.name }}</option>
          </select>
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Campaign Contents List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('title')">
                Title <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('campaign_id')">
                Campaign <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('platform_id')">
                Platform <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('content_type')">
                Type <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('scheduled_post_date')">
                Scheduled Post Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(content, index) in campaignContents.data" :key="content.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ currentIndex + index + 1 }}</td>
              <td class="px-6 py-4">{{ content.title }}</td>
              <td class="px-6 py-4">{{ content.campaign?.campaign_name ?? '-' }}</td>
              <td class="px-6 py-4">{{ content.platform?.name ?? '-' }}</td>
              <td class="px-6 py-4">{{ content.content_type ?? '-' }}</td>
              <td class="px-6 py-4">{{ content.status ?? '-' }}</td>
              <td class="px-6 py-4">{{ content.scheduled_post_date ? format(new Date(content.scheduled_post_date), 'PPP p') : '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.campaign-contents.show', content.id)"
                    class="btn-icon text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.campaign-contents.edit', content.id)"
                    class="btn-icon text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(content.id)" class="btn-icon text-red-600" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="campaignContents.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400">No campaign contents found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="campaignContents.data.length > 0" :links="campaignContents.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>