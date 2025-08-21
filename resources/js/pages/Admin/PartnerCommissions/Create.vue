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
  <Head title="Add Partner Commission" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Add New Partner Commission
            </h3>
            <Link :href="route('admin.partner-commissions.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :partnerAgreements="props.partnerAgreements" :referrals="props.referrals" :invoices="props.invoices" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.partner-commissions.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Save' }}
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>
