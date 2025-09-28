<template>
  <Head title="Edit Shared Invoice" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Shared Invoice</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update shared invoice information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <div class="space-y-4">
          <SharedInvoiceForm
            :sharedInvoice="sharedInvoice"
            :invoices="invoices"
            :partners="partners"
            :staff="staff"
            editMode
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import SharedInvoiceForm from '@/components/SharedInvoiceForm.vue'

const props = defineProps({
  sharedInvoice: Object,
  invoices: Array,
  partners: Array,
  staff: Array,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Shared Invoices', href: route('admin.shared-invoices.index') },
  { title: 'Edit', href: '#' },
]
</script>
