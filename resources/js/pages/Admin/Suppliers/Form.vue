<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue'
import Input from '@/components/ui/input/Input.vue'

const props = defineProps<{
  form: ReturnType<typeof useForm>;
}>();
</script>

<template>
  <form @submit.prevent="$emit('submit')">
    <div class="grid grid-cols-6 gap-6">
      <div class="col-span-6 sm:col-span-3">
        <label for="name" class="text-sm font-medium text-gray-900 block mb-2">Supplier Name</label>
        <Input v-model="form.name" id="name" name="name" type="text" required />
        <InputError class="mt-1" :message="form.errors.name" />
      </div>
      <div class="col-span-6 sm:col-span-3">
        <label for="contact_person" class="text-sm font-medium text-gray-900 block mb-2">Contact Person</label>
        <Input v-model="form.contact_person" id="contact_person" name="contact_person" type="text" />
        <InputError class="mt-1" :message="form.errors.contact_person" />
      </div>
      <div class="col-span-6 sm:col-span-3">
        <label for="email" class="text-sm font-medium text-gray-900 block mb-2">Email</label>
        <Input v-model="form.email" id="email" name="email" type="email" />
        <InputError class="mt-1" :message="form.errors.email" />
      </div>
      <div class="col-span-6 sm:col-span-3">
        <label for="phone" class="text-sm font-medium text-gray-900 block mb-2">Phone</label>
        <Input
          v-model="form.phone"
          id="phone"
          name="phone"
          type="text"
          inputmode="tel"
          placeholder="e.g., 0912345678 or +251912345678"
          pattern="^((\+251)|(0))\d{9}$"
          title="Phone number must be an Ethiopian number (e.g., +251912345678 or 0912345678)"
        />
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
