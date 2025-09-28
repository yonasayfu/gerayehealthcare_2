<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Printer, ArrowUpDown, Eye, Edit3, Trash2, Search, Plus } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import { useExport } from '@/composables/useExport'
import ConfirmModal from '@/components/ConfirmModal.vue'

interface MedicalDocumentFilters {
  search?: string
  sort?: string
  direction?: 'asc' | 'desc'
  per_page?: number
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Medical Documents', href: route('admin.medical-documents.index') },
]

const props = defineProps<{
  medicalDocuments: {
    data: Array<any>
    links: any[]
    current_page: number
    per_page: number
    total?: number
    from?: number
    to?: number
  }
  filters: MedicalDocumentFilters
}>()

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)

const { exportData, printCurrentView } = useExport({ routeName: 'admin.medical-documents', filters: props.filters })

const formattedGeneratedDate = computed(() => format(new Date(), 'PPP p'))

watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  }
  if (sortField.value) params.sort = sortField.value

  router.get(route('admin.medical-documents.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

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
  router.delete(route('admin.medical-documents.destroy', pendingDeleteId.value), {
    preserveScroll: true,
    onFinish: () => {
      showConfirm.value = false
      pendingDeleteId.value = null
    },
  })
}
</script>

<template>
  <Head title="Medical Documents" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <!-- Liquid glass header -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="print:hidden">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Medical Documents</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">Manage uploaded medical documents</p>
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.medical-documents.create')" class="btn-glass">
              <Plus class="icon" />
              <span class="hidden sm:inline">Add Document</span>
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
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search documents..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

       <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
              <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-gray-400 text-white sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-700 dark:border-gray-700">

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
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Medical Documents (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('title')">
                Title <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('document_type')">
                Type <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('document_date')">
                Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(doc, index) in medicalDocuments.data" :key="doc.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ ((medicalDocuments.current_page - 1) * medicalDocuments.per_page) + index + 1 }}</td>
              <td class="px-6 py-4">{{ doc.title || '-' }}</td>
              <td class="px-6 py-4">{{ doc.document_type }}</td>
              <td class="px-6 py-4">{{ doc.document_date || '-' }}</td>
              <td class="px-6 py-4">{{ doc.patient?.full_name || doc.patient?.id || '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.medical-documents.show', doc.id)" class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700" title="View">
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link :href="route('admin.medical-documents.edit', doc.id)" class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="confirmDelete(doc.id)" class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="medicalDocuments.data.length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No medical documents found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6 space-y-2 print:hidden">
        <div class="flex justify-center">
          <Pagination v-if="medicalDocuments.data.length > 0" :links="medicalDocuments.links" />
        </div>
        <p v-if="medicalDocuments.total" class="text-center text-sm text-gray-600 dark:text-gray-300">
          Showing {{ medicalDocuments.from || 0 }}–{{ medicalDocuments.to || 0 }} of {{ medicalDocuments.total }}
        </p>
      </div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
        <hr class="my-2 border-gray-300">
        <p>Document Generated: {{ formattedGeneratedDate }}</p>
      </div>

      <!-- Delete Confirmation Modal -->
      <ConfirmModal
        :open="showConfirm"
        title="Delete Document"
        message="Are you sure you want to delete this document? This action cannot be undone."
        confirm-text="Delete"
        cancel-text="Cancel"
        id-suffix="medical-docs-delete"
        @update:open="v => (showConfirm = v)"
        @confirm="proceedDelete"
        @cancel="cancelDelete"
      />

    </div>
  </AppLayout>
</template>
