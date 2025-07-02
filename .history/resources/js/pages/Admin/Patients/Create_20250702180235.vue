<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
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
      <!-- Section Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Patient</h1>
        <p class="text-sm text-muted-foreground mt-1">
          Fill in the fields below to register a new patient.
        </p>
      </div>

      <!-- Form Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" />

        <!-- Form Actions -->
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
            {{ form.processing ? 'Saving...' : 'Save Patient' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
