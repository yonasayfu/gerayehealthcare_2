<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{ partnerAgreements: Array<{ id: number; title: string }>; referrals: Array<{ id: number; referral_date: string }>; invoices: Array<{ id: number; invoice_number: string }> }>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Partner Commissions', href: '/dashboard/partner-commissions' },
  { title: 'Create', href: '/dashboard/partner-commissions/create' },
]

const form = useForm({
  agreement_id: null,
  referral_id: null,
  invoice_id: null,
  commission_amount: null,
  calculation_date: '',
  payout_date: '',
  status: 'Due',
})

function submit() {
  form.post(route('admin.partner-commissions.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Create New Partner Commission" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Partner Commission</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a partner commission.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.partner-commissions.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Partner Commission' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
