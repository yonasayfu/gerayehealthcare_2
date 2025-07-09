<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{
  form: ReturnType<typeof useForm>
  existingPhoto?: string | null
}>()

</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
      <input id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required />
      <div v-if="form.errors.first_name" class="text-sm text-red-600 mt-1">{{ form.errors.first_name }}</div>
    </div>

    <div>
      <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
      <input id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required />
      <div v-if="form.errors.last_name" class="text-sm text-red-600 mt-1">{{ form.errors.last_name }}</div>
    </div>

    <div class="md:col-span-2">
      <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
      <input id="email" v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required />
      <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
    </div>

    <div>
      <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
      <input id="phone" v-model="form.phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
      <div v-if="form.errors.phone" class="text-sm text-red-600 mt-1">{{ form.errors.phone }}</div>
    </div>

    <div>
      <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
      <input id="position" v-model="form.position" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
      <div v-if="form.errors.position" class="text-sm text-red-600 mt-1">{{ form.errors.position }}</div>
    </div>
    
    <div>
      <label for="hourly_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hourly Rate ($)</label>
      <input id="hourly_rate" v-model="form.hourly_rate" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
      <div v-if="form.errors.hourly_rate" class="text-sm text-red-600 mt-1">{{ form.errors.hourly_rate }}</div>
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
        <select id="status" v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <option>Active</option>
            <option>Inactive</option>
        </select>
        <div v-if="form.errors.status" class="text-sm text-red-600 mt-1">{{ form.errors.status }}</div>
    </div>

    <div class="md:col-span-2">
      <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profile Photo</label>
      <img v-if="existingPhoto && !form.photo" :src="existingPhoto" alt="Current Photo" class="w-20 h-20 rounded-full object-cover my-2">
      <input id="photo" type="file" @input="form.photo = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
      <div v-if="form.errors.photo" class="text-sm text-red-600 mt-1">{{ form.errors.photo }}</div>
    </div>
  </div>
</template>