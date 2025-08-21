<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

const props = defineProps<{ partnerCommissions: any; filters: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Partner Commissions', href: '/dashboard/partner-commissions' },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage], debounce(() => {
  router.get('/dashboard/partner-commissions', {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this partner commission?')) {
    router.delete(route('admin.partner-commissions.destroy', id), {
      preserveScroll: true,
    })
  }
}

function exportCsv() {
  window.open(route('admin.partner-commissions.export', { type: 'csv' }), '_blank');
}

function printCurrentView() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog for current view. Please check your browser settings or try again.');
    }
  }, 100);
}

const printAllPartnerCommissions = () => {
  window.open(route('admin.partner-commissions.printAll', { preview: true }), '_blank');
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
  <Head title="Partner Commissions" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Partner Commissions</h1>
          <p class="text-sm text-muted-foreground">Manage all partner commissions here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.partner-commissions.create')" class="btn btn-primary">
            + Add Commission
          </Link>
          <button @click="exportCsv()" class="btn btn-success">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="printCurrentView" class="btn btn-dark">
            <Printer class="h-4 w-4" /> Print Current
          </button>
          <button @click="printAllPartnerCommissions" class="btn btn-info">
            <Printer class="h-4 w-4" /> Print All
          </button>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search by status, calculation date..."
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full py-2.5 pl-3 pr-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Partner Commission List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3">Agreement</th>
              <th class="px-6 py-3">Referral Date</th>
              <th class="px-6 py-3">Invoice #</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('calculation_date')">
                Calculation Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Payout Date</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(commission, i) in partnerCommissions.data" :key="commission.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ (partnerCommissions.from ?? 1) + i }}</td>
              <td class="px-6 py-4">{{ commission.agreement?.agreement_title ?? '-' }}</td>
              <td class="px-6 py-4">{{ commission.referral?.referral_date ?? '-' }}</td>
              <td class="px-6 py-4">{{ commission.invoice?.invoice_number ?? '-' }}</td>
              <td class="px-6 py-4">{{ commission.commission_amount }}</td>
              <td class="px-6 py-4">{{ commission.calculation_date }}</td>
              <td class="px-6 py-4">{{ commission.payout_date ?? '-' }}</td>
              <td class="px-6 py-4">{{ commission.status }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.partner-commissions.show', commission.id)" class="btn-icon text-indigo-600" title="View Details">
  <Eye class="w-4 h-4" />
</Link>

                  <Link :href="route('admin.partner-commissions.edit', commission.id)" class="btn-icon text-blue-600" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(commission.id)" class="btn-icon text-red-600 hover:text-red-800" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="partnerCommissions.data.length === 0">
              <td colspan="9" class="text-center px-6 py-4 text-gray-400">No partner commissions found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      
      <Pagination v-if="partnerCommissions.data.length > 0" :links="partnerCommissions.links" class="mt-6 flex justify-center print:hidden" />
      <p v-if="partnerCommissions.total" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300 print:hidden">
        Showing {{ partnerCommissions.from || 0 }}–{{ partnerCommissions.to || 0 }} of {{ partnerCommissions.total }}
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