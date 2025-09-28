<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps<{
  form: ReturnType<typeof useForm>
  patients: Array<{ id: number; full_name: string; patient_code: string }>
  visits: Array<{ id: number; visit_date: string; visit_type: string }>
  loadingVisits: boolean
}>()

// Visits selector support - these are now passed as props from Create/Edit
// No need to re-fetch here, just use the props
</script>

<template>
  <div class="border-b border-gray-900/10 pb-12">
    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Medical Document Details</h2>
    <p class="mt-1 text-sm text-muted-foreground">
      Please fill in the details of the medical document.
    </p>

    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
      <div class="sm:col-span-3">
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Patient</label>
        <div class="mt-2">
          <select v-model="props.form.patient_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
            <option value="">Select patient</option>
            <option v-for="p in props.patients" :key="p.id" :value="p.id">{{ p.full_name }} ({{ p.patient_code }})</option>
          </select>
          <InputError class="mt-1" :message="props.form.errors.patient_id" />
        </div>
      </div>

      <div class="sm:col-span-3">
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Medical Visit (optional)</label>
        <div class="mt-2">
          <select v-model="props.form.medical_visit_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" :disabled="!props.form.patient_id || props.loadingVisits">
            <option value="">Select visit</option>
            <option v-for="v in props.visits" :key="v.id" :value="v.id">
              {{ new Date(v.visit_date).toLocaleString() }} - {{ v.visit_type || 'Visit' }}
            </option>
          </select>
          <div v-if="props.form.patient_id && !props.loadingVisits && props.visits.length === 0" class="text-xs text-gray-500 mt-1">No visits found for selected patient.</div>
          <InputError class="mt-1" :message="props.form.errors.medical_visit_id" />
        </div>
      </div>

      <div class="sm:col-span-6">
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Title</label>
        <div class="mt-2">
          <input v-model="props.form.title" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
          <InputError class="mt-1" :message="props.form.errors.title" />
        </div>
      </div>

      <div class="sm:col-span-3">
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Document Type</label>
        <div class="mt-2">
          <select v-model="props.form.document_type" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
            <option value="doctor_note">Doctor Note</option>
            <option value="lab_request">Lab Request</option>
            <option value="lab_result">Lab Result</option>
            <option value="prescription">Prescription</option>
            <option value="other">Other</option>
          </select>
          <InputError class="mt-1" :message="props.form.errors.document_type" />
        </div>
      </div>

      <div class="sm:col-span-3">
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Document Date</label>
        <div class="mt-2">
          <input v-model="props.form.document_date" type="date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
          <InputError class="mt-1" :message="props.form.errors.document_date" />
        </div>
      </div>

      <div class="sm:col-span-6">
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Summary</label>
        <div class="mt-2">
          <textarea v-model="props.form.summary" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" rows="3"></textarea>
          <InputError class="mt-1" :message="props.form.errors.summary" />
        </div>
      </div>

    </div>
  </div>
</template>
