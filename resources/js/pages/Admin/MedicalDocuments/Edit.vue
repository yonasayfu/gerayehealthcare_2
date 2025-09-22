<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Save, ArrowLeft, FileText } from 'lucide-vue-next'
import Form from './Form.vue'
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
  },
  patients: Array<{ id: number; full_name: string; patient_code: string }>
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Medical Documents', href: route('admin.medical-documents.index') },
  { title: 'Edit', href: null },
]

const form = useForm({
  patient_id: props.medicalDocument.patient?.id || '',
  medical_visit_id: props.medicalDocument.medical_visit_id || '',
  document_type: props.medicalDocument.document_type || 'other',
  title: props.medicalDocument.title || '',
  document_date: props.medicalDocument.document_date || '',
  summary: props.medicalDocument.summary || '',
  file: null as File | null,
})

// Visits selector support
import { ref, watch, onMounted } from 'vue'
const visits = ref<Array<{ id: number; visit_date: string; visit_type: string }>>([])
const loadingVisits = ref(false)

async function fetchVisits(patientId: number | string | undefined) {
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
  } finally {
    loadingVisits.value = false
  }
}

onMounted(() => {
  fetchVisits(form.patient_id)
})

watch(() => form.patient_id, (val) => {
  form.medical_visit_id = ''
  fetchVisits(val as any)
})

function submit() {
  form.post(route('admin.medical-documents.update', props.medicalDocument.id), {
    forceFormData: true,
    _method: 'put'
  })
}
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
            </div>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <Form :form="form" :patients="props.patients" :visits="visits" :loadingVisits="loadingVisits" />

            <div class="md:col-span-2">
              <label class="form-label">Replace File</label>
              <input @change="form.file = ($event.target as HTMLInputElement)?.files?.[0] || null" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="block w-full text-sm" />
              <InputError class="mt-1" :message="form.errors.file" />
              <div v-if="props.medicalDocument.file_path" class="text-sm mt-2">
                Current file:
                <a :href="`/storage/${props.medicalDocument.file_path}`" target="_blank" class="underline text-primary-600">View</a>
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
