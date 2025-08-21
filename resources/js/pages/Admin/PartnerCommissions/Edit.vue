<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  partnerCommission: {
    id: number
    agreement_id: number
    referral_id: number
    invoice_id: number
    commission_amount: number
    calculation_date: string
    payout_date: string | null
    status: string
  },
  partnerAgreements: Array<{ id: number; title: string }>,
  referrals: Array<{ id: number; referral_date: string }>,
  invoices: Array<{ id: number; invoice_number: string }>,
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('admin.partner-commissions.index') },
  { title: 'Partner Commissions', href: route('admin.partner-commissions.index') },
  { title: 'Edit', href: route('admin.partner-commissions.edit', { partner_commission: props.partnerCommission.id }) },
]

const form = useForm({
  _method: 'PUT', // Method spoofing for PUT request
  agreement_id: props.partnerCommission.agreement_id,
  referral_id: props.partnerCommission.referral_id,
  invoice_id: props.partnerCommission.invoice_id,
  commission_amount: props.partnerCommission.commission_amount,
  calculation_date: props.partnerCommission.calculation_date,
  payout_date: props.partnerCommission.payout_date,
  status: props.partnerCommission.status,
});

function submit() {
  form.post(route('admin.partner-commissions.update', { partner_commission: props.partnerCommission.id }), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Edit Partner Commission" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Partner Commission</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update partner commission information below.</p>
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
              :href="route('admin.partner-commissions.index')"
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
