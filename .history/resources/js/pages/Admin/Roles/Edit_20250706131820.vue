<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Form from './Form.vue';

const props = defineProps<{
  role: {
    id: number;
    name: string;
    permissions: Array<{ name: string }>;
  };
  allPermissions: string[];
}>();

const form = useForm({
  name: props.role.name,
  permissions: props.role.permissions.map(p => p.name),
});

const submit = () => {
  form.put(route('admin.roles.update', props.role.id));
};
</script>
<template>
  <Head title="Edit Role" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-xl font-semibold mb-4">Edit Role: {{ role.name }}</h1>
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <Form :form="form" :all-permissions="allPermissions" @submit="submit" />
        <div class="mt-6 flex justify-end space-x-4">
          <Link :href="route('admin.roles.index')" class="px-4 py-2 border rounded-md">Cancel</Link>
          <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md">
            {{ form.processing ? 'Saving...' : 'Update Role' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
