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

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">Invoice Number</th>
              <th class="px-6 py-3">Partner Name</th>
              <th class="px-6 py-3">Share Date</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Shared By</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in sharedInvoices.data" :key="invoice.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ invoice.id }}</td>
              <td class="px-6 py-4">{{ invoice.invoice ? invoice.invoice.invoice_number : 'N/A' }}</td>
              <td class="px-6 py-4">{{ invoice.partner ? invoice.partner.name : 'N/A' }}</td>
              <td class="px-6 py-4">{{ invoice.share_date }}</td>
              <td class="px-6 py-4">{{ invoice.status }}</td>
              <td class="px-6 py-4">{{ invoice.shared_by ? invoice.shared_by.first_name + ' ' + invoice.shared_by.last_name : 'N/A' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.shared-invoices.show', invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Details">
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link :href="route('admin.shared-invoices.edit', invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
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
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Edit3, Trash2, Eye, Printer } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { useExport } from '@/composables/useExport'

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

const deleteInvoice = (id) => {
  if (confirm('Are you sure you want to delete this shared invoice?')) {
    form.delete(route('admin.shared-invoices.destroy', id))
  }
}
</script>
