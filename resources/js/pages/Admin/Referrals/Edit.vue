<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  referral: {
    id: number
    partner_id: number
    agreement_id: number | null
    referred_patient_id: number
    referral_date: string
    status: string
    notes: string | null
  },
  partners: Array<{ id: number; name: string }>,
  partnerAgreements: Array<{ id: number; title: string }>,
  patients: Array<{ id: number; name: string }>,
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('admin.referrals.index') },
  { title: 'Referrals', href: route('admin.referrals.index') },
  { title: 'Edit', href: route('admin.referrals.edit', { referral: props.referral.id }) },
]

const form = useForm({
  _method: 'PUT', // Method spoofing for PUT request
  partner_id: props.referral.partner_id,
  agreement_id: props.referral.agreement_id,
  referred_patient_id: props.referral.referred_patient_id,
  referral_date: props.referral.referral_date,
  status: props.referral.status,
  notes: props.referral.notes,
});

function submit() {
  form
    .transform((data: any) => ({
      ...data,
      partner_id: data.partner_id != null ? Number(data.partner_id) : null,
      agreement_id: data.agreement_id != null ? Number(data.agreement_id) : null,
      referred_patient_id: data.referred_patient_id != null ? Number(data.referred_patient_id) : null,
      referral_date: data.referral_date,
      status: data.status || null,
      notes: data.notes || null,
    }))
    .post(route('admin.referrals.update', { referral: props.referral.id }), {
      preserveScroll: true,
      onFinish: () => form.transform((d: any) => d),
    });
}

function handleDelete() {
  if (window.confirm('Are you sure you want to delete this referral?')) {
    router.delete(route('admin.referrals.destroy', { referral: props.referral.id }))
  }
}
</script>

<template>
  <Head title="Edit Referral" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Referral
            </h3>
            <Link :href="route('admin.referrals.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :partners="props.partners" :partnerAgreements="props.partnerAgreements" :patients="props.patients" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap items-center gap-2">
              <Link :href="route('admin.referrals.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </button>
              <button
                class="btn btn-danger ml-auto"
                @click="handleDelete"
              >
                Delete
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>
