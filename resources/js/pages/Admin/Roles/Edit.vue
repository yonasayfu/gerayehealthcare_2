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
  // We extract just the names from the permissions array for the form
  permissions: (props.role.permissions || []).map(p => p.name),
});

const submit = () => {
  form.put(route('admin.roles.update', props.role.id));
};

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Role Management', href: route('admin.roles.index') },
  { title: 'Edit Role', href: route('admin.roles.edit', props.role.id) },
];
</script>
<template>
  <Head title="Edit Role" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Role</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update role information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.roles.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
