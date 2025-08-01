<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

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

function exportData(type: 'csv' | 'pdf') {
  window.open(route('admin.campaign-contents.export', { type }), '_blank');
}

function printAllContents() {
  window.open(route('admin.campaign-contents.printAll'), '_blank');
}

function printCurrentView() {
    const params = {
        search: search.value,
        sort: sortField.value,
        direction: sortDirection.value,
        campaign_id: campaignId.value,
        platform_id: platformId.value,
        content_type: contentType.value,
        status: status.value,
        scheduled_post_date_start: scheduledPostDateStart.value,
        scheduled_post_date_end: scheduledPostDateEnd.value,
    };
    const url = route('admin.campaign-contents.printCurrent', params);
    window.open(url, '_blank');
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
          <Link :href="route('admin.campaign-contents.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Content
          </Link>
          <button @click="exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printAllContents" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print All
          </button>
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
            <option value="">5</option>
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
            <tr v-for="content in campaignContents.data" :key="content.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
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
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.campaign-contents.edit', content.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(content.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
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

<style>
/* Print-specific styles for Index.vue (Print Current View) */
@media print {
  @page {
    size: A4 landscape; /* Landscape is often better for tables */
    margin: 1cm; /* Increased margin for more room */
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
    font-size: 10pt; /* Base font size for print */
  }

  /* Hide elements */
  .print\:hidden {
    display: none !important;
  }

  /* Specific styles for the print header content (logo and clinic name) */
  .print-header-content {
      display: block !important; /* Show header */
      text-align: center;
      padding-top: 0.5cm;
      padding-bottom: 0.5cm;
      margin-bottom: 1cm; /* More space below header */
  }
  .print-logo {
      max-width: 180px; /* Slightly larger logo */
      max-height: 60px; /* Slightly larger logo */
      margin-bottom: 0.5rem;
      display: block;
      margin-left: auto;
      margin-right: auto;
  }
  .print-clinic-name {
      font-size: 1.8rem !important; /* Larger clinic name */
      margin-bottom: 0.3rem !important;
      line-height: 1.2 !important;
      font-weight: bold;
  }
  .print-document-title {
      font-size: 1rem !important; /* Larger document title */
      color: #333 !important;
  }
  hr { border-color: #999 !important; }

  /* Main content container adjustments */
  .space-y-6.p-6 {
    padding: 0 !important;
    margin: 0 !important;
    height: auto !important;
    min-height: auto !important;
  }

  /* Table specific print styles */
  .overflow-x-auto.bg-white.dark\:bg-gray-900.shadow.rounded-lg {
    box-shadow: none !important;
    border-radius: 0 !important;
    background-color: transparent !important; /* No background color */
    overflow: visible !important; /* Essential to prevent clipping */
    padding: 0; /* Remove inner padding, controlled by page margin */
    page-break-after: auto !important;
  }

  .print-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 9pt; /* Adjust table body font size */
    table-layout: fixed; /* Helps with column width distribution */
  }

  .print-table-header {
    background-color: #e0e0e0 !important; /* Slightly darker header background */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    text-transform: uppercase !important;
  }

  .print-table th, .print-table td {
    border: 1px solid #bbb !important; /* Darker borders for better visibility */
    padding: 0.5rem 0.75rem !important; /* Increased cell padding */
    color: #000 !important;
    vertical-align: top !important; /* Align content to top of cell */
    word-break: break-word; /* Allow long words to break */
  }

  .print-table th {
    font-weight: bold !important;
    font-size: 9pt; /* Header font size */
    white-space: nowrap; /* Keep header text on one line if possible */
  }

  /* Adjust column widths if needed, target by nth-child or specific content */
  .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 20%; } /* Title */
  .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 15%; } /* Campaign */
  .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 15%; } /* Platform */
  .print-table th:nth-child(4), .print-table td:nth-child(4) { width: 10%; }  /* Type */
  .print-table th:nth-child(5), .print-table td:nth-child(5) { width: 10%; } /* Status */
  .print-table th:nth-child(6), .print-table td:nth-child(6) { width: 15%; } /* Scheduled Post Date */


  .print-table tbody tr:nth-child(even) {
    background-color: #f0f0f0 !important; /* Subtle zebra striping */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .print-table tbody tr:last-child {
    border-bottom: 1px solid #bbb !important;
  }
  .print-table-row {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  /* Hide actions column for print */
  .print-table th:last-child,
  .print-table td:last-child {
    display: none !important;
  }

  /* Hide sort arrows on print */
  .print\:hidden {
    display: none !important;
  }

  /* Print Footer */
  .print-footer {
    display: block !important;
    text-align: center;
    position: relative; /* Changed from fixed */
    margin-top: 1.5cm; /* More space above footer */
    font-size: 8pt; /* Smaller footer font */
    color: #444 !important;
  }
  .print-footer hr {
    border-color: #999 !important;
  }
}
</style>