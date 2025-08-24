<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import Form from './Form.vue'

const props = defineProps({
  inventoryTransaction: Object,
  staffList: Array,
  inventoryItems: Array,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Transactions', href: route('admin.inventory-transactions.index') },
  { title: 'Edit', href: route('admin.inventory-transactions.edit', (props as any).inventoryTransaction?.id) },
]

const form = useForm({
  item_id: (props as any).inventoryTransaction?.item_id ?? null,
  transaction_type: (props as any).inventoryTransaction?.transaction_type ?? null,
  quantity: (props as any).inventoryTransaction?.quantity ?? 1,
  performed_by_id: (props as any).inventoryTransaction?.performed_by_id ?? null,
  from_location: (props as any).inventoryTransaction?.from_location ?? '',
  to_location: (props as any).inventoryTransaction?.to_location ?? '',
  notes: (props as any).inventoryTransaction?.notes ?? '',
})

function submit() {
  form.put(route('admin.inventory-transactions.update', (props as any).inventoryTransaction.id))
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

          <!-- Footer actions: Cancel + Save (right aligned) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.inventory-transactions.index')" class="btn-glass btn-glass-sm">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
