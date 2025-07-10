<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{
  form: ReturnType<typeof useForm>
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
  visitService?: any 
}>()
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patient</label>
      <select id="patient_id" v-model="form.patient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
      </select>
      <div v-if="form.errors.patient_id" class="text-sm text-red-600 mt-1">{{ form.errors.patient_id }}</div>
    </div>
    <div>
      <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Staff</label>
      <select id="staff_id" v-model="form.staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
        <option v-for="member in staff" :key="member.id" :value="member.id">{{ member.first_name }} {{ member.last_name }}</option>
      </select>
      <div v-if="form.errors.staff_id" class="text-sm text-red-600 mt-1">{{ form.errors.staff_id }}</div>
    </div>

    <div>
      <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled At</label>
      <input id="scheduled_at" v-model="form.scheduled_at" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required />
      <div v-if="form.errors.scheduled_at" class="text-sm text-red-600 mt-1">{{ form.errors.scheduled_at }}</div>
    </div>
    <div>
      <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
      <select id="status" v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
        <option>Pending</option><option>In Progress</option><option>Completed</option><option>Cancelled</option>
      </select>
      <div v-if="form.errors.status" class="text-sm text-red-600 mt-1">{{ form.errors.status }}</div>
    </div>

    <div class="md:col-span-1">
      <label for="visit_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visit Notes</label>
      <textarea id="visit_notes" v-model="form.visit_notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"></textarea>
      <div v-if="form.errors.visit_notes" class="text-sm text-red-600 mt-1">{{ form.errors.visit_notes }}</div>
    </div>

    <div class="md:col-span-1">
      <label for="service_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Description (for Invoice)</label>
      <textarea id="service_description" v-model="form.service_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="e.g., Standard 1-hour consultation"></textarea>
      <div v-if="form.errors.service_description" class="text-sm text-red-600 mt-1">{{ form.errors.service_description }}</div>
    </div>

    <div class="md:col-span-2">
        </div>
  </div>
</template>