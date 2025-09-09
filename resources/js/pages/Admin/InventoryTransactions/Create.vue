<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import Form from './Form.vue'
import FormActions from '@/components/FormActions.vue'

const props = defineProps<{ staffList: Array<any>; inventoryItems: Array<any> }>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Transactions', href: route('admin.inventory-transactions.index') },
  { title: 'Create', href: route('admin.inventory-transactions.create') },
]

const form = useForm({
  item_id: null,
  transaction_type: null,
  quantity: 1,
  performed_by_id: null,
  from_location: '',
  to_location: '',
  notes: '',
})

function submit() {
  form.post(route('admin.inventory-transactions.store'))
}
</script>

<template>
  <Head title="Create Inventory Transaction" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create Inventory Transaction</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Record a new inventory transaction.</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :staff-list="props.staffList" :inventory-items="props.inventoryItems" />
          <FormActions :cancel-href="route('admin.inventory-transactions.index')" submit-text="Create Transaction" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>

