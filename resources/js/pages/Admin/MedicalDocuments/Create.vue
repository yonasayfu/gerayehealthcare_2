<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Save, ArrowLeft } from 'lucide-vue-next'
import InputError from '@/components/InputError.vue'
import axios from 'axios'

const props = defineProps<{ patients: Array<{ id:number; full_name:string; patient_code:string }> }>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Medical Documents', href: route('admin.medical-documents.index') },
  { title: 'Create', href: null },
]

const form = useForm({
  patient_id: '',
  medical_visit_id: '',
  document_type: 'other',
  title: '',
  document_date: '',
  summary: '',
  file: null as File | null,
})

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement
  if (input?.files && input.files[0]) {
    form.file = input.files[0]
  }
}

function submit() {
  form.post(route('admin.medical-documents.store'), { forceFormData: true })
}

// Visits selector support
import { ref, watch } from 'vue'
const visits = ref<Array<{ id: number; visit_date: string; visit_type: string }>>([])
const loadingVisits = ref(false)

async function fetchVisits(patientId: string | number) {
  if (!patientId) {
    visits.value = []
    return
  }
  try {
    loadingVisits.value = true
    const { data } = await axios.get(`/dashboard/medical-documents/visits-for-patient/${patientId}`)
    visits.value = data.visits ?? []
  } catch (e) {
    visits.value = []
    // optional: could surface an error banner here
  } finally {
    loadingVisits.value = false
  }
}

watch(() => form.patient_id, (val) => {
  form.medical_visit_id = ''
  fetchVisits(val as any)
})
</script>

<template>
  <Head title="Create Medical Document" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Header -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create Medical Document</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Upload and describe the document.</p>
          </div>
          <div class="flex items-center gap-2">
            <Link :href="route('admin.medical-documents.index')" class="btn-glass btn-glass-sm">
              <ArrowLeft class="icon" />
              <span class="hidden sm:inline">Back</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Top error banner for consistency -->
          <div v-if="Object.keys(form.errors).length" class="rounded-md bg-red-50 p-4 border border-red-200 text-red-800 text-sm">
            Please correct the highlighted errors and try again.
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Patient</label>
              <select v-model="form.patient_id" class="form-input">
                <option value="">Select patient</option>
                <option v-for="p in props.patients" :key="p.id" :value="p.id">{{ p.full_name }} ({{ p.patient_code }})</option>
              </select>
              <InputError class="mt-1" :message="form.errors.patient_id" />
            </div>

            <div>
              <label class="form-label">Medical Visit (optional)</label>
              <select v-model="form.medical_visit_id" class="form-input" :disabled="!form.patient_id || loadingVisits">
                <option value="">Select visit</option>
                <option v-for="v in visits" :key="v.id" :value="v.id">
                  {{ new Date(v.visit_date).toLocaleString() }} - {{ v.visit_type || 'Visit' }}
                </option>
              </select>
              <div v-if="form.patient_id && !loadingVisits && visits.length === 0" class="text-xs text-gray-500 mt-1">No visits found for selected patient.</div>
              <InputError class="mt-1" :message="form.errors.medical_visit_id" />
            </div>

            <div class="md:col-span-2">
              <label class="form-label">Title</label>
              <input v-model="form.title" type="text" class="form-input" />
              <InputError class="mt-1" :message="form.errors.title" />
            </div>

            <div>
              <label class="form-label">Document Type</label>
              <select v-model="form.document_type" class="form-input">
                <option value="doctor_note">Doctor Note</option>
                <option value="lab_request">Lab Request</option>
                <option value="lab_result">Lab Result</option>
                <option value="prescription">Prescription</option>
                <option value="other">Other</option>
              </select>
              <InputError class="mt-1" :message="form.errors.document_type" />
            </div>

            <div>
              <label class="form-label">Document Date</label>
              <input v-model="form.document_date" type="date" class="form-input" />
              <InputError class="mt-1" :message="form.errors.document_date" />
            </div>

            <div class="md:col-span-2">
              <label class="form-label">Summary</label>
              <textarea v-model="form.summary" class="form-input" rows="3"></textarea>
              <InputError class="mt-1" :message="form.errors.summary" />
            </div>

            <div class="md:col-span-2">
              <label class="form-label">File</label>
              <input @change="onFileChange" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="block w-full text-sm" />
              <InputError class="mt-1" :message="form.errors.file" />
            </div>
          </div>

          <div class="flex justify-end gap-2">
            <Link :href="route('admin.medical-documents.index')" class="btn-glass btn-glass-sm">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
              <Save class="icon" />
              {{ form.processing ? 'Saving...' : 'Save Document' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
