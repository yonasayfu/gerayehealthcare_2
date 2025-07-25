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
  permissions: props.role.permissions.map(p => p.name),
});

const submit = () => {
  form.put(route('admin.roles.update', props.role.id));
};
</script>
<template>
  <Head title="Edit Role" />
  <AppLayout>
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Role: {{ role.name }}</h1>
                <p class="text-sm text-muted-foreground">Update the role's name and assigned permissions.</p>
            </div>
        </div>

              <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
            <Form :form="form" :all-permissions="allPermissions" @submit="submit" />
            <div class="mt-6 flex justify-end space-x-4 border-t dark:border-gray-700 pt-6">
                <Link :href="route('admin.roles.index')" class="px-4 py-2 border rounded-md text-sm font-medium">Cancel</Link>
                <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Update Role' }}
                </button>
            </div>
        </div>
    </div>
  </AppLayout>
</template>

