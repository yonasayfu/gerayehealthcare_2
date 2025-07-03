<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'
import { watch, reactive, ref } from 'vue'
import axios from 'axios'
import debounce from 'lodash/debounce'

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

const localErrors = reactive({
  email: '',
  phone_number: '',
})

const checkFieldUnique = debounce(async (field: 'email' | 'phone_number', value: string) => {
  if (!value) {
    localErrors[field] = ''
    return
  }

  try {
    const { data } = await axios.get(route('patients.validate-field'), {
      params: { field, value },
    })

    localErrors[field] = data.valid
      ? ''
      : `${field === 'email' ? 'Email' : 'Phone number'} already exists.`
  } catch (err) {
    localErrors[field] = 'Validation failed. Please try again.'
  }
}, 200) // Reduced delay for smoother UX

// Real-time watchers
watch(() => form.email, (val) => checkFieldUnique('email', val))
watch(() => form.phone_number, (val) => checkFieldUnique('phone_number', val))

function submit() {
  if (localErrors.email || localErrors.phone_number) return
  form.post('/dashboard/patients')
}
</script>

<template>
  <Head title="Create Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Patient</h1>
        <p class="text-sm text-muted-foreground">
          Fill in the form to register a new patient.
        </p>
      </div>

      <!-- Form Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <!-- Form with real-time error props -->
        <Form :form="form" :local-errors="localErrors" />

        <!-- Actions -->
        <div class="flex flex-col items-end space-y-2">
          <div v-if="localErrors.email || localErrors.phone_number" class="text-red-500 text-sm">
            Please fix the highlighted errors before submitting.
          </div>
          <div class="flex space-x-3">
            <Link
              href="/dashboard/patients"
              class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
            >
              Cancel
            </Link>
            <button
              @click="submit"
              :disabled="form.processing || localErrors.email || localErrors.phone_number"
              class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition disabled:cursor-not-allowed disabled:bg-green-600 disabled:text-white"
            >
              {{ form.processing ? 'Saving...' : 'Save Patient' }}
            </button>
          <a/div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>