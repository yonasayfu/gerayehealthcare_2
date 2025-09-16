<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Save, ArrowLeft, FileText } from 'lucide-vue-next'
import InputError from '@/components/InputError.vue'
import axios from 'axios'

const props = defineProps<{
  medicalDocument: {
    id: number
    title: string | null
    document_type: string
    document_date: string | null
    summary: string | null
    file_path?: string | null
    patient?: { id: number; full_name: string; patient_code: string }
    medical_visit_id?: number | null
  }
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Medical Documents', href: route('admin.medical-documents.index') },
  { title: 'Edit', href: null },
]

const form = useForm({
  title: props.medicalDocument.title || '',
  document_type: props.medicalDocument.document_type || 'other',
  document_date: props.medicalDocument.document_date || '',
  summary: props.medicalDocument.summary || '',
  file: null as File | null,
  medical_visit_id: (props.medicalDocument as any).medical_visit_id || '',
})

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement
  if (input?.files && input.files[0]) {
    form.file = input.files[0]
  }
}

function submit() {
  form.post(route('admin.medical-documents.update', props.medicalDocument.id), {
    _method: 'put',
    forceFormData: true,
  })
}

// Fetch visits for this document's patient for selection
import { ref, onMounted } from 'vue'
const visits = ref<Array<{ id: number; visit_date: string; visit_type: string }>>([])
const loadingVisits = ref(false)

async function fetchVisits(patientId: number | undefined) {
  if (!patientId) return
  try {
    loadingVisits.value = true
    const { data } = await axios.get(`/dashboard/medical-documents/visits-for-patient/${patientId}`)
    visits.value = data.visits ?? []
  } catch (e) {
    visits.value = []
  } finally {
    loadingVisits.value = false
  }
}

onMounted(() => {
  fetchVisits(props.medicalDocument.patient?.id)
})
</script>

<template>
  <Head title="Edit Medical Document" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="liquidGlass-wrapper">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content p-4">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Medical Document</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Update details or replace the file</p>
            </div>
            <div class="flex items-center gap-2">
              <Link :href="route('admin.medical-documents.index')" class="btn-glass btn-glass-sm">
                <ArrowLeft class="icon" />
                <span class="hidden sm:inline">Back</span>
              </Link>
              <button @click="submit" :disabled="form.processing" class="btn-glass">
                <Save class="icon" />
                <span class="hidden sm:inline">Update</span>
              </button>
            </div>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Medical Visit (optional)</label>
                <select v-model="form.medical_visit_id" class="form-input" :disabled="loadingVisits">
                  <option value="">Select visit</option>
                  <option v-for="v in visits" :key="v.id" :value="v.id">
                    {{ new Date(v.visit_date).toLocaleString() }} - {{ v.visit_type || 'Visit' }}
                  </option>
                </select>
                <div v-if="!loadingVisits && visits.length === 0" class="text-xs text-gray-500 mt-1">No visits found for patient.</div>
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
                <label class="form-label">Replace File</label>
                <input @change="onFileChange" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="block w-full text-sm" />
                <InputError class="mt-1" :message="form.errors.file" />
                <div v-if="props.medicalDocument.file_path" class="text-sm mt-2">
                  Current file:
                  <a :href="`/storage/${props.medicalDocument.file_path}`" target="_blank" class="underline text-primary-600">View</a>
                </div>
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" :disabled="form.processing" class="btn-glass">
                <Save class="icon" />
                Update Document
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
