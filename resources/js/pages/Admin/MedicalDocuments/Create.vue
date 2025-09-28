<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Save } from 'lucide-vue-next'
import Form from './Form.vue'
import InputError from '@/components/InputError.vue'
import axios from 'axios'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{ patients: Array<{ id:number; full_name:string; patient_code:string }> }>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Medical Documents', href: route('admin.medical-documents.index') },
  { title: 'Create', href: route('admin.medical-documents.create') },
]

const user = usePage().props.auth.user
// Only set staff ID if user has a staff profile and the ID is valid
const staffId = user.staff?.id && !isNaN(parseInt(user.staff.id, 10)) ? parseInt(user.staff.id, 10) : null

console.log('User object:', user);
console.log('User staff:', user.staff);
console.log('Parsed staff ID:', staffId);

const form = useForm({
  id: null as number | null, // Explicitly define id as optional and null for creation
  patient_id: '' as number | string,
  medical_visit_id: '' as number | string, // Allow number or string
  document_type: 'other',
  title: '',
  document_date: '',
  summary: '',
  file: null as File | null,
  // Only include created_by_staff_id if we have a valid staff ID
  ...(staffId ? { created_by_staff_id: staffId } : {}),
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
    const { data } = await axios.get(route('admin.medical-documents.visitsForPatient', { patient: patientId }))
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
  console.log('Form submission started');
  console.log('Form data:', form.data());
  console.log('Staff ID:', staffId);
  console.log('Form processing:', form.processing);
  
  // Ensure patient_id is a number if it's set
  if (form.patient_id && typeof form.patient_id === 'string') {
    form.patient_id = parseInt(form.patient_id, 10) || '';
  }
  
  // Ensure medical_visit_id is a number if it's set
  if (form.medical_visit_id && typeof form.medical_visit_id === 'string') {
    form.medical_visit_id = parseInt(form.medical_visit_id, 10) || '';
  }
  
  // Ensure 'id' is null for creation to prevent Inertia from routing to update
  form.id = null;

  form.post(route('admin.medical-documents.store'), {
    forceFormData: true,
    onStart: () => {
      console.log('Form submission started - onStart');
    },
    onSuccess: (page) => {
      console.log('Form submission successful:', page);
      // Redirect to index page or show success message
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
      // Show errors to user
      alert('Form submission failed: ' + JSON.stringify(errors));
    },
    onFinish: () => {
      console.log('Form submission finished');
    }
  });
}
</script>

<template>
  <Head title="Create Medical Document" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Medical Document</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Upload and describe the document.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :patients="props.patients" :visits="visits" :loadingVisits="loadingVisits" />
          
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.medical-documents.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              <Save class="icon" />
              {{ form.processing ? 'Creating...' : 'Save Document' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
