<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Adjust path if Form.vue is in a different directory
import type { BreadcrumbItemType, PatientForm } from '@/types' // Import PatientForm type

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: 'Create', href: route('admin.patients.create') },
]

// Initialize an empty form for a new patient
const form = useForm<any>({
  full_name: null,
  fayda_id: null,
  date_of_birth: null,
  gender: null,
  address: null,
  phone_number: null,
  email: null,
  source: null,
  emergency_contact: null,
  geolocation: null,
})

function submit() {
  form.post(route('admin.patients.store'))
}
</script>

<template>
  <Head title="Create New Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Patient</h1>
        <p class="text-sm text-muted-foreground">Fill in the details to add a new patient.</p>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" />

        <div class="flex justify-end space-x-3">
          <Link
            :href="route('admin.patients.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
          >
            Cancel
          </Link>
          <button
            @click="submit"
            :disabled="form.processing"
            class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition"
          >
            {{ form.processing ? 'Creating...' : 'Create Patient' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
