<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
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

// Initialize form with existing patient data
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

// Submit update request
function submit() {
  form.put(`/dashboard/patients/${props.patient.id}`)
}
</script>

<template>
  <Head title="Edit Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
        Edit Patient
      </h1>

      <Form :form="form" @submit="submit" />
    </div>
  </AppLayout>
</template>
