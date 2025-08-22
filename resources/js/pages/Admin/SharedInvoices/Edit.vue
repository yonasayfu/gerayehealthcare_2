<template>
  <Head title="Edit Shared Invoice" />

  <AppLayout :breadcrumbs="breadcrumbs">
<<<<<<< HEAD
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
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.shared-invoices.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
=======
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">
>>>>>>> yonas/feature/modern-refacotr

      <div class="flex items-start justify-between p-5 border-b rounded-t">
        <h3 class="text-xl font-semibold">
          Edit Shared Invoice
        </h3>
        <Link :href="route('admin.shared-invoices.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </Link>
      </div>

      <div class="p-6 space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
import { Head, Link } from '@inertiajs/vue3'
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
