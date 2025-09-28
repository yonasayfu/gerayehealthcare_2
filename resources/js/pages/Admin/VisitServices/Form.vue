<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  form: ReturnType<typeof useForm>
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
  visitService?: any // Optional: for edit view to show existing files
}>()

const statusOptions = ['Pending', 'In Progress', 'Completed', 'Cancelled']

function fillCurrentLocation() {
  if (!('geolocation' in navigator)) {
    alert('Geolocation is not supported in this browser.');
    return;
  }
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const { latitude, longitude } = pos.coords;
      props.form.check_in_latitude = Number(latitude.toFixed(6));
      props.form.check_in_longitude = Number(longitude.toFixed(6));
    },
    (err) => {
      console.warn('Geolocation error:', err);
      alert('Unable to retrieve your location.');
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patient</label>
      <select id="patient_id" v-model="form.patient_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" required>
        <option value="">Select a patient...</option>
        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
      </select>
      <div v-if="form.errors.patient_id" class="text-sm text-red-600 mt-1">{{ form.errors.patient_id }}</div>
    </div>

    <div>
      <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Staff</label>
      <select id="staff_id" v-model="form.staff_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" required>
        <option value="">Select a staff member...</option>
        <option v-for="member in staff" :key="member.id" :value="member.id">{{ member.first_name }} {{ member.last_name }}</option>
      </select>
      <div v-if="form.errors.staff_id" class="text-sm text-red-600 mt-1">{{ form.errors.staff_id }}</div>
    </div>

    <div title="Auto-checkout runs after 8 hours if not checked out. Admins may adjust times with a reason; mobile timestamps are accepted within safe limits.">
      <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled At</label>
      <input id="scheduled_at" v-model="form.scheduled_at" type="datetime-local" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" required />
      <div v-if="form.errors.scheduled_at" class="text-sm text-red-600 mt-1">{{ form.errors.scheduled_at }}</div>
    </div>

    <div>
      <label for="check_in_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-in Time (optional)</label>
      <input id="check_in_time" v-model="form.check_in_time" type="datetime-local" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
      <div v-if="form.errors.check_in_time" class="text-sm text-red-600 mt-1">{{ form.errors.check_in_time }}</div>
    </div>
    <div>
      <label for="check_out_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-out Time (optional)</label>
      <input id="check_out_time" v-model="form.check_out_time" type="datetime-local" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
      <div v-if="form.errors.check_out_time" class="text-sm text-red-600 mt-1">{{ form.errors.check_out_time }}</div>
    </div>

    <div>
      <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
      <select id="status" v-model="form.status" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" required>
        <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
      </select>
      <div v-if="form.errors.status" class="text-sm text-red-600 mt-1">{{ form.errors.status }}</div>
    </div>

    <div class="md:col-span-1">
      <label for="visit_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visit Notes</label>
      <textarea id="visit_notes" v-model="form.visit_notes" rows="4" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"></textarea>
      <div v-if="form.errors.visit_notes" class="text-sm text-red-600 mt-1">{{ form.errors.visit_notes }}</div>
    </div>

    <div class="md:col-span-1">
      <label for="service_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Description (for Invoice)</label>
      <textarea id="service_description" v-model="form.service_description" rows="4" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" placeholder="e.g., Standard 1-hour consultation"></textarea>
      <div v-if="form.errors.service_description" class="text-sm text-red-600 mt-1">{{ form.errors.service_description }}</div>
    </div>

    <!-- Optional manual geolocation inputs -->
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-6">
      <div>
        <label for="check_in_latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-in Latitude</label>
        <input id="check_in_latitude" type="number" step="0.000001" v-model.number="form.check_in_latitude" placeholder="e.g., 9.010123" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
        <div v-if="form.errors.check_in_latitude" class="text-sm text-red-600 mt-1">{{ form.errors.check_in_latitude }}</div>
      </div>
      <div>
        <label for="check_in_longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-in Longitude</label>
        <input id="check_in_longitude" type="number" step="0.000001" v-model.number="form.check_in_longitude" placeholder="e.g., 38.761234" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
        <div v-if="form.errors.check_in_longitude" class="text-sm text-red-600 mt-1">{{ form.errors.check_in_longitude }}</div>
      </div>
      <div>
        <label for="check_out_latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-out Latitude</label>
        <input id="check_out_latitude" type="number" step="0.000001" v-model.number="form.check_out_latitude" placeholder="e.g., 9.012345" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
        <div v-if="form.errors.check_out_latitude" class="text-sm text-red-600 mt-1">{{ form.errors.check_out_latitude }}</div>
      </div>
      <div>
        <label for="check_out_longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-out Longitude</label>
        <input id="check_out_longitude" type="number" step="0.000001" v-model.number="form.check_out_longitude" placeholder="e.g., 38.762345" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
        <div v-if="form.errors.check_out_longitude" class="text-sm text-red-600 mt-1">{{ form.errors.check_out_longitude }}</div>
      </div>
      <div class="md:col-span-4 -mt-3">
        <button type="button" class="btn-glass btn-glass-sm" @click="fillCurrentLocation">Use Current Location for Check-in</button>
        <p class="text-xs text-gray-500 mt-1">Optional. Mobile app auto-fills on check-in/out; web admins can set coordinates manually here if needed.</p>
      </div>
    </div>

    <div class="md:col-span-2">
      <label for="time_change_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Change Reason (required when editing times)</label>
      <textarea id="time_change_reason" v-model="form.time_change_reason" rows="3" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" placeholder="Explain why you adjusted check-in/out times"></textarea>
      <div v-if="form.errors.time_change_reason" class="text-sm text-red-600 mt-1">{{ form.errors.time_change_reason }}</div>
      <p class="text-xs text-gray-500 mt-1">Note: Visits left In Progress auto‑checkout after 8 hours. Mobile check‑in/out can include timestamps; the server accepts them if not far in the future and not too old.</p>
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
