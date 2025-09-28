<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Save, ArrowLeft, FileText } from 'lucide-vue-next'
import Form from './Form.vue'
import InputError from '@/components/InputError.vue'
import axios from 'axios'
import { computed } from 'vue' // Import computed

const props = defineProps<{
  medicalDocument: {
    id: number
    title: string | null
    document_type: string
    document_date: string | null // Backend date format might be different
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

// Computed property to format document_date for the input type="date"
const formattedDocumentDate = computed(() => {
  if (props.medicalDocument.document_date) {
    const date = new Date(props.medicalDocument.document_date);
    // Format to YYYY-MM-DD
    return date.toISOString().split('T')[0];
  }
  return '';
});

const form = useForm({
  patient_id: props.medicalDocument.patient?.id || '',
  medical_visit_id: props.medicalDocument.medical_visit_id || '' as number | string, // Ensure type allows string for initial empty state
  document_type: props.medicalDocument.document_type || 'other',
  title: props.medicalDocument.title || '',
  document_date: formattedDocumentDate.value, // Use the formatted date
  summary: props.medicalDocument.summary || '',
  file: null as File | null,
})

// Visits selector support
import { ref, watch, onMounted } from 'vue'
const visits = ref<Array<{ id: number; visit_date: string; visit_type: string }>>([])
const loadingVisits = ref(false)

async function fetchVisits(patientId: number | string | undefined) {
  console.log('fetchVisits called with patientId:', patientId);
  if (!patientId) {
    visits.value = []
    return
  }
  try {
    loadingVisits.value = true
    const { data } = await axios.get(route('admin.medical-documents.visitsForPatient', { patient: patientId }))
    visits.value = data.visits ?? []
    console.log('Fetched visits:', visits.value);
    console.log('Current medical_visit_id in form:', form.medical_visit_id);
  } catch (e) {
    visits.value = []
    console.error('Error fetching visits:', e);
  } finally {
    loadingVisits.value = false
  }
}

onMounted(() => {
  console.log('Edit.vue mounted. medicalDocument:', props.medicalDocument);
  console.log('Initial form.patient_id:', form.patient_id);
  console.log('Initial form.medical_visit_id:', form.medical_visit_id);
  fetchVisits(form.patient_id);
})

watch(() => form.patient_id, (val) => {
  form.medical_visit_id = ''
  fetchVisits(val as any)
})

function submit() {
  console.log('Form submission started in Edit.vue');
  console.log('Form data before type conversion:', form.data());

  // Ensure patient_id is a number if it's set
  if (form.patient_id && typeof form.patient_id === 'string') {
    form.patient_id = parseInt(form.patient_id, 10) || '';
  }
  
  // Ensure medical_visit_id is a number if it's set
  if (form.medical_visit_id && typeof form.medical_visit_id === 'string') {
    form.medical_visit_id = parseInt(form.medical_visit_id, 10) || '';
  }

  console.log('Form data after type conversion:', form.data());

  form.put(route('admin.medical-documents.update', props.medicalDocument.id), {
    forceFormData: true,
    onSuccess: (page) => {
      console.log('Form submission successful:', page);
      // Inertia automatically handles redirect to index and flashing banner
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
      alert('Form submission failed: ' + JSON.stringify(errors));
    },
    onFinish: () => {
      console.log('Form submission finished');
    }
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
