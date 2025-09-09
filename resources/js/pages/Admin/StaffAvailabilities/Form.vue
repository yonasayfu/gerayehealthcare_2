<script setup lang="ts">
import InputError from '@/components/InputError.vue'

const props = defineProps<{ form: any; staffList?: Array<{ id: number; first_name: string; last_name: string }> }>()

const statuses = ['Available', 'Unavailable']
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Staff</label>
      <select v-model.number="props.form.staff_id" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
        <option :value="null">Select staff</option>
        <option v-for="s in props.staffList || []" :key="s.id" :value="s.id">{{ s.first_name }} {{ s.last_name }}</option>
      </select>
      <InputError class="mt-1" :message="props.form.errors?.staff_id" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Start Time</label>
      <input v-model="props.form.start_time" type="datetime-local" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700" />
      <InputError class="mt-1" :message="props.form.errors?.start_time" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">End Time</label>
      <input v-model="props.form.end_time" type="datetime-local" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700" />
      <InputError class="mt-1" :message="props.form.errors?.end_time" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
      <select v-model="props.form.status" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
        <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
      </select>
      <InputError class="mt-1" :message="props.form.errors?.status" />
    </div>
  </div>
</template>

