<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Patients', href: '/dashboard/patients' },
  { title: 'Create', href: '/dashboard/patients/create' },
]

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

function submit() {
  form.post('/dashboard/patients')
}
</script>

<template>
  <Head title="Create Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Page Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Patient</h1>
        <p class="text-sm text-muted-foreground mt-1">
          Fill in the details below to register a new patient.
        </p>
      </div>

      <!-- Patient Form Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm">
        <Form :form="form" @submit="submit" />
      </div>
    </div>
  </AppLayout>
</template>
