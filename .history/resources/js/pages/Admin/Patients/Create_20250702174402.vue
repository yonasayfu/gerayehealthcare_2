<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

// Breadcrumbs for Create Page
const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Patients', href: '/dashboard/patients' },
  { title: 'Create', href: '/dashboard/patients/create' },
]

// Form state
const form = useForm({
  full_name: '',
  date_of_birth: '',
  gender: '',
  address: '',
  phone_number: '',
  email: '',
  emergency_contact: '',
  geolocation: '',
})

// Handle submit
function submit() {
  form.post('/dashboard/patients')
}
</script>

<template>
  <Head title="Create Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">New Patient</h1>

      <Form :form="form" @submit="submit" />
    </div>
  </AppLayout>
</template>
