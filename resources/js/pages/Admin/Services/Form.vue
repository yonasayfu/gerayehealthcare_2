<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

defineProps<{
  form: ReturnType<typeof useForm>;
}>();
</script>

<template>
  <div class="space-y-4">
    <div>
      <label for="name" class="block text-sm font-medium">Service Name</label>
      <input id="name" v-model="form.name" type="text" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required />
      <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
    </div>
    <div>
      <label for="price" class="block text-sm font-medium">Price ($)</label>
      <input id="price" v-model="form.price" type="number" step="0.01" min="0" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required />
      <div v-if="form.errors.price" class="text-sm text-red-600 mt-1">{{ form.errors.price }}</div>
    </div>
    <div>
      <label for="description" class="block text-sm font-medium">Description</label>
      <textarea id="description" v-model="form.description" rows="3" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
      <div v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</div>
    </div>
    <div class="flex items-center gap-4">
        <input id="is_active" v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
        <label for="is_active" class="block text-sm font-medium">Service is Active</label>
    </div>
  </div>
</template>