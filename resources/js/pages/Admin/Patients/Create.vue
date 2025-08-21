<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue' // Adjust path if Form.vue is in a different directory
import type { BreadcrumbItemType, PatientForm } from '@/types' // Import PatientForm type

const props = defineProps<{
  corporateClients: Array<any>;
  insurancePolicies: Array<any>;
}>();

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
  ethiopian_date_of_birth: null,
  gender: null,
  address: null,
  phone_number: null,
  email: null,
  source: null,
  emergency_contact: null,
  geolocation: null,
  corporate_client_id: null,
  policy_id: null,
})

function submit() {
  form.post(route('admin.patients.store'))
}
</script>

<template>
  <Head title="Create New Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Patient</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to register a patient.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :corporateClients="props.corporateClients" :insurancePolicies="props.insurancePolicies" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.patients.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Patient' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
