<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { confirmDialog } from '@/lib/confirm'

const props = defineProps<{ partners: any; filters: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Partners', href: '/dashboard/partners' },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage], debounce(() => {
  router.get('/dashboard/partners', {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 500))

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Partner',
    message: 'Are you sure you want to delete this partner?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.partners.destroy', id), {
    preserveScroll: true,
  })
}

function exportCsv() {
  window.open(route('partners.export', { type: 'csv' }), '_blank');
}

function printCurrentView() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog for current view. Please check your browser settings or try again.');
    }
  }, 100); // Small delay for reliability
}

const printAllPartners = () => {
  window.open(route('admin.partners.printAll', { preview: true }), '_blank');
};

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
  <Head title="Partners" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Partners</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage partners</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.partners.create')" class="btn-glass">
              <span>Add Partner</span>
            </Link>
            <button @click="exportCsv()" class="btn-glass btn-glass-sm">
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
            placeholder="Search partners..."
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Partner List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('name')">
                Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('type')">
                Type <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Contact Person</th>
              <th class="px-6 py-3">Email</th>
              <th class="px-6 py-3">Phone</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('engagement_status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(partner, i) in partners.data" :key="partner.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ (partners.from ?? 1) + i }}</td>
              <td class="px-6 py-4">{{ partner.name }}</td>
              <td class="px-6 py-4">{{ partner.type }}</td>
              <td class="px-6 py-4">{{ partner.contact_person ?? '-' }}</td>
              <td class="px-6 py-4">{{ partner.email ?? '-' }}</td>
              <td class="px-6 py-4">{{ partner.phone ?? '-' }}</td>
              <td class="px-6 py-4">{{ partner.engagement_status }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.partners.show', partner.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.partners.edit', partner.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(partner.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="partners.data.length === 0">
              <td colspan="8" class="text-center px-6 py-4 text-gray-400">No partners found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      
      <Pagination v-if="partners.data.length > 0" :links="partners.links" class="mt-6 flex justify-center print:hidden" />
      <p v-if="partners.total" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300 print:hidden">
        Showing {{ partners.from || 0 }}–{{ partners.to || 0 }} of {{ partners.total }}
      </p>

      <!-- spacer to prevent content under footer when printing -->
      <div class="hidden print:block h-24"></div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> 
      </div>

    </div>
  </AppLayout>
</template>

<style>
@media print {
  .print-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding-bottom: 8px;
  }
}
</style>