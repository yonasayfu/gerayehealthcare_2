<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import Form from './Form.vue'
import FormActions from '@/components/FormActions.vue'

interface StaffOption { id: number; first_name: string; last_name: string }
interface ItemOption { id: number; name: string }
interface InventoryTransaction {
  id: number
  item_id: number | null
  transaction_type: string | null
  quantity: number
  performed_by_id: number | null
  from_location: string | null
  to_location: string | null
  notes: string | null
}

const props = defineProps<{
  inventoryTransaction: InventoryTransaction
  staffList: StaffOption[]
  inventoryItems: ItemOption[]
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Transactions', href: route('admin.inventory-transactions.index') },
  { title: 'Edit', href: route('admin.inventory-transactions.edit', props.inventoryTransaction?.id) },
]

const form = useForm({
  item_id: props.inventoryTransaction?.item_id ?? null,
  transaction_type: props.inventoryTransaction?.transaction_type ?? null,
  quantity: Number(props.inventoryTransaction?.quantity ?? 1),
  performed_by_id: props.inventoryTransaction?.performed_by_id ?? null,
  from_location: props.inventoryTransaction?.from_location ?? '',
  to_location: props.inventoryTransaction?.to_location ?? '',
  notes: props.inventoryTransaction?.notes ?? '',
})

function submit() {
  form.put(route('admin.inventory-transactions.update', props.inventoryTransaction.id))
}
</script>

<template>
  <Head title="Edit Inventory Transaction" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Inventory Transaction</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update inventory transaction details below.</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <FormActions :cancel-href="route('admin.inventory-transactions.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
