<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType, Patient, PatientForm } from '@/types' // Import Patient and PatientForm

const props = defineProps<{
  patient: Patient; // Use the Patient interface
  corporateClients: Array<any>;
  insurancePolicies: Array<any>;
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: 'Edit', href: route('admin.patients.edit', props.patient.id) },
]

const normalizeDate = (value: string | null | undefined) => {
  if (!value) return null as any
  // Accept ISO strings like 2025-08-01T00:00:00.000000Z
  const s = String(value)
  return s.length >= 10 ? s.substring(0, 10) : s
}

const form = useForm<any>({ // Use any for the form data to satisfy FormDataType constraint
  full_name: props.patient.full_name,
  fayda_id: props.patient.fayda_id,
  date_of_birth: normalizeDate(props.patient.date_of_birth as any),
  ethiopian_date_of_birth: props.patient.ethiopian_date_of_birth,
  gender: props.patient.gender,
  address: props.patient.address,
  phone_number: String(props.patient.phone_number ?? ''),
  email: props.patient.email, // Add the missing email field
  source: props.patient.source,
  emergency_contact: props.patient.emergency_contact,
  geolocation: props.patient.geolocation,
  // Pre-populate employer and policy from active insurance record if available
  corporate_client_id: (props.patient.employee_insurance_records?.[0]?.policy?.corporate_client_id)
    ?? props.patient.employee_insurance_records?.[0]?.corporate_client_id
    ?? null,
  policy_id: props.patient.employee_insurance_records?.[0]?.policy_id || null,
})

function submit() {
  form.put(route('admin.patients.update', props.patient.id))
}
</script>

<template>
  <Head title="Edit Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Header -->
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Patient</h1>
          <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update patient information below.</p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            :href="route('admin.patients.index')"
            class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium bg-white text-gray-800 border border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
          >
            Back
          </Link>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- reuse shared Form component (no changes to props/logic) -->
          <Form :form="form" :corporateClients="props.corporateClients" :insurancePolicies="props.insurancePolicies" />
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.patients.index')"
              class="inline-flex items-center px-3 py-2 rounded-md text-sm bg-white text-gray-800 border border-gray-200 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-700 dark:hover:bg-gray-600"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-60 dark:bg-indigo-500 dark:hover:bg-indigo-600"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
