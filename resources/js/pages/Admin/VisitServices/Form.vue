<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{
  form: ReturnType<typeof useForm>
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
  visitService?: any // Optional: for edit view to show existing files
}>()

const statusOptions = ['Pending', 'In Progress', 'Completed', 'Cancelled']
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patient</label>
      <select id="patient_id" v-model="form.patient_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
      </select>
      <div v-if="form.errors.patient_id" class="text-sm text-red-600 mt-1">{{ form.errors.patient_id }}</div>
    </div>

    <div>
      <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Staff</label>
      <select id="staff_id" v-model="form.staff_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
        <option v-for="member in staff" :key="member.id" :value="member.id">{{ member.first_name }} {{ member.last_name }}</option>
      </select>
      <div v-if="form.errors.staff_id" class="text-sm text-red-600 mt-1">{{ form.errors.staff_id }}</div>
    </div>

    <div>
      <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled At</label>
      <input id="scheduled_at" v-model="form.scheduled_at" type="datetime-local" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required />
      <div v-if="form.errors.scheduled_at" class="text-sm text-red-600 mt-1">{{ form.errors.scheduled_at }}</div>
    </div>

    <div>
      <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
      <select id="status" v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required>
        <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
      </select>
      <div v-if="form.errors.status" class="text-sm text-red-600 mt-1">{{ form.errors.status }}</div>
    </div>

    <div class="md:col-span-1">
      <label for="visit_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visit Notes</label>
      <textarea id="visit_notes" v-model="form.visit_notes" rows="4" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
      <div v-if="form.errors.visit_notes" class="text-sm text-red-600 mt-1">{{ form.errors.visit_notes }}</div>
    </div>

    <div class="md:col-span-1">
      <label for="service_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Description (for Invoice)</label>
      <textarea id="service_description" v-model="form.service_description" rows="4" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" placeholder="e.g., Standard 1-hour consultation"></textarea>
      <div v-if="form.errors.service_description" class="text-sm text-red-600 mt-1">{{ form.errors.service_description }}</div>
    </div>
    
    <div>
      <label for="prescription_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prescription File</label>
      <input type="file" @input="form.prescription_file = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
      <div v-if="visitService?.prescription_file_url" class="mt-2 text-xs">
        Current file: <a :href="visitService.prescription_file_url" target="_blank" class="text-indigo-600 hover:underline">View/Download</a>
      </div>
      <div v-if="form.errors.prescription_file" class="text-sm text-red-600 mt-1">{{ form.errors.prescription_file }}</div>
    </div>
    <div>
      <label for="vitals_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vitals File</label>
      <input type="file" @input="form.vitals_file = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
      <div v-if="visitService?.vitals_file_url" class="mt-2 text-xs">
        Current file: <a :href="visitService.vitals_file_url" target="_blank" class="text-indigo-600 hover:underline">View/Download</a>
      </div>
      <div v-if="form.errors.vitals_file" class="text-sm text-red-600 mt-1">{{ form.errors.vitals_file }}</div>
    </div>

    <div v-if="form.progress" class="md:col-span-2">
      <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
        <div class="bg-indigo-600 h-2.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
      </div>
    </div>

  </div>
</template>