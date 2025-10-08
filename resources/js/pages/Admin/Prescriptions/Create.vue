<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Save, ArrowLeft } from 'lucide-vue-next'
import Form from './Form.vue'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Prescriptions', href: route('admin.prescriptions.index') },
  { title: 'Create', href: null },
]

const props = defineProps<{ patients: Array<any>; staff: Array<any> }>()

const form = useForm({
  patient_id: '',
  created_by_staff_id: '',
  prescribed_date: '',
  status: 'draft',
  instructions: '',
  items: [
    { medication_name: '', dosage: '', frequency: '', duration: '', notes: '' },
  ],
})

function submit() {
  form.transform((data) => ({
    ...data,
    items: data.items.filter((item) => item.medication_name.trim() !== ''),
  })).post(route('admin.prescriptions.store'))
}
</script>

<template>
  <Head title="Create Prescription" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4 sm:p-6">
      <div class="surface-panel overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">Create New Prescription</h1>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in the required information to create a prescription.</p>
            </div>
            <div class="flex-shrink-0">
              <Link 
                :href="route('admin.prescriptions.index')" 
                class="btn-glass btn-glass-sm"
              >
                <ArrowLeft class="w-4 h-4 mr-2" />
                Back to Prescriptions
              </Link>
            </div>
          </div>
        </div>

        <div class="px-4 py-5 sm:p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <Form :form="form" :patients="props.patients" :staff="props.staff" />
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[hsl(215_28%_32%)]">
              <Link 
                :href="route('admin.prescriptions.index')" 
                class="btn-glass btn-glass-sm"
              >
                Cancel
              </Link>
              <button 
                type="submit" 
                :disabled="form.processing" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-75"
              >
                <Save class="w-4 h-4 mr-2" />
                {{ form.processing ? 'Saving...' : 'Save Prescription' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
