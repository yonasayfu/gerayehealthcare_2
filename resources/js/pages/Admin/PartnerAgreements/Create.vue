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
  form
    .transform((data: any) => ({
      ...data,
      partner_id: data.partner_id != null ? Number(data.partner_id) : null,
      end_date: data.end_date || null,
      priority_service_level: data.priority_service_level || null,
      commission_type: data.commission_type || null,
      commission_rate: data.commission_rate ?? null,
      terms_document_path: data.terms_document_path || null,
      signed_by_staff_id: data.signed_by_staff_id != null ? Number(data.signed_by_staff_id) : null,
    }))
    .post(route('admin.partner-agreements.store'), {
      preserveScroll: true,
      onFinish: () => form.transform((d: any) => d), // reset transform
    })
}
</script>

<template>
  <Head title="Add Partner Agreement" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Add New Partner Agreement
            </h3>
            <Link :href="route('admin.partner-agreements.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :partners="props.partners" :staff="props.staff" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.partner-agreements.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Save' }}
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>