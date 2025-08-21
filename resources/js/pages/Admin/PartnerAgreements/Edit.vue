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
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Partner Agreement
            </h3>
            <Link :href="route('admin.partner-agreements.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :partners="props.partners" :staff="props.staff" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap items-center gap-2">
              <Link :href="route('admin.partner-agreements.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </button>
              <button
                class="btn btn-danger ml-auto"
                @click="() => { if (confirm('Are you sure you want to delete this partner agreement?')) { router.delete(route('admin.partner-agreements.destroy', { partner_agreement: props.partnerAgreement.id })) } }"
              >
                Delete
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>