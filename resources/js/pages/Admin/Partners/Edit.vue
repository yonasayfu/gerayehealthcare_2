<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  partner: {
    id: number
    name: string
    type: string
    contact_person: string | null
    email: string | null
    phone: string | null
    address: string | null
    engagement_status: string
    account_manager_id: number | null
    notes: string | null
  },
  staff: Array<{ id: number; name: string }>,
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('admin.partners.index') },
  { title: 'Partners', href: route('admin.partners.index') },
  { title: 'Edit', href: route('admin.partners.edit', { partner: props.partner.id }) },
]

const form = useForm({
  _method: 'PUT', // Method spoofing for PUT request
  name: props.partner.name,
  type: props.partner.type,
  contact_person: props.partner.contact_person,
  email: props.partner.email,
  phone: props.partner.phone,
  address: props.partner.address,
  engagement_status: props.partner.engagement_status,
  account_manager_id: props.partner.account_manager_id,
  notes: props.partner.notes,
});

function submit() {
  form.post(route('admin.partners.update', { partner: props.partner.id }), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Edit Partner" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Partner
            </h3>
            <Link :href="route('admin.partners.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :staff="props.staff" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap items-center gap-2">
              <Link :href="route('admin.partners.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </button>
              <button
                class="btn btn-danger ml-auto"
                @click="() => { if (confirm('Are you sure you want to delete this partner?')) { router.delete(route('admin.partners.destroy', { partner: props.partner.id })) } }"
              >
                Delete
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>
