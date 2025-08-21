<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{ partners: Array<{ id: number; name: string }>; staff: Array<{ id: number; name: string }> }>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Partner Agreements', href: '/dashboard/partner-agreements' },
  { title: 'Create', href: '/dashboard/partner-agreements/create' },
]

const form = useForm({
  partner_id: null,
  agreement_title: '',
  agreement_type: '',
  status: 'Draft',
  start_date: '',
  end_date: '',
  priority_service_level: null,
  commission_type: null,
  commission_rate: null,
  terms_document_path: '',
  signed_by_staff_id: null,
})

function submit() {
  form.post(route('admin.partner-agreements.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Create New Partner Agreement" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Partner Agreement</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a partner agreement.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.partner-agreements.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Partner Agreement' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>