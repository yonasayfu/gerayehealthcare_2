<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType, Patient, PatientForm } from '@/types'

const props = defineProps<{
  corporateClients: Array<any>;
  insurancePolicies: Array<any>;
  patient: any;
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: props.patient?.full_name ?? 'Patient', href: route('admin.patients.show', props.patient?.id) },
  { title: 'Edit' },
]

// Initialize form with full patient data so inputs (including selects) are pre-filled
const form = useForm<any>({
  full_name: props.patient?.full_name ?? '',
  fayda_id: props.patient?.fayda_id ?? '',
  date_of_birth: props.patient?.date_of_birth ?? '',
  ethiopian_date_of_birth: props.patient?.ethiopian_date_of_birth ?? '',
  gender: props.patient?.gender ?? null,
  address: props.patient?.address ?? '',
  phone_number: props.patient?.phone_number ?? '',
  email: props.patient?.email ?? '',
  emergency_contact: props.patient?.emergency_contact ?? '',
  source: props.patient?.source ?? null,
  geolocation: props.patient?.geolocation ?? '',
  // Ensure selects are populated from patient relationships / fields
  corporate_client_id: props.patient?.corporate_client_id ?? props.patient?.corporate_client?.id ?? null,
  policy_id: props.patient?.employee_insurance_records?.[0]?.policy_id ?? props.patient?.policy_id ?? null,
  // include any other fields your Form.vue relies on
})

function submit() {
  form.put(route('admin.patients.update', props.patient.id))
}
</script>

<template>
  <Head title="Edit Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Patient</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update patient information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :corporateClients="props.corporateClients" :insurancePolicies="props.insurancePolicies" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.patients.index')"
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
