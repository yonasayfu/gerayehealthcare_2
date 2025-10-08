<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Printer, ArrowUpDown, Eye, Edit3, Trash2, Search, Plus } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { useExport } from '@/composables/useExport'
import ConfirmModal from '@/components/ConfirmModal.vue'
import { useTableFilters } from '@/composables/useTableFilters'

interface PrescriptionFilters {
  search?: string
  sort?: string
  direction?: 'asc' | 'desc'
  per_page?: number
  status?: string
}

function printWithBrowser() {
  setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100)
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Prescriptions', href: route('admin.prescriptions.index') },
]

const props = defineProps<{
  prescriptions: {
    data: Array<any>
    links: any[]
    current_page: number
    per_page: number
    total?: number
    from?: number
    to?: number
  }
  filters: PrescriptionFilters
}>()

const { search, perPage, sort, direction, toggleSort } = useTableFilters({
  routeName: 'admin.prescriptions.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort,
    direction: props.filters?.direction,
    per_page: props.filters?.per_page ?? props.prescriptions?.per_page ?? 5,
  }
})

// Status filter similar to other modules
const statusFilter = ref(props.filters?.status || 'All')

watch([statusFilter, search, perPage, sort, direction], () => {
  router.get(route('admin.prescriptions.index'), {
    search: search.value,
    per_page: perPage.value,
    sort: sort.value,
    direction: direction.value,
    status: statusFilter.value,
  }, { preserveState: true, replace: true })
})

const { exportData } = useExport({ routeName: 'admin.prescriptions', filters: props.filters })

const formattedGeneratedDate = computed(() => format(new Date(), 'PPP p'))

// URL updates handled by composable

// Delete confirmation state & handlers
const showConfirm = ref(false)
const pendingDeleteId = ref<number | null>(null)

function confirmDelete(id: number) {
  pendingDeleteId.value = id
  showConfirm.value = true
}

function cancelDelete() {
  showConfirm.value = false
  pendingDeleteId.value = null
}

function proceedDelete() {
  if (!pendingDeleteId.value) return
  router.delete(route('admin.prescriptions.destroy', pendingDeleteId.value), {
    preserveScroll: true,
    onFinish: () => {
      showConfirm.value = false
      pendingDeleteId.value = null
    },
  })
}
</script>

<template>
  <Head title="Prescriptions" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <!-- Liquid glass header -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="print:hidden">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Prescriptions</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">Manage prescriptions for patients</p>
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.prescriptions.create')" class="btn-glass">
              <Plus class="icon" />
              <span class="hidden sm:inline">Add Prescription</span>
            </Link>
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printWithBrowser" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search prescriptions..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

        <div class="flex items-center gap-3">
          <div>
            <label for="statusFilter" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Status:</label>
            <select id="statusFilter" v-model="statusFilter" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
              <option>All</option>
              <option>draft</option>
              <option>final</option>
              <option>dispensed</option>
              <option>cancelled</option>
            </select>
          </div>
           
        </div>
       <div>
            <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
            <PerPageSelect v-model="perPage" id="perPage" />
          </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block print-stamp">Official - Exchange Copy</div>
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Prescriptions (Current View)</p>
        </div>

        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('id')">
                # <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('prescribed_date')">
                Prescribed Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Items</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(p, index) in prescriptions.data" :key="p.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ ((prescriptions.current_page - 1) * prescriptions.per_page) + index + 1 }}</td>
              <td class="px-6 py-4">{{ p.patient?.full_name || p.patient?.patient_code || '-' }}</td>
              <td class="px-6 py-4">{{ p.prescribed_date || '-' }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                  'bg-yellow-100 text-yellow-800': p.status === 'draft',
                  'bg-blue-100 text-blue-800': p.status === 'final',
                  'bg-green-100 text-green-800': p.status === 'dispensed',
                  'bg-red-100 text-red-800': p.status === 'cancelled',
                  'bg-gray-100 text-gray-800': !p.status
                }">
                  {{ p.status || '-' }}
                </span>
              </td>
              <td class="px-6 py-4">{{ p.items_count ?? p.items?.length ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.prescriptions.show', p.id)" class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700" title="View">
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link :href="route('admin.prescriptions.edit', p.id)" class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="confirmDelete(p.id)" class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="prescriptions.data.length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No prescriptions found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6 space-y-2 print:hidden">
        <div class="flex justify-center">
          <Pagination v-if="prescriptions.data.length > 0" :links="prescriptions.links" />
        </div>
        <p v-if="prescriptions.total" class="text-center text-sm text-gray-600 dark:text-gray-300">
          Showing {{ prescriptions.from || 0 }}–{{ prescriptions.to || 0 }} of {{ prescriptions.total }}
        </p>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
        <p>Document Generated: {{ formattedGeneratedDate }}</p>
      </div>

      <!-- Delete Confirmation Modal -->
      <ConfirmModal
        :open="showConfirm"
        title="Delete Prescription"
        message="Are you sure you want to delete this prescription? This action cannot be undone."
        confirm-text="Delete"
        cancel-text="Cancel"
        id-suffix="prescriptions-delete"
        @update:open="(v: boolean) => (showConfirm = v)"
        @confirm="proceedDelete"
        @cancel="cancelDelete"
      />

    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page { size: A4 landscape; margin: 0.5cm; }
  .app-sidebar-header, .app-sidebar { display: none !important; }
  body > header, body > nav, [role="banner"], [role="navigation"] { display: none !important; }
  html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
  table { border-collapse: collapse; width: 100%; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }
}
</style>
<style>
/* Professional A5 print layout */
@page {
  size: A5 portrait; /* A4/2 */
  margin: 12mm;
}
@media print {
  html, body { background: #fff !important; }
  .print-header-content { page-break-inside: avoid; }
  .print-logo { display: inline-block; margin: 0 auto 6px auto; max-width: 100%; height: auto; }
  .print-clinic-name { font-size: 16px; margin: 0; }
  .print-document-title { font-size: 12px; margin: 2px 0 0 0; }
  .print-table { font-size: 11px; border-collapse: collapse; }
  .print-table thead { background: #f3f4f6; }
  .print-table th, .print-table td { border: 1px solid #d1d5db; padding: 6px 8px; }
  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: none !important; }
  /* Ensure any hr doesn't render as shadow in print */
  hr { display: none !important; }

  /* Optional text stamp */
  .print-stamp {
    position: fixed;
    top: 10mm;
    right: 10mm;
    z-index: 50;
    font-weight: 700;
    color: #c53030;
    border: 2px solid #c53030;
    padding: 4px 8px;
    transform: rotate(-12deg);
    background: rgba(255,255,255,0.85);
    font-size: 11px;
  }
}
</style>
