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
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="form-label">Patient</label>
      <select v-model="props.form.patient_id" class="form-input">
        <option value="">Select patient</option>
        <option v-for="p in props.patients" :key="p.id" :value="p.id">{{ p.full_name }} ({{ p.patient_code }})</option>
      </select>
      <InputError class="mt-1" :message="props.form.errors.patient_id" />
    </div>

    <div>
      <label class="form-label">Medical Visit (optional)</label>
      <select v-model="props.form.medical_visit_id" class="form-input" :disabled="!props.form.patient_id || props.loadingVisits">
        <option value="">Select visit</option>
        <option v-for="v in props.visits" :key="v.id" :value="v.id">
          {{ new Date(v.visit_date).toLocaleString() }} - {{ v.visit_type || 'Visit' }}
        </option>
      </select>
      <div v-if="props.form.patient_id && !props.loadingVisits && props.visits.length === 0" class="text-xs text-gray-500 mt-1">No visits found for selected patient.</div>
      <InputError class="mt-1" :message="props.form.errors.medical_visit_id" />
    </div>

    <div class="md:col-span-2">
      <label class="form-label">Title</label>
      <input v-model="props.form.title" type="text" class="form-input" />
      <InputError class="mt-1" :message="props.form.errors.title" />
    </div>

    <div>
      <label class="form-label">Document Type</label>
      <select v-model="props.form.document_type" class="form-input">
        <option value="doctor_note">Doctor Note</option>
        <option value="lab_request">Lab Request</option>
        <option value="lab_result">Lab Result</option>
        <option value="prescription">Prescription</option>
        <option value="other">Other</option>
      </select>
      <InputError class="mt-1" :message="props.form.errors.document_type" />
    </div>

    <div>
      <label class="form-label">Document Date</label>
      <input v-model="props.form.document_date" type="date" class="form-input" />
      <InputError class="mt-1" :message="props.form.errors.document_date" />
    </div>

    <div class="md:col-span-2">
      <label class="form-label">Summary</label>
      <textarea v-model="props.form.summary" class="form-input" rows="3"></textarea>
      <InputError class="mt-1" :message="props.form.errors.summary" />
    </div>

    
  </div>
</template>
