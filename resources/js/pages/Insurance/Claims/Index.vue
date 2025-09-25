<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { confirmDialog } from '@/lib/confirm'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

import type { BreadcrumbItemType } from '@/types';

interface InsuranceClaimPagination {
  current_page: number;
  per_page: number;
  data: any[];
  links: Array<{ url: string | null; label: string; active: boolean }>;
  from: number;
  to: number;
  total: number;
}

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance Claims', href: route('admin.insurance-claims.index') },
]

const props = defineProps<{
  insuranceClaims: InsuranceClaimPagination;
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

const currentDate = computed(() => {
  return format(new Date(), 'PPP');
});

// Add a computed property to calculate the index numbers
const currentIndex = computed(() => {
  return (props.insuranceClaims.current_page - 1) * props.insuranceClaims.per_page;
});

watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.insurance-claims.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Insurance Claim',
    message: 'Are you sure you want to delete this insurance claim?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.insurance-claims.destroy', id))
}

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}


function printCurrentView() {
  setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100);
}
</script>

<template>
  <Head title="Insurance Claims" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="liquidGlass-wrapper print:hidden w-full rounded-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4">
          <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Insurance Claims</h1>
            <p class="text-sm text-muted-foreground">Manage all insurance claims here.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link :href="route('admin.insurance-claims.create')" class="btn-glass btn-glass-sm">+ Add Claim</Link>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search claims..."
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-cyan-600 bg-cyan-600 text-white sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-700 dark:border-gray-700">
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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Insurance Claims List (Current View)</p>
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('claim_status')">
                Claim Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('coverage_amount')">
                Coverage Amount <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('paid_amount')">
                Paid Amount <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('submitted_at')">
                Submitted At <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('processed_at')">
                Processed At <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('payment_due_date')">
                Payment Due Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('payment_method')">
                Payment Method <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('reimbursement_required')">
                Reimbursement Required <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('receipt_number')">
                Receipt Number <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('is_pre_authorized')">
                Is Pre-Authorized <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('pre_authorization_code')">
                Pre-Authorization Code <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(claim, index) in (insuranceClaims.data || [])" :key="(claim && claim.id) ? claim.id : index">
            <tr v-if="claim" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ currentIndex + index + 1 }}</td>
              <td class="px-6 py-4">{{ claim.claim_status ?? '-' }}</td>
              <td class="px-6 py-4">{{ claim.coverage_amount ?? '-' }}</td>
              <td class="px-6 py-4">{{ claim.paid_amount ?? '-' }}</td>
              <td class="px-6 py-4">{{ claim.submitted_at ? format(new Date(claim.submitted_at), 'PPP') : '-' }}</td>
              <td class="px-6 py-4">{{ claim.processed_at ? format(new Date(claim.processed_at), 'PPP') : '-' }}</td>
              <td class="px-6 py-4">{{ claim.payment_due_date ? format(new Date(claim.payment_due_date), 'PPP') : '-' }}</td>
              <td class="px-6 py-4">{{ claim.payment_method ?? '-' }}</td>
              <td class="px-6 py-4">{{ claim.reimbursement_required ? 'Yes' : 'No' }}</td>
              <td class="px-6 py-4">{{ claim.receipt_number ?? '-' }}</td>
              <td class="px-6 py-4">{{ claim.is_pre_authorized ? 'Yes' : 'No' }}</td>
              <td class="px-6 py-4">{{ claim.pre_authorization_code ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="(claim && claim.id) ? route('admin.insurance-claims.show', claim.id) : '#'"
                    class="btn-icon text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="(claim && claim.id) ? route('admin.insurance-claims.edit', claim.id) : '#'"
                    class="btn-icon text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="(claim && claim.id) && destroy(claim.id)" class="btn-icon text-red-600" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            </template>
            <tr v-if="(insuranceClaims.data && insuranceClaims.data.length === 0)">
              <td colspan="13" class="text-center px-6 py-4 text-gray-400">No insurance claims found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="insuranceClaims.data && insuranceClaims.data.length > 0" :links="insuranceClaims.links" class="mt-6 flex justify-center print:hidden" />
      <p v-if="insuranceClaims.total" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300 print:hidden">
        Showing {{ insuranceClaims.from || 0 }}–{{ insuranceClaims.to || 0 }} of {{ insuranceClaims.total }}
      </p>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
        <p>Printed on: {{ formattedGeneratedDate }}</p>
      </div>

    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page { size: A4 landscape; margin: 0.5cm; }

  /* Hide app chrome */
  .app-sidebar-header, .app-sidebar { display: none !important; }
  body > header, body > nav, [role="banner"], [role="navigation"] { display: none !important; }

  html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }

  .print-header-content { page-break-inside: avoid; }
  .print-logo { display: inline-block; margin: 0 auto 6px auto; max-width: 100%; height: auto; }
  .print-clinic-name { font-size: 16px; margin: 0; }
  .print-document-title { font-size: 12px; margin: 2px 0 0 0; }

  /* Keep table headers and rows intact across pages */
  table { border-collapse: collapse; width: 100%; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }

  hr { display: none !important; }
  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: none !important; }
}
</style>
