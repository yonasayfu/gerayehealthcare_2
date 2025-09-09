<script setup lang="ts">
import InputError from '@/components/InputError.vue'

const props = defineProps<{ form: any }>()

const statuses = ['Draft', 'Published', 'Archived']
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Title</label>
      <input
        v-model="props.form.title"
        type="text"
        required
        class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      />
      <InputError class="mt-1" :message="props.form.errors?.title" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Event Date</label>
      <input
        v-model="props.form.event_date"
        type="date"
        required
        class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      />
      <InputError class="mt-1" :message="props.form.errors?.event_date" />
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
      <textarea
        v-model="props.form.description"
        rows="3"
        class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      ></textarea>
      <InputError class="mt-1" :message="props.form.errors?.description" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Broadcast Status</label>
      <select
        v-model="props.form.broadcast_status"
        class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      >
        <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
      </select>
      <InputError class="mt-1" :message="props.form.errors?.broadcast_status" />
    </div>

    <div class="flex items-center gap-2">
      <input id="is_free_service" type="checkbox" v-model="props.form.is_free_service" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
      <label for="is_free_service" class="text-sm font-medium text-gray-900 dark:text-white">Free Service</label>
      <InputError class="mt-1" :message="props.form.errors?.is_free_service" />
    </div>
  </div>
</template>

