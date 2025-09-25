
<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Edit3, Trash2, Eye, Printer, ArrowUpDown, Download, Share2 } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { useExport } from '@/composables/useExport'
import { confirmDialog } from '@/lib/confirm'
import { useTableFilters } from '@/composables/useTableFilters'

const props = defineProps({
  sharedInvoices: Object,
  filters: { type: Object, default: () => ({}) },
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Shared Invoices', href: route('admin.shared-invoices.index') },
]

const { printCurrentView, printAllRecords, isProcessing } = useExport({ routeName: 'admin.shared-invoices', filters: props.filters || {} })

const form = useForm({})

// Filters via composable
const { search, perPage, toggleSort } = useTableFilters({
  routeName: 'admin.shared-invoices.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort,
    direction: props.filters?.direction ?? 'desc',
    per_page: props.filters?.per_page ?? 5,
  }
})

const statusFilter = ref(props.filters?.status || 'All')

watch([statusFilter, search, perPage], ([status]) => {
  router.get(route('admin.shared-invoices.index'), {
    search: search.value,
    per_page: perPage.value,
    sort: props.filters?.sort,
    direction: props.filters?.direction ?? 'desc',
    status,
  }, { preserveState: true, replace: true })
})

async function deleteInvoice(id) {
  const ok = await confirmDialog({
    title: 'Delete Shared Invoice',
    message: 'Are you sure you want to delete this shared invoice?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  form.delete(route('admin.shared-invoices.destroy', id))
}

function exportCsv() { window.open(route('admin.shared-invoices.export', { type: 'csv', search: search || undefined }), '_blank') }

async function copyShareLink(id) {
  try {
    const res = await fetch(route('admin.shared-invoices.shareLink', id), { headers: { 'Accept': 'application/json' } })
    const data = await res.json()
    await navigator.clipboard.writeText(data.url)
    alert('Share link copied to clipboard!')
  } catch (e) {
    alert('Failed to generate share link.')
  }
}

async function rotateLink(id) {
  if (!confirm('Rotate link? Existing public URL will stop working.')) return
  try {
    const csrfToken = window.Laravel?.csrfToken || 
      document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || 
      ''
    await fetch(route('admin.shared-invoices.rotateShare', id), { 
      method: 'POST', 
      headers: { 
        'X-CSRF-TOKEN': csrfToken 
      } 
    })
    alert('Link rotated.')
  } catch (e) {
    alert('Failed to rotate link.')
  }
}

async function expireLink(id) {
  if (!confirm('Expire link now? Public access will stop immediately.')) return
  try {
    const csrfToken = window.Laravel?.csrfToken || 
      document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || 
      ''
    await fetch(route('admin.shared-invoices.expireShare', id), { 
      method: 'POST', 
      headers: { 
        'X-CSRF-TOKEN': csrfToken 
      } 
    })
    alert('Link expired.')
  } catch (e) {
    alert('Failed to expire link.')
  }
}

async function setPin(id) {
  const pin = prompt('Enter PIN (leave blank to clear):')
  if (pin === null) return
  try {
    const csrfToken = window.Laravel?.csrfToken || 
      document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || 
      ''
    await fetch(route('admin.shared-invoices.setPin', id), { 
      method: 'POST', 
      headers: { 
        'Content-Type': 'application/json', 
        'X-CSRF-TOKEN': csrfToken 
      }, 
      body: JSON.stringify({ pin }) 
    })
    alert(pin ? 'PIN set.' : 'PIN cleared.')
  } catch (e) {
    alert('Failed to set PIN.')
  }
}
</script>
<template>
  <Head title="Shared Invoices" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Shared Invoices</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage shared invoices</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.shared-invoices.create')" class="btn-glass">
              <span>Add Shared Invoice</span>
            </Link>
            <button :disabled="isProcessing" @click="exportCsv()" class="btn-glass btn-glass-sm disabled:opacity-50">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button :disabled="isProcessing" @click="printCurrentView" class="btn-glass btn-glass-sm disabled:opacity-50">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
            <button :disabled="isProcessing" @click="printAllRecords" class="btn-glass btn-glass-sm disabled:opacity-50">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print All</span>
            </button>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">Shared Invoices (Current View)</p>
        </div>
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-3 print:hidden">
          <div class="relative w-full md:w-1/3">
            <input
              type="text"
              v-model="search"
              placeholder="Search shared invoices..."
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-3 py-2.5"
            />
          </div>
          <div class="flex items-center gap-2">
            <label for="statusFilter" class="text-sm text-gray-700 dark:text-gray-300">Status:</label>
            <select id="statusFilter" v-model="statusFilter" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
              <option>All</option>
              <option>Sent</option>
              <option>Viewed</option>
              <option>Paid</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <label for="perPage" class="text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
            <select id="perPage" v-model="perPage" class="rounded-md border-cyan-600 bg-cyan-600 text-white sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-700 dark:border-gray-700">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('id')">
                ID <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3">Invoice Number</th>
              <th class="px-6 py-3">Partner Name</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('share_views')">
                Views <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('share_date')">
                Share Date <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3">Shared By</th>
              <th class="px-6 py-3 cursor-pointer print:hidden" @click="toggleSort('created_at')">
                Created <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in sharedInvoices.data" :key="invoice.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ invoice.id }}</td>
              <td class="px-6 py-4">{{ invoice.invoice ? invoice.invoice.invoice_number : 'N/A' }}</td>
              <td class="px-6 py-4">{{ invoice.partner ? invoice.partner.name : 'N/A' }}</td>
              <td class="px-6 py-4">{{ invoice.share_views ?? 0 }}</td>
              <td class="px-6 py-4">{{ invoice.share_date }}</td>
              <td class="px-6 py-4">
                <span :class="[
                  'px-2 py-1 rounded-full text-xs font-semibold',
                  invoice.status === 'Sent' ? 'bg-blue-100 text-blue-800' :
                  invoice.status === 'Viewed' ? 'bg-amber-100 text-amber-800' :
                  invoice.status === 'Paid' ? 'bg-green-100 text-green-800' :
                  'bg-gray-100 text-gray-800'
                ]">{{ invoice.status }}</span>
              </td>
              <td class="px-6 py-4">{{ (invoice.shared_by ? (invoice.shared_by.first_name + ' ' + invoice.shared_by.last_name) : (invoice.sharedBy ? (invoice.sharedBy.first_name + ' ' + invoice.sharedBy.last_name) : 'N/A')) }}</td>
              <td class="px-6 py-4 print:hidden">{{ invoice.created_at }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.shared-invoices.show', invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Details">
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link :href="route('admin.shared-invoices.edit', invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <a :href="route('admin.shared-invoices.printSingle', invoice.id)" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Print Single">
                    <Printer class="w-4 h-4" />
                  </a>
                  <button @click="copyShareLink(invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Copy Share Link">
                    <Share2 class="w-4 h-4" />
                  </button>
                  <button @click="() => rotateLink(invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Rotate Link">
                    R
                  </button>
                  <button @click="() => expireLink(invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Expire Link">
                    X
                  </button>
                  <button @click="() => setPin(invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Set/Update PIN">
                    PIN
                  </button>
                  <button @click="deleteInvoice(invoice.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!sharedInvoices.data || sharedInvoices.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400">No shared invoices found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="sharedInvoices.data && sharedInvoices.data.length > 0" :links="sharedInvoices.links" class="mt-6 flex justify-center print:hidden" />

      <!-- Print-only footer -->
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ new Date().toLocaleString() }}</p>
      </div>
    </div>
  </AppLayout>
</template>


<style>
@media print {
  @page {
    size: A4 landscape;
    margin: 0.5cm;
  }
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
  }
  .hidden.print\:block { display: block !important; }
  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
  }
  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
  .print-clinic-name { font-size: 1.8rem !important; margin-bottom: 0.2rem !important; line-height: 1.2 !important; }
  .print-document-title { font-size: 0.9rem !important; color: #555 !important; }
}
</style>
