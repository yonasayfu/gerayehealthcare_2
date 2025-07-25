<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
  form: ReturnType<typeof useForm>;
}>();

const emit = defineEmits(['submit']);
</script>

<template>
  <form @submit.prevent="emit('submit')" class="space-y-8">
    <!-- User Account Details Section -->
    <div class="space-y-4">
      <h3 class="font-medium text-lg text-gray-900 dark:text-white">User Account Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Name</label>
          <input id="name" type="text" v-model="form.name" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Login Email</label>
          <input id="email" type="email" v-model="form.email" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
          <input id="password" type="password" v-model="form.password" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
        </div>
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
          <input id="password_confirmation" type="password" v-model="form.password_confirmation" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
        </div>
      </div>
    </div>

    <!-- Staff Profile Details Section -->
    <div class="space-y-4 border-t dark:border-gray-700 pt-8">
      <h3 class="font-medium text-lg text-gray-900 dark:text-white">Staff Profile Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
          <input id="first_name" type="text" v-model="form.first_name" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</div>
        </div>
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
          <input id="last_name" type="text" v-model="form.last_name" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</div>
        </div>
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
          <input id="phone" type="text" v-model="form.phone" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
        </div>
        <div>
          <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position / Job Title</label>
          <input id="position" type="text" v-model="form.position" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.position" class="text-red-500 text-sm mt-1">{{ form.errors.position }}</div>
        </div>
        <div>
          <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
          <input id="department" type="text" v-model="form.department" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.department" class="text-red-500 text-sm mt-1">{{ form.errors.department }}</div>
        </div>
        <div>
          <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hire Date</label>
          <input id="hire_date" type="date" v-model="form.hire_date" class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700"/>
          <div v-if="form.errors.hire_date" class="text-red-500 text-sm mt-1">{{ form.errors.hire_date }}</div>
        </div>
      </div>
    </div>
  </form>
</template>
