<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  form: any;
  patients?: Array<{ id: number; full_name: string }>;
  staff?: Array<{ id: number; first_name: string; last_name: string }>;
}>();

const emit = defineEmits(['submit']);

const staffOptions = computed(() => {
  if (!Array.isArray(props.staff)) return [];
  return props.staff.map(s => ({
    id: s.id,
    fullName: `${s.first_name || ''} ${s.last_name || ''}`.trim(),
  }));
});

const patientOptions = computed(() => {
    return Array.isArray(props.patients) ? props.patients : [];
});

// Helper function to format a date from the server (which is UTC)
// into a string that the datetime-local input can use in the browser's local time.
const formatDateTimeForInput = (dateString: string): string => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        // This correctly creates a local time string for the input value
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    } catch (e) {
        return '';
    }
};
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="border-b border-gray-900/10 pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Assignment Details</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Assign a staff member to a patient and define their shift schedule.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

        <!-- Patient Selection -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Patient</label>
          <div class="mt-2">
            <select
              v-model="form.patient_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option value="">Select a patient</option>
              <option v-for="patient in patientOptions" :key="patient.id" :value="patient.id">
                {{ patient.full_name }}
              </option>
            </select>
            <div v-if="form.errors.patient_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.patient_id }}
            </div>
          </div>
        </div>

        <!-- Staff Selection -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Staff Member</label>
          <div class="mt-2">
            <select
              v-model="form.staff_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option value="">Select a staff member</option>
              <option v-for="staffMember in staffOptions" :key="staffMember.id" :value="staffMember.id">
                {{ staffMember.fullName }}
              </option>
            </select>
            <div v-if="form.errors.staff_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.staff_id }}
            </div>
          </div>
        </div>

        <!-- Shift Start -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Shift Start</label>
          <div class="mt-2">
            <input
              type="datetime-local"
              :value="formatDateTimeForInput(form.shift_start)"
              @input="form.shift_start = $event.target.value"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.shift_start" class="text-red-500 text-sm mt-1">
              {{ form.errors.shift_start }}
            </div>
          </div>
        </div>

        <!-- Shift End -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Shift End</label>
          <div class="mt-2">
            <input
              type="datetime-local"
              :value="formatDateTimeForInput(form.shift_end)"
              @input="form.shift_end = $event.target.value"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.shift_end" class="text-red-500 text-sm mt-1">
              {{ form.errors.shift_end }}
            </div>
          </div>
        </div>

        <!-- Status -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
          <div class="mt-2">
            <select
              v-model="form.status"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option>Assigned</option>
              <option>In Progress</option>
              <option>Completed</option>
              <option>Cancelled</option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">
              {{ form.errors.status }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
