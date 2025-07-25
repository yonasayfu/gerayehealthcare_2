<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Form from './Form.vue';

defineProps<{
  permissions: string[];
}>();

const form = useForm({
  name: '',
  permissions: [],
});

const submit = () => {
  form.post(route('admin.roles.store'));
};
</script>
<template>
  <Head title="Create Role" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-xl font-semibold mb-4">Create New Role</h1>
      <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" :all-permissions="permissions" @submit="submit" />
        <div class="mt-6 flex justify-end space-x-4">
          <Link :href="route('admin.roles.index')" class="px-4 py-2 border rounded-md">Cancel</Link>
          <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md">
            {{ form.processing ? 'Saving...' : 'Create Role' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
