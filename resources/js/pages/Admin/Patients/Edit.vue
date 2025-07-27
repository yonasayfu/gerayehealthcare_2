<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType, Patient, PatientForm } from '@/types' // Import Patient and PatientForm

const props = defineProps<{
  patient: Patient; // Use the Patient interface
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: 'Edit', href: route('admin.patients.edit', props.patient.id) },
]

const form = useForm<any>({ // Use any for the form data to satisfy FormDataType constraint
  full_name: props.patient.full_name,
  fayda_id: props.patient.fayda_id,
  date_of_birth: props.patient.date_of_birth,
  gender: props.patient.gender,
  address: props.patient.address,
  phone_number: props.patient.phone_number,
  email: props.patient.email, // Add the missing email field
  source: props.patient.source,
  emergency_contact: props.patient.emergency_contact,
  geolocation: props.patient.geolocation,
})

function submit() {
  form.put(route('admin.patients.update', props.patient.id))
}
</script>

<template>
  <Head title="Edit Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Patient
            </h3>
            <Link :href="route('admin.patients.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
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
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>
