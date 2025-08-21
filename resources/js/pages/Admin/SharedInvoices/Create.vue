<template>
  <Head title="Create Shared Invoice" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

      <div class="flex items-start justify-between p-5 border-b rounded-t">
        <h3 class="text-xl font-semibold">
          Create Shared Invoice
        </h3>
        <Link :href="route('admin.shared-invoices.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </Link>
      </div>

      <div class="p-6 space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <SharedInvoiceForm :invoices="invoices" :partners="partners" :staff="staff" />
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import SharedInvoiceForm from '@/components/SharedInvoiceForm.vue'

const props = defineProps({
  invoices: Array,
  partners: Array,
  staff: Array,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Shared Invoices', href: route('admin.shared-invoices.index') },
  { title: 'Create', href: '#' },
]
</script>
