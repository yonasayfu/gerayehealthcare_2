<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'

const props = defineProps<{
  staff: { id: number; first_name: string; last_name: string }[],
  prefill?: {
    title?: string | null,
    assigned_to?: number | null,
    due_date?: string | null,
    status?: string | null,
    notes?: string | null,
  },
  returnTo?: string | null,
  inventoryAlertId?: number | null,
}>()

const form = useForm({
  title: props.prefill?.title ?? '',
  assigned_to: props.prefill?.assigned_to ?? null,
  due_date: props.prefill?.due_date ?? '',
  status: props.prefill?.status ?? 'Pending',
  notes: props.prefill?.notes ?? '',
  // Extra meta to support linking back to an InventoryAlert
  inventory_alert_id: props.inventoryAlertId ?? null,
  return_to: props.returnTo ?? null,
})

function submit() {
  form.post(route('admin.task-delegations.store'), {
    preserveScroll: true,
    onSuccess: () => {
      if (props.returnTo) {
        window.location.href = props.returnTo as string
      }
    }
  })
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Task Delegations', href: route('admin.task-delegations.index') },
  { title: 'Create', href: null },
]
</script>

<template>
  <Head title="Create New Task Delegation" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Task Delegation</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a task delegation.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.task-delegations.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Task Delegation' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>