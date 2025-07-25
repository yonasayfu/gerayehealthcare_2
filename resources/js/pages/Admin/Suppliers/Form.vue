<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  form: Object, // Inertia form object
});

const emit = defineEmits(['submit']);
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="space-y-6">
      <!-- Supplier Information -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Supplier Information</h2>
        <p class="mt-1 text-sm text-muted-foreground">Basic contact and identification details for the supplier.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-full">
            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white">Supplier Name</label>
            <input type="text" id="name" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-800" required />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="contact_person" class="block text-sm font-medium text-gray-900 dark:text-white">Contact Person</label>
            <input type="text" id="contact_person" v-model="form.contact_person" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-800" />
            <div v-if="form.errors.contact_person" class="text-red-500 text-sm mt-1">{{ form.errors.contact_person }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white">Email</label>
            <input type="email" id="email" v-model="form.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-800" />
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="phone" class="block text-sm font-medium text-gray-900 dark:text-white">Phone</label>
            <input type="text" id="phone" v-model="form.phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-800" />
            <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
          </div>

          <div class="sm:col-span-full">
            <label for="address" class="block text-sm font-medium text-gray-900 dark:text-white">Address</label>
            <textarea id="address" v-model="form.address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-800"></textarea>
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
