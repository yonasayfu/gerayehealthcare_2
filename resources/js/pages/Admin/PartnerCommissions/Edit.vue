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
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Partner Commission
            </h3>
            <Link :href="route('admin.partner-commissions.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :partnerAgreements="props.partnerAgreements" :referrals="props.referrals" :invoices="props.invoices" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap items-center gap-2">
              <Link :href="route('admin.partner-commissions.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </button>
              <button
                class="btn btn-danger ml-auto"
                @click="() => { if (confirm('Are you sure you want to delete this partner commission?')) { router.delete(route('admin.partner-commissions.destroy', { partner_commission: props.partnerCommission.id })) } }"
              >
                Delete
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>
