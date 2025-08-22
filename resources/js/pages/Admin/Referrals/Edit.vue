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
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Referral</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update referral information below.</p>
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
              :href="route('admin.referrals.index')"
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
