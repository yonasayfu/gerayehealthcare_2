<template>
  <Head title="Referral Documents" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Referral Documents</h1>
          <p class="text-sm text-muted-foreground">Manage all referral documents here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.referral-documents.create')" class="btn btn-primary">
            + Add Document
          </Link>
          <button @click="printAllRecords" :disabled="isProcessing" class="btn btn-info">
            <Printer class="h-4 w-4" /> Print All
          </button>
          <button @click="printCurrent" class="btn btn-dark">
            <Printer class="h-4 w-4" /> Print Current
          </button>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="flex items-center justify-end gap-3 p-3 print:hidden">
          <label for="perPage" class="text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        <!-- Print-only Header -->
        <div class="hidden print:block print-header-content">
          <img :src="getClinicLogo()" :alt="getClinicName()" class="print-logo" />
          <div class="print-clinic-name">{{ getClinicName() }}</div>
          <div class="print-document-title">Referral Documents - Current View</div>
          <hr />
        </div>

        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">Referral</th>
              <th class="px-6 py-3">Document Name</th>
              <th class="px-6 py-3">Document Type</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Uploaded By</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="document in referralDocuments.data" :key="document.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ document.id }}</td>
              <td class="px-6 py-4">{{ document.referral ? document.referral.referred_patient_id : 'N/A' }}</td>
              <td class="px-6 py-4">{{ document.document_name }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-0.5 text-xs">
                  {{ document.document_type || '—' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 px-2 py-0.5 text-xs">
                  {{ document.status }}
                </span>
              </td>
              <td class="px-6 py-4">{{ document.uploaded_by?.full_name || '—' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.referral-documents.show', document.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Details">
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link :href="route('admin.referral-documents.edit', document.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="deleteDocument(document.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!referralDocuments.data || referralDocuments.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400">No referral documents found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="referralDocuments.data && referralDocuments.data.length > 0" :links="referralDocuments.links" class="mt-6 flex justify-center print:hidden" />

      <!-- Print-only Footer -->
      <div class="hidden print:block print-footer">
        <hr />
        <div>{{ getPrintFooterText() }}</div>
      </div>
    </div>
  </AppLayout>
  </template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Edit3, Trash2, Eye, Printer } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { useExport } from '@/composables/useExport'
import { useClinicInfo } from '@/composables/useClinicInfo'

const props = defineProps({
  referralDocuments: Object,
  filters: { type: Object, default: () => ({}) },
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Referral Documents', href: route('admin.referral-documents.index') },
]

const { printAllRecords, isProcessing } = useExport({ routeName: 'admin.referral-documents', filters: props.filters || {} })
const { getClinicName, getClinicLogo, getPrintFooterText } = useClinicInfo()

const form = useForm({})

// Per-page selector state and watcher
const perPage = ref(props.filters?.per_page || 5)

watch(perPage, (val) => {
  router.get(route('admin.referral-documents.index'), { per_page: val }, {
    preserveState: true,
    replace: true,
  })
})

const deleteDocument = (id) => {
  if (confirm('Are you sure you want to delete this referral document?')) {
    form.delete(route('admin.referral-documents.destroy', id))
  }
}

const printCurrent = () => {
  window.print()
}
</script>
