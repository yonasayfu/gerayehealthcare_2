<template>
  <Head :title="`Shared Invoice: ${sharedInvoice.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

      <!-- Header with title and close/back button (hidden in print) -->
      <div class="flex items-start justify-between p-5 border-b rounded-t print:hidden">
        <h3 class="text-xl font-semibold">
          Shared Invoice #{{ sharedInvoice.id }}
        </h3>
        <Link :href="route('admin.shared-invoices.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" title="Close">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </Link>
      </div>

      <div class="p-6 space-y-6">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8">

          <!-- Print-only header -->
          <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Shared Invoice Record</p>
            <hr class="my-3 border-gray-300 print:my-2">
          </div>

          <!-- Shared Invoice Summary -->
          <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
              <div>
                <p class="text-sm text-muted-foreground">Invoice Number:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ sharedInvoice.invoice ? sharedInvoice.invoice.invoice_number : 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Partner Name:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ sharedInvoice.partner ? sharedInvoice.partner.name : 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Share Date:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ sharedInvoice.share_date }}</p>
              </div>
            </div>
          </div>

          <!-- Status & Notes -->
          <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Status & Notes</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
              <div>
                <p class="text-sm text-muted-foreground">Status:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ sharedInvoice.status }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Notes:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ sharedInvoice.notes || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Shared By & Timestamps -->
          <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Audit</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
              <div>
                <p class="text-sm text-muted-foreground">Shared By:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ sharedInvoice.shared_by ? sharedInvoice.shared_by.first_name + ' ' + sharedInvoice.shared_by.last_name : 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Shared At:</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ new Date(sharedInvoice.created_at).toLocaleString() }}</p>
              </div>
            </div>
          </div>
          <!-- Print-only footer -->
          <div class="hidden print:block h-16"></div>
          <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print:text-xs print-footer">
            <hr class="my-2 border-gray-300">
            <p>Printed on: {{ new Date().toLocaleString() }}</p>
          </div>

        </div>
      </div>

      <!-- Footer actions -->
      <div class="p-6 border-t border-gray-200 rounded-b print:hidden">
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.shared-invoices.index')" class="btn btn-outline">
            Back to List
          </Link>
          <Link :href="route('admin.shared-invoices.edit', sharedInvoice.id)" class="btn btn-primary">
            Edit
          </Link>
          <button @click="printCurrentView" class="btn btn-dark">
            <Printer class="h-4 w-4" /> Print Current
          </button>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Printer } from 'lucide-vue-next'
import { useExport } from '@/composables/useExport'

const props = defineProps({
  sharedInvoice: Object,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Shared Invoices', href: route('admin.shared-invoices.index') },
  { title: `Show: #${props.sharedInvoice?.id ?? ''}`, href: '#' },
]

// Use useExport for consistent print-current behavior (window.print under the hood)
const { printCurrentView } = useExport({ routeName: 'admin.shared-invoices', filters: {} })
</script>

<style>
@media print {
  @page {
    size: A4;
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

  .print-footer {
    position: fixed !important;
    bottom: 0.3cm !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    background: #ffffff !important;
    padding-top: 0.2rem !important;
  }
}
</style>
