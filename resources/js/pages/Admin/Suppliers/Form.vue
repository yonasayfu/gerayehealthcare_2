<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue'

const props = defineProps<{
  form: ReturnType<typeof useForm>;
}>();
</script>

<template>
  <form @submit.prevent="$emit('submit')">
    <div class="grid grid-cols-6 gap-6">
      <div class="col-span-6 sm:col-span-3">
        <label for="name" class="text-sm font-medium text-gray-900 block mb-2">Supplier Name</label>
        <input type="text" name="name" id="name" v-model="form.name" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" required>
        <InputError class="mt-1" :message="form.errors.name" />
      </div>
      <div class="col-span-6 sm:col-span-3">
        <label for="contact_person" class="text-sm font-medium text-gray-900 block mb-2">Contact Person</label>
        <input type="text" name="contact_person" id="contact_person" v-model="form.contact_person" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <InputError class="mt-1" :message="form.errors.contact_person" />
      </div>
      <div class="col-span-6 sm:col-span-3">
        <label for="email" class="text-sm font-medium text-gray-900 block mb-2">Email</label>
        <input type="email" name="email" id="email" v-model="form.email" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <InputError class="mt-1" :message="form.errors.email" />
      </div>
      <div class="col-span-6 sm:col-span-3">
        <label for="phone" class="text-sm font-medium text-gray-900 block mb-2">Phone</label>
        <input type="text" name="phone" id="phone" v-model="form.phone" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <InputError class="mt-1" :message="form.errors.phone" />
      </div>
      <div class="col-span-full">
        <label for="address" class="text-sm font-medium text-gray-900 block mb-2">Address</label>
        <textarea id="address" rows="6" v-model="form.address" class="shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-4"></textarea>
        <InputError class="mt-1" :message="form.errors.address" />
      </div>
    </div>
  </form>
</template>
