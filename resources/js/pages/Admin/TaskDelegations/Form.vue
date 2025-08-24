<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  form: any,
  staff?: { id: number; first_name: string; last_name: string }[]
}>()

const staffOptions = computed(() => props.staff ?? [])
</script>

<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Title</label>
        <input
          v-model="form.title"
          type="text"
          class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
        />
        <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Assigned To</label>
        <select
          v-model="form.assigned_to"
          class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
        >
          <option :value="null">Select staff</option>
          <option v-for="person in staffOptions" :key="person.id" :value="person.id">
            {{ person.first_name }} {{ person.last_name }}
          </option>
        </select>
        <p v-if="form.errors.assigned_to" class="text-red-500 text-xs mt-1">{{ form.errors.assigned_to }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Due Date</label>
        <input
          v-model="form.due_date"
          type="date"
          class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
        />
        <p v-if="form.errors.due_date" class="text-red-500 text-xs mt-1">{{ form.errors.due_date }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
        <select
          v-model="form.status"
          class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
        >
          <option value="Pending">Pending</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
        </select>
        <p v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</p>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
      <textarea
        v-model="form.notes"
        rows="3"
        class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      ></textarea>
      <p v-if="form.errors.notes" class="text-red-500 text-xs mt-1">{{ form.errors.notes }}</p>
    </div>
  </div>
</template>
