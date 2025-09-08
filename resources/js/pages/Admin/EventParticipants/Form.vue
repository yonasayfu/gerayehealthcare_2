<script setup lang="ts">
const props = defineProps<{
  form: any
  events?: Array<{ id: number; title?: string; name?: string }>
  patients?: Array<{ id: number; full_name?: string; patient_code?: string }>
}>()
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Event</label>
      <select v-model="form.event_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="">Select Event</option>
        <option v-for="ev in (props.events || [])" :key="ev.id" :value="ev.id">{{ ev.title ?? ev.name ?? ('Event #' + ev.id) }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors?.event_id">{{ form.errors.event_id }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Patient</label>
      <select v-model="form.patient_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="">Select Patient</option>
        <option v-for="p in (props.patients || [])" :key="p.id" :value="p.id">
          {{ p.full_name ?? p.patient_code ?? ('Patient #' + p.id) }}
        </option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors?.patient_id">{{ form.errors.patient_id }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
      <select v-model="form.status" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="registered">Registered</option>
        <option value="attended">Attended</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors?.status">{{ form.errors.status }}</span>
    </div>
  </div>
</template>
