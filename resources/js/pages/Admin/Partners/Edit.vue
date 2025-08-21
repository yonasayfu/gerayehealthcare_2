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
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Partner</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update partner information below.</p>
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
              :href="route('admin.partners.index')"
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
