<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Save, ArrowLeft } from 'lucide-vue-next'
import Form from './Form.vue'
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

// Visits selector support
import { ref, watch, onMounted } from 'vue'
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
  } finally {
    loadingVisits.value = false
  }
}

watch(() => form.patient_id, (val) => {
  form.medical_visit_id = ''
  fetchVisits(val as any)
})

function submit() {
  form.post(route('admin.medical-documents.store'), { forceFormData: true })
}
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
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-6">
          

          <Form :form="form" :patients="props.patients" :visits="visits" :loadingVisits="loadingVisits" />

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
