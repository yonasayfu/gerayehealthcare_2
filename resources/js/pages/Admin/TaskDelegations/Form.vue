<script setup lang="ts">
import { computed } from 'vue'
import InputError from '@/components/InputError.vue'

const props = defineProps<{
  form: any,
  staff?: { id: number; first_name: string; last_name: string }[],
  partners?: { id: number; name: string }[] | null,
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
        <InputError class="mt-1" :message="form.errors.title" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Assigned To</label>
        <select
          v-model="form.assigned_to"
          class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
        >
          <option v-for="person in staffOptions" :key="person.id" :value="person.id">
            {{ person.first_name }} {{ person.last_name }}
          </option>
        </select>
        <InputError class="mt-1" :message="form.errors.assigned_to" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Due Date</label>
        <input
          v-model="form.due_date"
          type="date"
          class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
        />
        <InputError class="mt-1" :message="form.errors.due_date" />
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
        <InputError class="mt-1" :message="form.errors.status" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Priority</label>
        <select
          v-model="form.priority_level"
          class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
        >
          <option value="1">Low</option>
          <option value="2">Medium</option>
          <option value="3">Normal</option>
          <option value="4">High</option>
          <option value="5">Critical</option>
        </select>
        <InputError class="mt-1" :message="form.errors.priority_level" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Category</label>
        <select v-model="form.task_category" class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600">
          <option value="General">General</option>
          <option value="Engagement">Engagement</option>
          <option value="Other">Other</option>
        </select>
        <InputError class="mt-1" :message="form.errors.task_category" />
      </div>

      <div v-if="form.task_category === 'Engagement'">
        <label class="block text-sm font-medium text-gray-900 dark:text-white">Partner (for Engagement)</label>
        <select v-model="form.partner_id" class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600">
          <option :value="null">Select Partner</option>
          <option v-for="p in (partners || [])" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
        <InputError class="mt-1" :message="form.errors.partner_id" />
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
      <textarea
        v-model="form.notes"
        rows="3"
        class="mt-2 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      ></textarea>
      <InputError class="mt-1" :message="form.errors.notes" />
    </div>
  </div>
</template>
