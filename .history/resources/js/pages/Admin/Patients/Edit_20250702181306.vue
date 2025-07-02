<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  patient: {
    id: number
    full_name: string
    date_of_birth: string | null
    gender: string | null
    address: string | null
    phone_number: string | null
    email: string | null
    emergency_contact: string | null
    geolocation: string | null
  }
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Patients', href: '/dashboard/patients' },
  { title: 'Edit', href: `/dashboard/patients/${props.patient.id}/edit` },
]

const form = useForm({
  full_name: props.patient.full_name,
  date_of_birth: props.patient.date_of_birth,
  gender: props.patient.gender,
  address: props.patient.address,
  phone_number: props.patient.phone_number,
  email: props.patient.email,
  emergency_contact: props.patient.emergency_contact,
  geolocation: props.patient.geolocation,
})

function submit() {
  form.put(`/dashboard/patients/${props.patient.id}`)
}
</script>

<template>
  <Head title="Edit Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Patient</h1>
        <p class="text-sm text-muted-foreground">Update patient details as necessary.</p>
      </div>

      <!-- Form Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" />

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
          <Link
            href="/dashboard/patients"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
          >
            Cancel
          </Link>
          <button
            @click="submit"
            :disabled="form.processing"
            class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition"
          >
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
