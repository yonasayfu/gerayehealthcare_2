<script setup lang="ts">
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  form: Object, // This form object will be passed from Create.vue or Edit.vue
});
</script>

<template>
  <div>
    <InputLabel for="name" value="Platform Name" />
    <TextInput
      id="name"
      type="text"
      class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
      v-model="props.form.name"
      required
      autofocus
    />
    <InputError class="mt-2" :message="props.form.errors.name" />
  </div>

  <div>
    <InputLabel for="api_endpoint" value="API Endpoint" />
    <TextInput
      id="api_endpoint"
      type="url"
      class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
      v-model="props.form.api_endpoint"
    />
    <InputError class="mt-2" :message="props.form.errors.api_endpoint" />
  </div>

  <div>
    <InputLabel for="api_credentials" value="API Credentials" />
    <TextInput
      id="api_credentials"
      type="text"
      class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
      v-model="props.form.api_credentials"
    />
    <InputError class="mt-2" :message="props.form.errors.api_credentials" />
  </div>

  <div class="flex items-center gap-2">
    <input type="checkbox" id="is_active" v-model="props.form.is_active" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500" />
    <InputLabel for="is_active" value="Is Active" />
    <InputError class="mt-2" :message="props.form.errors.is_active" />
  </div>
</template>
