<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  partnerAgreement: {
    id: number
    partner_id: number
    agreement_title: string
    agreement_type: string
    status: string
    start_date: string
    end_date: string | null
    priority_service_level: string | null
    commission_type: string | null
    commission_rate: number | null
    terms_document_path: string | null
    signed_by_staff_id: number | null
  },
  partners: Array<{ id: number; name: string }>,
  staff: Array<{ id: number; name: string }>,
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('admin.partner-agreements.index') },
  { title: 'Partner Agreements', href: route('admin.partner-agreements.index') },
  { title: 'Edit', href: route('admin.partner-agreements.edit', { partner_agreement: props.partnerAgreement.id }) },
]

const form = useForm({
  _method: 'PUT', // Method spoofing for PUT request
  partner_id: props.partnerAgreement.partner_id,
  agreement_title: props.partnerAgreement.agreement_title,
  agreement_type: props.partnerAgreement.agreement_type,
  status: props.partnerAgreement.status,
  start_date: props.partnerAgreement.start_date,
  end_date: props.partnerAgreement.end_date,
  priority_service_level: props.partnerAgreement.priority_service_level,
  commission_type: props.partnerAgreement.commission_type,
  commission_rate: props.partnerAgreement.commission_rate,
  terms_document_path: props.partnerAgreement.terms_document_path,
  signed_by_staff_id: props.partnerAgreement.signed_by_staff_id,
});

function submit() {
  form
    .transform((data: any) => ({
      ...data,
      end_date: data.end_date || null,
      priority_service_level: data.priority_service_level || null,
      commission_type: data.commission_type || null,
      commission_rate: data.commission_rate ?? null,
      terms_document_path: data.terms_document_path || null,
      signed_by_staff_id: data.signed_by_staff_id || null,
    }))
    .post(route('admin.partner-agreements.update', { partner_agreement: props.partnerAgreement.id }), {
      preserveScroll: true,
      onFinish: () => form.transform((d: any) => d),
    });
}
</script>

<template>
  <Head title="Edit Partner Agreement" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Partner Agreement</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update partner agreement information below.</p>
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
              :href="route('admin.partner-agreements.index')"
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