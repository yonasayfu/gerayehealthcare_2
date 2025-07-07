<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  form: ReturnType<typeof useForm>
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
}>()

const emit = defineEmits(['submit'])

const statusOptions = ['Pending', 'Completed', 'Cancelled', 'In Progress']
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patient</label>
        <select
          id="patient_id"
          v-model="form.patient_id"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
          required
        >
          <option value="" disabled>Select a patient</option>
          <option v-for="patient in patients" :key="patient.id" :value="patient.id">
            {{ patient.full_name }}
          </option>
        </select>
        <div v-if="form.errors.patient_id" class="text-sm text-red-600 mt-1">{{ form.errors.patient_id }}</div>
      </div>

      <div>
        <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Staff</label>
        <select
          id="staff_id"
          v-model="form.staff_id"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
          required
        >
          <option value="" disabled>Select a staff member</option>
          <option v-for="member in staff" :key="member.id" :value="member.id">
            {{ member.first_name }} {{ member.last_name }}
          </option>
        </select>
        <div v-if="form.errors.staff_id" class="text-sm text-red-600 mt-1">{{ form.errors.staff_id }}</div>
      </div>

      <div>
        <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled At</label>
        <input
          id="scheduled_at"
          v-model="form.scheduled_at"
          type="datetime-local"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
          required
        />
        <div v-if="form.errors.scheduled_at" class="text-sm text-red-600 mt-1">{{ form.errors.scheduled_at }}</div>
      </div>

      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
        <select
          id="status"
          v-model="form.status"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
          required
        >
          <option v-for="status in statusOptions" :key="status" :value="status">
            {{ status }}
          </option>
        </select>
        <div v-if="form.errors.status" class="text-sm text-red-600 mt-1">{{ form.errors.status }}</div>
      </div>

      <div class="md:col-span-2">
        <label for="visit_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visit Notes</label>
        <textarea
          id="visit_notes"
          v-model="form.visit_notes"
          rows="4"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
        ></textarea>
        <div v-if="form.errors.visit_notes" class="text-sm text-red-600 mt-1">{{ form.errors.visit_notes }}</div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-4 mt-8">
      <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
        Save Visit
      </button>
      <a :href="route('admin.visit-services.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
        Cancel
      </a>
    </div>
  </form>
</template>