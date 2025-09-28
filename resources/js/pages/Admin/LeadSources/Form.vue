<script setup lang="ts">
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'

const props = defineProps({
  form: Object, // This form object will be passed from Create.vue or Edit.vue
  categories: Array, // Prop for categories data
});

console.log('Form.vue - categories prop:', props.categories);
console.log('Form.vue - form.category initial:', props.form.category);
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white">Source Name</label>
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
      <label for="category" class="block text-sm font-medium text-gray-900 dark:text-white">Category</label>
      <select
        id="category"
        v-model="props.form.category"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option value="">Select a Category</option>
        <option v-for="cat in props.categories" :key="cat" :value="cat">{{ cat }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.category" />
    </div>

    <div class="md:col-span-2">
      <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
      <textarea
        id="description"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
        v-model="props.form.description"
      ></textarea>
      <InputError class="mt-2" :message="props.form.errors.description" />
    </div>

    <div class="flex items-center gap-2 md:col-span-2">
      <input type="checkbox" id="is_active" v-model="props.form.is_active" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500" />
      <label for="is_active" class="block text-sm font-medium text-gray-900 dark:text-white">Is Active</label>
      <InputError class="mt-2" :message="props.form.errors.is_active" />
    </div>
  </div>
</template>
