<template>
  <Head :title="`Shared Invoice: ${sharedInvoice.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-4">
              <strong>Invoice Number:</strong> {{ sharedInvoice.invoice ? sharedInvoice.invoice.invoice_number : 'N/A' }}
            </div>
            <div class="mb-4">
              <strong>Partner Name:</strong> {{ sharedInvoice.partner ? sharedInvoice.partner.name : 'N/A' }}
            </div>
            <div class="mb-4">
              <strong>Share Date:</strong> {{ sharedInvoice.share_date }}
            </div>
            <div class="mb-4">
              <strong>Status:</strong> {{ sharedInvoice.status }}
            </div>
            <div class="mb-4">
              <strong>Notes:</strong> {{ sharedInvoice.notes }}
            </div>
            <div class="mb-4">
              <strong>Shared By:</strong> {{ sharedInvoice.shared_by ? sharedInvoice.shared_by.first_name + ' ' + sharedInvoice.shared_by.last_name : 'N/A' }}
            </div>
            <div class="mb-4">
              <strong>Shared At:</strong> {{ new Date(sharedInvoice.created_at).toLocaleString() }}
            </div>

            <div class="flex justify-end mt-6 gap-2">
              <Link :href="route('admin.shared-invoices.edit', sharedInvoice.id)"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit">
                <Edit3 class="w-4 h-4" />
              </Link>
              <button @click="deleteInvoice"
                      class="inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-red-100 dark:hover:bg-red-900 text-red-600"
                      title="Delete">
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Edit3, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  sharedInvoice: Object,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Shared Invoices', href: route('admin.shared-invoices.index') },
  { title: `Show: #${props.sharedInvoice?.id ?? ''}`, href: '#' },
]

const form = useForm({})

const deleteInvoice = () => {
  if (confirm('Are you sure you want to delete this shared invoice?')) {
    form.delete(route('admin.shared-invoices.destroy', props.sharedInvoice.id))
  }
}
</script>
